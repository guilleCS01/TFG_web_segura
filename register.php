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
        $_SESSION['error'] = "El nombre de usuario ya existe";
    } else {

        $password = $_POST['password'];
        if (!preg_match('/^(?=.*[0-9])(?=.*[A-Z])(?=.*[!@#$%^&*.,]).{8,}$/', $password)) {
            $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres,
             incluir un número, una letra mayúscula y un símbolo.";
        } else {

            $token = uniqid();


            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conexion->prepare("INSERT INTO users (username, password, token) VALUES (?, ?, ?)");
            $insertStmt->bind_param("sss", $username, $hashedPassword, $token);
            $info = password_get_info($hashedPassword);


            if ($insertStmt->execute()) {
                $_SESSION['success'] = "Cuenta creada!";
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
            <p><input id="username" name="username" type="text" placeholder="Usuario" /></p>
            <p><input id="password" name="password" type="password" placeholder="Contraseña" /></p>
            <p><input name="register" type="submit" value="Register" /></p><br><br>
        </form>
    </div>
    <div class="back">
        <h3><b><a href="login.php">Go Back</a></b></h3>
    </div>
</body>

</html>
