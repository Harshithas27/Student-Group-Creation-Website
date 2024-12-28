<?php
session_start();

// Check if the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.html");
    exit;
}

// Check if the team number is set in the session
if (!isset($_SESSION['team_number'])) {
    header("Location: student1_dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);

        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf");

        if (!in_array($file_type, $allowed_types)) {
            echo "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $uploader_name = $_POST["uploaderName"];
                $team_number = $_POST["teamNumber"];

                $db_host = "localhost";
                $db_user = "root";
                $db_pass = "Harsh123";
                $db_name = "registration";

                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, 3307);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = $conn->prepare("INSERT INTO abstracts (uploader_name, team_number, filename, filesize, filetype) VALUES (?, ?, ?, ?, ?)");

                $filesize = $_FILES["file"]["size"];

                $sql->bind_param("sssis", $uploader_name, $team_number, $target_file, $filesize, $file_type);

                if ($sql->execute()) {
                    echo "The file " . basename($_FILES["file"]["name"]) . " has been uploaded and the information has been stored in the database.";
                } else {
                    echo "Sorry, there was an error uploading your file and storing information in the database: " . $conn->error;
                }

                $sql->close();
                $conn->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "No file was uploaded.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .container3{
    font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    border:100vh;
    border: 2px solid;
    border:double;
    align-items: center;
    border-radius: 10px;
    padding-left: 10px;
    padding: auto;
    padding-right: 20px;
    background-color: white;
}
</style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container3">
        <img src="https://tse1.mm.bing.net/th?id=OIP.0GDlrf9HU_XyVItdyNrngQHaCS&pid=Api&P=0&h=180" alt="Logo" width="300" height="150" class="logo">
        <h2>Upload a File</h2>
        <form action="upload_abstract.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label for="uploaderName">Enter Your Name:</label>
                <input type="text" name="uploaderName" id="uploaderName" required>
            </div>
            <div class="input-group">
                <label for="teamNumber">Team Number:</label>
                <input type="text" name="teamNumber" id="teamNumber" required>
            </div>
            <div class="input-group">
                <label for="file">Select File:</label>
                <input type="file" name="file" id="file" required>
            </div>
            <button type="submit" class="btn">Upload File</button>
        </form>
    </div>
</body>
</html>
