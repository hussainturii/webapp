# Student Records — CSC418 Assignment 2

Hussain · SP23-BAI-057 · COMSATS Islamabad

A PHP application stores student names in MariaDB. Both deployments use persistent database volumes.

## Part I

The Dockerfile packages the PHP application and MySQL extensions. The published image is `hussainturii/webapp:latest`.

On EC2, `/home/ubuntu/webapp/docker-compose.yml` runs this image on host port 8081 and MariaDB on host port 3307. The existing external volume `webapp_db_data` preserves student records.

```sh
docker compose up -d
```

Application: http://13.212.120.21:8081

For a fresh machine, create the named volume first: `docker volume create webapp_db_data`.

## Part II

`compose.pipeline.yml` mounts `./src` into the PHP container instead of building a new application image. It uses port 8082, database port 3308, distinct container names, and its own database volume.

The Jenkins pipeline checks out the pushed revision, validates PHP inside Docker, starts the deployment, checks its HTTP response, and emails the build result to `pusher.email` from the GitHub push payload. A setup build without a push event sends no email.

Required Jenkins plugins: Git, Pipeline, Docker Pipeline, Email Extension, and Generic Webhook Trigger.

Configure a Pipeline from SCM job for this repository, branch `main`, script `Jenkinsfile`. Add a secret-text credential named `webapp-webhook-token`. The GitHub webhook sends `application/json` push events to Jenkins' `/generic-webhook-trigger/invoke` endpoint with that token. Keep the webhook token private.

Configure Extended E-mail Notification with Gmail SMTP (`smtp.gmail.com`, port 465, SSL), sender `h0essainr@gmail.com`, and a Gmail app password stored in Jenkins credentials. Never commit the app password.

After testing, stop only Part II from its Jenkins workspace:

```sh
docker compose -f compose.pipeline.yml down
```

Leave Part I running for evaluation. A subsequent push to `main` starts Part II again.

## Submission checklist

- Verify adding a name and preserving it after container recreation.
- Verify a real GitHub push deploys Part II within three minutes.
- Verify its pusher receives the build result within five minutes.
- Invite `qasimalik@gmail.com` to collaborate on the GitHub repository.
- Include screenshots, configuration files, and steps in the report.
- Submit repository URL, image name, and EC2 IP in the assignment form.

The webhook, email configuration, collaborator invitation, and timing checks must be completed and verified before submission.
