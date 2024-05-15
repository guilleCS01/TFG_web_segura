<?php

session_start();

// Definir el número máximo de intentos fallidos y el tiempo de bloqueo (en segundos)
$max_attempts = 4;
$block_duration = 5; // 2 minutos

if (isset($_POST['login'])) {
    // Verificar si el usuario ha excedido el número máximo de intentos
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
        // Verificar si ha pasado el tiempo de bloqueo
        if (isset($_SESSION['last_attempt_time']) && (time() - $_SESSION['last_attempt_time']) < $block_duration) {
            $remaining_time = $block_duration - (time() - $_SESSION['last_attempt_time']);
            $_SESSION['error'] = "The maximum number of login attempts has been exceeded. Please wait $remaining_time seconds before trying again.";
            header("Location: login.php");
            exit;
        } else {
            // Reiniciar el contador de intentos fallidos y actualizar el tiempo del último intento
            $_SESSION['login_attempts'] = 0;
            unset($_SESSION['last_attempt_time']);
        }
    }

    $HOST = 'localhost';
    $USER = 'root';
    $PASS = 'password';
    $NAME = 'web_users';
    $connect = mysqli_connect($HOST, $USER, $PASS, $NAME);

    if ($connect->connect_error) {
        die("There was an error connecting to the database. Please try again later " . $connect->connect_error);
    }

    $stmt = $connect->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt->execute();
    $stmt->store_result();

    // Verificar si existe el usuario
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashedPassword);
        $stmt->fetch();

        // Comparar la contraseña
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;

            // Reiniciar el contador de intentos fallidos y actualizar el tiempo del último intento después de un inicio de sesión exitoso
            $_SESSION['login_attempts'] = 0;
            unset($_SESSION['last_attempt_time']);

            header("Location: dashboard.php?id=$id");
            exit;
        } else {
            // Incrementar el contador de intentos fallidos y registrar el tiempo del último intento
            $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;
            $_SESSION['last_attempt_time'] = time();
            $remaining_attempts = $max_attempts - $_SESSION['login_attempts'] + 1;
            $_SESSION['error'] = "Incorrect password. You have $remaining_attempts attempts left.";
            if ($remaining_attempts == 1) {
                $_SESSION['error'] = "Incorrect password. You have $remaining_attempts attempt left.";
            }
        }
    } else {
        $_SESSION['error'] = "The user does not exist.";
    }

    $stmt->close();
    $connect->close();
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
            <p><input id="username" name="username" type="text" placeholder="Usuario" /></p>
            <p><input id="password" name="password" type="password" placeholder="Contraseña" /></p>
            <p><input name="login" type="submit" value="Login" /></p>
        </form>
        <div id="create-account-wrap">
            <p>Not a member? <a href="register.php">Create Account</a>
            <p>
        </div>
    </div>
    <div class="back">
        <h3><b><a href="index.html">Go Back</a></b></h3>
    </div>
</body>

</html>