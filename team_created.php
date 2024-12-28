<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.html");
    exit;
}

if (!isset($_SESSION['team_number'])) {
    header("Location: student1_dashboard.php");
    exit;
}

$team_number = $_SESSION['team_number'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Team Created</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding: 50px;
        }

        h2 {
            color: #4CAF50;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>YOUR TEAM HAS BEEN CREATED SUCCESSFULLY!!</h2>
    <button onclick="location.href='view_team1.php?team_number=<?php echo htmlspecialchars($team_number); ?>'">View Team</button>
    <p>You can now upload your abstract.</p>
    <a href="upload_abstract.php">Upload Abstract</a>
</body>
</html>
