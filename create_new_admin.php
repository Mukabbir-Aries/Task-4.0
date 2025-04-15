<?php
// Usage: Run this script to create a new admin user with hashed password

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'task_buddy_db';

if ($argc < 3) {
    echo "Usage: php create_new_admin.php <admin_name> <admin_password>\n";
    exit(1);
}

$admin_name = $argv[1];
$admin_password_plain = $argv[2];

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_password_hashed = password_hash($admin_password_plain, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (admin_name, password) VALUES (?, ?)");
$stmt->bind_param("ss", $admin_name, $admin_password_hashed);

if ($stmt->execute()) {
    echo "Admin user '$admin_name' created successfully.\n";
} else {
    echo "Error creating admin user: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>
