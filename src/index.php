<?php
$host = 'db';
$user = 'root';
$pass = 'rootpass';
$db   = 'studentsdb';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $conn->query("INSERT INTO students (name) VALUES ('$name')");
}

$result = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Student Records</title></head>
<body>
<h2>Student Records</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Enter student name" required>
    <button type="submit">Add</button>
</form>
<ul>
<?php while($row = $result->fetch_assoc()): ?>
    <li><?= $row['id'] ?>. <?= htmlspecialchars($row['name']) ?></li>
<?php endwhile; ?>
</ul>
</body>
</html>
