<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "Harsh123";
$dbname = "registration";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch list of students
$result = $conn->query("SELECT id, enrollmentno, studentname, divisioncode, emailid FROM students");
$students = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Fetch teams and their associated students
$teams_result = $conn->query("
    SELECT 
        t.team_number, t.project_name, g.guide_name,
        s1.studentname as student1_name, s1.enrollmentno as student1_enrollmentno, s1.divisioncode as student1_divisioncode,
        s2.studentname as student2_name, s2.enrollmentno as student2_enrollmentno, s2.divisioncode as student2_divisioncode,
        s3.studentname as student3_name, s3.enrollmentno as student3_enrollmentno, s3.divisioncode as student3_divisioncode,
        s4.studentname as student4_name, s4.enrollmentno as student4_enrollmentno, s4.divisioncode as student4_divisioncode
    FROM 
        teams t
    LEFT JOIN students s1 ON t.student1_id = s1.id
    LEFT JOIN students s2 ON t.student2_id = s2.id
    LEFT JOIN students s3 ON t.student3_id = s3.id
    LEFT JOIN students s4 ON t.student4_id = s4.id
    LEFT JOIN guides g ON t.guide_id = g.id
");
$teams = [];

if ($teams_result->num_rows > 0) {
    while ($row = $teams_result->fetch_assoc()) {
        $teams[] = $row;
    }
}

// Determine which section to display
$displaySection = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['show_teams'])) {
        $displaySection = 'teams';
    } elseif (isset($_POST['show_remaining_students'])) {
        $displaySection = 'remaining_students';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        
        th {
            background-color: #f2f2f2;
        }

        .button-container {
            margin-bottom: 20px;
        }

        .button-container form {
            display: inline-block;
        }

        .button-container button {
            padding: 10px 20px;
            font-size: 16px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h2>Admin Dashboard</h2>

    <div class="button-container">
        <form method="post">
            <button type="submit" name="show_teams">Created Teams</button>
            <button type="submit" name="show_remaining_students">Remaining Students</button>
        </form>
    </div>

    <?php if ($displaySection == 'teams') : ?>
        <h3>Teams and their Students</h3>
        <?php if (count($teams) > 0): ?>
            <?php foreach ($teams as $team): ?>
                <h4>Team Number: <?php echo htmlspecialchars($team['team_number']); ?></h4>
                <p>Project Name: <?php echo htmlspecialchars($team['project_name']); ?></p>
                <p>Guide Name: <?php echo htmlspecialchars($team['guide_name']); ?></p>
                <table>
                    <tr>
                        <th>Student Name</th>
                        <th>Enrollment No</th>
                        <th>Division Code</th>
                    </tr>
                    <?php if (!empty($team['student1_name'])): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($team['student1_name']); ?></td>
                        <td><?php echo htmlspecialchars($team['student1_enrollmentno']); ?></td>
                        <td><?php echo htmlspecialchars($team['student1_divisioncode']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($team['student2_name'])): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($team['student2_name']); ?></td>
                        <td><?php echo htmlspecialchars($team['student2_enrollmentno']); ?></td>
                        <td><?php echo htmlspecialchars($team['student2_divisioncode']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($team['student3_name'])): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($team['student3_name']); ?></td>
                        <td><?php echo htmlspecialchars($team['student3_enrollmentno']); ?></td>
                        <td><?php echo htmlspecialchars($team['student3_divisioncode']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($team['student4_name'])): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($team['student4_name']); ?></td>
                        <td><?php echo htmlspecialchars($team['student4_enrollmentno']); ?></td>
                        <td><?php echo htmlspecialchars($team['student4_divisioncode']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No teams found.</p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($displaySection == 'remaining_students') : ?>
        <h3>List of Remaining Students</h3>
        <table>
            <tr>
                <th>Enrollment No</th>
                <th>Student Name</th>
                <th>Division Code</th>
                <th>Email ID</th>
            </tr>
            <?php 
            $remaining_students = array_filter($students, function($student) use ($teams) {
                foreach ($teams as $team) {
                    if ($student['enrollmentno'] == $team['student1_enrollmentno'] || 
                        $student['enrollmentno'] == $team['student2_enrollmentno'] || 
                        $student['enrollmentno'] == $team['student3_enrollmentno'] || 
                        $student['enrollmentno'] == $team['student4_enrollmentno']) {
                        return false;
                    }
                }
                return true;
            });

            foreach ($remaining_students as $student) : ?>
            <tr>
                <td><?php echo htmlspecialchars($student['enrollmentno']); ?></td>
                <td><?php echo htmlspecialchars($student['studentname']); ?></td>
                <td><?php echo htmlspecialchars($student['divisioncode']); ?></td>
                <td><?php echo htmlspecialchars($student['emailid']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h3>Admin Management Options</h3>
    <ul>
        <li><a href="manage_students.php">Manage Students</a></li>
        <li><a href="manage_guides.php">Manage Guides</a></li>
        <li><a href="manage_teams.php">Manage Teams</a></li>
        <li><a href="view_reports.php">View Reports</a></li>
        <!-- Add more management options as needed -->
    </ul>
</body>
</html>
