<?php
session_start();
require 'config.php';  // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Debugging: Check if POST data is received
    echo "Email: $email<br>";
    echo "Password: $password<br>";
    echo "Role: $role<br>";

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT id, role, password, team_created FROM users WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $role, $hashed_password, $team_created);

    if ($stmt->fetch()) {
        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Store user information in session
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            $_SESSION['team_created'] = $team_created;

            // Debugging: Print session variables
            echo "User ID: " . $_SESSION['user_id'] . "<br>";
            echo "Session Role: " . $_SESSION['role'] . "<br>";
            echo "Team Created: " . $_SESSION['team_created'] . "<br>";

            // Redirect based on role and team_created status
            switch ($role) {
                case 'student':
                    if ($team_created) {
                        echo "Redirecting to Team Created page";
                        header("Location: team_created.php");
                    } else {
                        echo "Redirecting to Student Dashboard";
                        header("Location: student1_dashboard.php");
                    }
                    exit();
                case 'faculty':
                    echo "Redirecting to Faculty Dashboard";
                    header("Location: faculty_dashboard.php");
                    exit();
                case 'admin':
                    echo "Redirecting to Admin Dashboard";
                    header("Location: admin_dashboard.php");
                    exit();
                default:
                    echo "Invalid role!";
                    exit();
            }
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with this email!";
    }

    $stmt->close();
    $conn->close();
}
?>
