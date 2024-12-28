<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password, role, team_created FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role, $team_created);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Password is correct
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;

            if (!preg_match("/@dsu\.edu\.in$/", $email)) {
                echo "Error: Only email addresses ending with @dsu.edu.in are allowed.";
                exit();
            }

            // Redirect based on role
            if ($role == 'student') {
                if ($team_created) {
                    header("Location: team_created.php");
                } else {
                    header("Location: student1_dashboard.php");
                }
            } elseif ($role == 'faculty') {
                header("Location: faculty_dashboard.php");
            } elseif ($role == 'admin') {
                header("Location: admin_dashboard.php");
            }
            exit();
        } else {
            echo "Error: Incorrect password.";
        }
    } else {
        echo "Error: No user found with that email address.";
    }

    $stmt->close();
    $conn->close();
}
?>
