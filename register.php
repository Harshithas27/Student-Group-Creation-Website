<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];


    // Validate email domain
    if (!preg_match("/@dsu\.edu\.in$/", $email)) {
        echo "Error: Only email addresses ending with @dsu.edu.in are allowed.";
    } elseif ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashed_password, $role);

        if ($stmt->execute()) {
            echo " ";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        button{
            border-radius: 20px 20px 20px 20px ;
            text-transform:uppercase;
            width: 200px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            border: 1px solid #010100;
            padding: 15px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            display:inline-block;
            background-color:HEX
 #2E86C1 
RGB
 46, 134, 193 
HSL
 204, 62%, 47%;
            border-radius: 20px 20px 20px 20px;
        }
        body{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 100vh;
            background-color:rgb(125 211 252);
             
        }
        h2{
            text-align: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 50px;
            font-style: oblique;
            text-decoration-color: HEX
 #2E86C1 
RGB
 46, 134, 193 
HSL
 204, 62%, 47%;;
        }
        .container{
            align-items:center ;
        }
    </style>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="mainpagestyles.css">
</head>
<body>
    <div class="container">
		<h2>Registered Succesfully!</h2>
        <button type="button" class="button"><a href="login.html">Login here!</a></button>          
    </div>
</body>
</html>



