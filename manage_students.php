<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit;
}

echo "<h2>Manage Students Page - Placeholder</h2>";
// Add your student management functionality here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Admin Dashboard</a>
</body>
</html>
