<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "Harsh123";
$dbname = "registration";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 3307);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch team number from GET parameter
if (isset($_GET['team_number'])) {
    $team_number = $_GET['team_number'];
} else {
    die("Team number not provided");
}

// Fetch team details
$stmt = $conn->prepare("
    SELECT t.team_number, t.project_name, t.guide_name, 
           s.studentname, s.enrollmentno, s.divisioncode, s.emailid 
    FROM teams t
    LEFT JOIN students s ON (s.id = t.student1_id OR s.id = t.student2_id OR s.id = t.student3_id OR s.id = t.student4_id)
    WHERE t.team_number = ?
");
$stmt->bind_param("i", $team_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $team = $result->fetch_assoc();
} else {
    die("Team not found");
}
$stmt->close();

// Fetch abstracts for the team
$stmt = $conn->prepare("
    SELECT uploader_name, filename, uploaded_at
    FROM abstracts
    WHERE team_number = ?
");
$stmt->bind_param("i", $team_number);
$stmt->execute();
$abstracts_result = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Team Abstracts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            color: #4CAF50;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        
        th {
            background-color: #f2f2f2;
        }

        .btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Team Abstracts</h2>
        <h3>Team Number: <?php echo $team_number; ?></h3>
        <p>Project Name: <?php echo htmlspecialchars($team['project_name']); ?></p>
        <p>Guide Name: <?php echo htmlspecialchars($team['guide_name']); ?></p>

        <h3>Students</h3>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Enrollment No</th>
                <th>Division Code</th>
                <th>Email ID</th>
            </tr>
            <?php foreach ($team['students'] as $student) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['studentname']); ?></td>
                    <td><?php echo htmlspecialchars($student['enrollmentno']); ?></td>
                    <td><?php echo htmlspecialchars($student['divisioncode']); ?></td>
                    <td><?php echo htmlspecialchars($student['emailid']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Abstracts</h3>
        <?php if ($abstracts_result->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>Uploader Name</th>
                    <th>Filename</th>
                    <th>Uploaded At</th>
                </tr>
                <?php while ($abstract = $abstracts_result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($abstract['uploader_name']); ?></td>
                        <td><?php echo htmlspecialchars($abstract['filename']); ?></td>
                        <td><?php echo htmlspecialchars($abstract['uploaded_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>No abstracts uploaded for this team.</p>
        <?php endif; ?>
        
        <a href="faculty_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
