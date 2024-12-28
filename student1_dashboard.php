<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
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

$user_id = $_SESSION['user_id'];

// Check if the user has already created a team
$check_stmt = $conn->prepare("SELECT team_created FROM users WHERE id = ?");
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();
$user = $result->fetch_assoc();
$check_stmt->close();

if ($user['team_created']) {
    // User already has a team, redirect to team_created.php
    header("Location: team_created.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['students']) && count($_POST['students']) == 4 && isset($_POST['guide_id']) && isset($_POST['project_name'])) {
        $students = $_POST['students'];
        $guide_id = $_POST['guide_id'];
        $project_name = $_POST['project_name'];
        $studentDetails = [];

        // Fetch guide name based on guide ID
        $stmt = $conn->prepare("SELECT guide_name FROM guides WHERE id = ?");
        $stmt->bind_param("i", $guide_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $guide = $result->fetch_assoc();
        $guide_name = $guide['guide_name'];
        $stmt->close();

        foreach ($students as $studentId) {
            $stmt = $conn->prepare("SELECT studentname, enrollmentno, divisioncode FROM students WHERE id = ?");
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $studentDetails[] = $result->fetch_assoc();
            $stmt->close();
        }

        // Prepare and execute insert statement
        $stmt = $conn->prepare("INSERT INTO teams (student1_id, student1_name, student1_enrollmentno, student1_divisioncode,
                                                   student2_id, student2_name, student2_enrollmentno, student2_divisioncode,
                                                   student3_id, student3_name, student3_enrollmentno, student3_divisioncode,
                                                   student4_id, student4_name, student4_enrollmentno, student4_divisioncode,
                                                   guide_name, project_name, guide_id)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Check if prepare() succeeded
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
// bind parameters
        $stmt->bind_param("isssisssisssisssssi",
            $students[0], $studentDetails[0]['studentname'], $studentDetails[0]['enrollmentno'], $studentDetails[0]['divisioncode'],
            $students[1], $studentDetails[1]['studentname'], $studentDetails[1]['enrollmentno'], $studentDetails[1]['divisioncode'],
            $students[2], $studentDetails[2]['studentname'], $studentDetails[2]['enrollmentno'], $studentDetails[2]['divisioncode'],
            $students[3], $studentDetails[3]['studentname'], $studentDetails[3]['enrollmentno'], $studentDetails[3]['divisioncode'],
            $guide_name, $project_name, $guide_id);

        if ($stmt->execute()) {
            
              // Set the team number in the session
            $_SESSION['team_number'] = $conn->insert_id;
            // Update the user's team_created status
            $update_stmt = $conn->prepare("UPDATE users SET team_created = 1 WHERE id = ?");
            $update_stmt->bind_param("i", $user_id);
            $update_stmt->execute();
            $update_stmt->close();


            // Remove selected students from the list
            $placeholders = implode(',', array_fill(0, count($students), '?'));
            $delete_stmt = $conn->prepare("DELETE FROM students WHERE id IN ($placeholders)");
            $delete_stmt->bind_param(str_repeat('i', count($students)), ...$students);
            $delete_stmt->execute();
            $delete_stmt->close();

            header("Location: team_created.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Please select 4 students, a guide, and enter a project name.<br>";
    }
}

// Fetch students list
$result = $conn->query("SELECT id, enrollmentno, studentname, divisioncode, emailid FROM students");
$students_list = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students_list[] = $row;
    }
}

// Fetch available guides
$result = $conn->query("SELECT id, guide_name FROM guides");
$guides = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $guides[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 230px;
            overflow: auto;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content .checkbox {
            padding: 8px 16px;
        }

        .dropdown-content .checkbox input {
            margin-right: 10px;
        }

        .show {
            display: block;
        }

        .search-box {
            width: 100%;
            box-sizing: border-box;
            padding: 8px;
            border: none;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h2>Student Dashboard</h2>
    <form method="post">
        <label>Select 4 Students:</label>
        <div class="dropdown">
            <button type="button" class="dropbtn">Select Students</button>
            <div class="dropdown-content">
                <input type="text" class="search-box" placeholder="Search students..." onkeyup="filterFunction()">
                <?php foreach ($students_list as $student) : ?>
                    <div class="checkbox">
                        <input type="checkbox" id="student_<?php echo $student['id']; ?>" name="students[]" value="<?php echo $student['id']; ?>">
                        <label for="student_<?php echo $student['id']; ?>">
                            <?php echo  $student['enrollmentno'] . " - " . $student['divisioncode'] ; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <label for="guide_id">Select Guide:</label>
        <select name="guide_id" id="guide_id" required>
            <?php foreach ($guides as $guide) : ?>
                <option value="<?php echo $guide['id']; ?>"><?php echo $guide['guide_name']; ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" required>
        
        <button type="submit">Submit</button>
    </form>
    
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const dropbtn = document.querySelector('.dropbtn');
            const dropdownContent = document.querySelector('.dropdown-content');
            const searchBox = document.querySelector('.search-box');

            dropbtn.addEventListener('click', () => {
                dropdownContent.classList.toggle('show');
            });

            window.addEventListener('click', (e) => {
                if (!e.target.closest('.dropdown')) {
                    if (dropdownContent.classList.contains('show')) {
                        dropdownContent.classList.remove('show');
                    }
                }
            });

            searchBox.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });

        function filterFunction() {
            const input = document.querySelector('.search-box');
            const filter = input.value.toLowerCase();
            const checkboxes = document.querySelectorAll('.dropdown-content .checkbox');

            checkboxes.forEach((checkbox) => {
                const label = checkbox.querySelector('label');
                if (label.innerText.toLowerCase().indexOf(filter) > -1) {
                    checkbox.style.display = "";
                } else {
                    checkbox.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>
