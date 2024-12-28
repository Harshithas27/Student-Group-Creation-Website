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

// Retrieve faculty members
$result = $conn->query("SELECT * FROM guides");
$guides = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $guides[] = $row;
    }
}

// Initialize teams array
$teams = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && !empty($_POST['id'])) {
    $id = $_POST['id'];

    // Fetch teams associated with the selected guide ID
    $stmt = $conn->prepare("
        SELECT t.team_number, t.project_name, t.guide_name, 
               s.studentname, s.enrollmentno, s.divisioncode, s.emailid 
        FROM teams t
        LEFT JOIN students s ON (s.id = t.student1_id OR s.id = t.student2_id OR s.id = t.student3_id OR s.id = t.student4_id)
        WHERE t.guide_id = ?
        ORDER BY t.team_number
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $team_number = $row['team_number'];
            if (!isset($teams[$team_number])) {
                $teams[$team_number] = [
                    'project_name' => $row['project_name'],
                    'guide_name' => $row['guide_name'],
                    'students' => []
                ];
            }
            $teams[$team_number]['students'][] = [
                'studentname' => $row['studentname'],
                'enrollmentno' => $row['enrollmentno'],
                'divisioncode' => $row['divisioncode'],
                'emailid' => $row['emailid']
            ];
        }
    }
    $stmt->close();
}

// Function to fetch abstracts for a team
function fetchAbstracts($team_number, $conn) {
    $stmt = $conn->prepare("
        SELECT uploader_name, filename, uploaded_at
        FROM abstracts
        WHERE team_number = ?
    ");
    $stmt->bind_param("i", $team_number);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $result;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Team View</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding: 20px;
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

        form {
            margin-bottom: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        <h2>Faculty Team View</h2>
        <form method="post">
            <label for="id">Select Faculty:</label>
            <select name="id" id="id">
                <option value="">-- SELECT --</option>
                <?php foreach ($guides as $guide) : ?>
                    <option value="<?php echo $guide['id']; ?>"><?php echo $guide['guide_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Show Teams</button>
        </form>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && !empty($_POST['id'])) : ?>
            <?php if (!empty($teams)) : ?>
                <h3>Teams for Selected Faculty</h3>
                <table>
                    <tr>
                        <th>Team Number</th>
                        <th>Project Name</th>
                        <th>Guide Name</th>
                        <th>Students</th>
                        <th>View Abstract</th>
                    </tr>
                    <?php foreach ($teams as $team_number => $team) : ?>
                        <tr>
                            <td><?php echo $team_number; ?></td>
                            <td><?php echo htmlspecialchars($team['project_name']); ?></td>
                            <td><?php echo htmlspecialchars($team['guide_name']); ?></td>
                            <td>
                                <ul>
                                    <?php foreach ($team['students'] as $student) : ?>
                                        <li><?php echo htmlspecialchars($student['studentname']) . " (" . htmlspecialchars($student['enrollmentno']) . ")"; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <form action="view_team_abstracts.php" method="get">
                                    <input type="hidden" name="team_number" value="<?php echo $team_number; ?>">
                                    <button type="submit" class="btn">View Abstract</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else : ?>
                <p>No teams found for the selected faculty.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
