<?php
session_start();

if (isset($_POST['login'])) {
    $HOST = 'localhost';
    $USER = 'root';
    $PASS = 'password';
    $NAME = 'web_users';
    $conexion = new mysqli($HOST, $USER, $PASS, $NAME);

    if ($conexion->connect_error) {
        die("There has been an error in the connection to the database try again later " . $conexion->connect_error);
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conexion->prepare("SELECT id, password, token FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashedPassword, $token);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['token'] = $token; 

            
            header("Location: dashboard.php?id=$id&token=$token");
            exit;
        } else {
            $_SESSION['error'] = "Incorrect password.";
        }
    } else {
        $_SESSION['error'] = "The user does not exist.";
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login_page.css">
</head>

<body>
    <div id="login-form-wrap">
        <h2>Login</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<i><p style="color:red;">' . $_SESSION['error'] . '</p></i>';
            unset($_SESSION['error']); 
        }
        ?>
        <form action="login.php" method="post" id="login-form">
            <p><input id="username" name="username" type="text" placeholder="Username" /></p>
            <p><input id="password" name="password" type="password" placeholder="Password" /></p>
            <p><input name="login" type="submit" value="Login" /></p>
        </form>
        <div id="create-account-wrap">
            <p>Not a member? <a href="register.php">Create Account</a></p>
        </div>
    </div>
    <div class="back">
        <h3><b><a href="index.html">Go Back</a></b></h3>
    </div>
</body>

</html>
