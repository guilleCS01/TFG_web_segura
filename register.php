<?php

if (isset($_POST['register'])) {
    session_start();


    $HOST = 'localhost';
    $USER = 'root';
    $PASS = 'password';
    $NAME = 'web_users';
    $conexion = new mysqli($HOST, $USER, $PASS, $NAME);


    if ($mysqli->connect_error) {
        die("There has been an error in the connection to the database try again later " . $mysqli->connect_error);
    }


    $username = $_POST['username'];
    $check = $conexion->prepare("SELECT username FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();


    if ($check->num_rows > 0) {
        $_SESSION['error'] = "The username already existsss";
    } else {

        $password = $_POST['password'];
        if (!preg_match('/^(?=.*[0-9])(?=.*[A-Z])(?=.*[!@#$%^&*.,]).{8,}$/', $password)) {
            $_SESSION['error'] = "The password must be at least 8 characters long, include a number, an uppercase letter, and a symbol.";
        } else {

            $token = uniqid();


            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conexion->prepare("INSERT INTO users (username, password, token) VALUES (?, ?, ?)");
            $insertStmt->bind_param("sss", $username, $hashedPassword, $token);
            $info = password_get_info($hashedPassword);


            if ($insertStmt->execute()) {
                $_SESSION['success'] = "Account created!";
            } else {
                echo "Error: " . $insertStmt->error;
            }

            $insertStmt->close();
        }
    }


    $check->close();
    $conexion->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register_page.css">

</head>

<body>
    <div id="register-form-wrap">
        <h2>Sign Up</h2>
        <?php
  
        if (isset($_SESSION['error'])) {
            echo '<i><p style="color:red;">' . $_SESSION['error'] . '</p></i>';
            unset($_SESSION['error']); 
        } elseif (isset($_SESSION['success'])) {
            echo '<i><p style="color:blue;">' . $_SESSION['success'] . '</p></i>';
            unset($_SESSION['success']);
        }
        ?>
        <form action="register.php" method="post" id="register-form">
            <p><input id="username" name="username" type="text" placeholder="Username" /></p>
            <p><input id="password" name="password" type="password" placeholder="Password" /></p>
            <p><input name="register" type="submit" value="Register" /></p><br><br>
        </form>
    </div>
    <div class="back">
        <h3><b><a href="login.php">Go Back</a></b></h3>
    </div>
</body>

</html>
