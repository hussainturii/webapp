pipeline {
    agent any
    options {
        skipDefaultCheckout(true)
        disableConcurrentBuilds()
        timestamps()
        buildDiscarder(logRotator(numToKeepStr: '10'))
        timeout(time: 3, unit: 'MINUTES')
    }
    triggers {
        GenericTrigger(
            genericVariables: [
                [key: 'PUSHER_EMAIL', value: '$.pusher.email'],
                [key: 'PUSHER_LOGIN', value: '$.sender.login'],
                [key: 'PUSH_REF', value: '$.ref'],
                [key: 'PUSH_REPO', value: '$.repository.full_name'],
                [key: 'PUSH_SHA', value: '$.after']
            ],
            tokenCredentialId: 'webapp-webhook-token',
            causeString: 'GitHub push by $PUSHER_LOGIN',
            printContributedVariables: false,
            printPostContent: false,
            regexpFilterText: '$PUSH_REPO:$PUSH_REF',
            regexpFilterExpression: '^hussainturii/webapp:refs/heads/main$'
        )
    }
    stages {
        stage('Checkout') {
            steps {
                checkout scm
                script {
                    if (env.PUSH_SHA?.trim()) {
                        if (!(env.PUSH_SHA ==~ /[a-f0-9]{40}/)) {
                            error('Invalid push revision')
                        }
                        sh 'git checkout --detach "$PUSH_SHA"'
                    }
                }
            }
        }
        stage('Validate PHP in Docker') {
            steps {
                script {
                    docker.image('hussainturii/webapp:latest').inside('--entrypoint=') {
                        sh 'php -l src/index.php'
                    }
                }
                sh 'docker compose -f compose.pipeline.yml config --quiet'
            }
        }
        stage('Deploy') {
            steps {
                sh 'docker compose -f compose.pipeline.yml up -d --wait --wait-timeout 90'
                sh '''
                    for attempt in 1 2 3 4 5 6 7 8 9 10; do
                        if curl -fsS http://localhost:8082 | grep -q 'Student Records'; then
                            exit 0
                        fi
                        sleep 2
                    done
                    exit 1
                '''
            }
        }
    }
    post {
        always {
            script {
                def recipient = env.PUSHER_EMAIL?.trim()
                if (recipient && recipient ==~ /[A-Za-z0-9.!#$%&'*+\/=?^_`{|}~-]+@[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)+/) {
                    emailext(
                        to: recipient,
                        from: 'h0essainr@gmail.com',
                        subject: "${currentBuild.currentResult}: webapp #${env.BUILD_NUMBER}",
                        mimeType: 'text/plain',
                        body: """GitHub push by ${env.PUSHER_LOGIN}
Build result: ${currentBuild.currentResult}
Commit: ${env.PUSH_SHA}
Build: ${env.BUILD_URL}
Application: http://13.212.120.21:8082
"""
                    )
                } else {
                    echo 'No valid pusher email supplied. Manual setup builds do not send mail.'
                }
            }
        }
    }
}
