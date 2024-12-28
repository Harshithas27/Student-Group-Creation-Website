<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student' || !isset($_GET['team_number'])) {
    header("Location: login.html");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "Harsh123";
$dbname = "registration";

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$team_number = intval($_GET['team_number']);

if ($team_number === false) {
    die("Invalid team number");
}

$stmt = $conn->prepare("
    SELECT 
        project_name, guide_name, 
        student1_name, student1_enrollmentno, student1_divisioncode,
        student2_name, student2_enrollmentno, student2_divisioncode,
        student3_name, student3_enrollmentno, student3_divisioncode,
        student4_name, student4_enrollmentno, student4_divisioncode
    FROM teams 
    WHERE team_number = ?
");
$stmt->bind_param("i", $team_number);
$stmt->execute();
$result = $stmt->get_result();
$team = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Team</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>View Your Team</h2>
    <?php if ($team): ?>
        <table>
            <tr>
                <th>Team Number</th>
                <th>Project Name</th>
                <th>Guide Name</th>
                <th>Student Name</th>
                <th>Enrollment Number</th>
                <th>Division Code</th>
            </tr>
            <tr>
                <td rowspan="4"><?php echo htmlspecialchars($team_number); ?></td>
                <td rowspan="4"><?php echo htmlspecialchars($team['project_name']); ?></td>
                <td rowspan="4"><?php echo htmlspecialchars($team['guide_name']); ?></td>
                <td><?php echo htmlspecialchars($team['student1_name']); ?></td>
                <td><?php echo htmlspecialchars($team['student1_enrollmentno']); ?></td>
                <td><?php echo htmlspecialchars($team['student1_divisioncode']); ?></td>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($team['student2_name']); ?></td>
                <td><?php echo htmlspecialchars($team['student2_enrollmentno']); ?></td>
                <td><?php echo htmlspecialchars($team['student2_divisioncode']); ?></td>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($team['student3_name']); ?></td>
                <td><?php echo htmlspecialchars($team['student3_enrollmentno']); ?></td>
                <td><?php echo htmlspecialchars($team['student3_divisioncode']); ?></td>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($team['student4_name']); ?></td>
                <td><?php echo htmlspecialchars($team['student4_enrollmentno']); ?></td>
                <td><?php echo htmlspecialchars($team['student4_divisioncode']); ?></td>
            </tr>
        </table>
    <?php else: ?>
        <p>No team found. Please create a team first.</p>
    <?php endif; ?>
</body>
</html>
