<?php
session_start();


if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

$mysqli = new mysqli("localhost", "root", "password", "web_users");

if ($mysqli->connect_error) {
    die("Se ha producido un error, intentalo de nuevo más tarde");
}


$loggedInUserId = $_SESSION['id'];

// Obtener el ID de usuario de la URL si está presente
if (isset($_GET['id'])) {
    $urlUserId = $_GET['id'];

    // Verificar que el ID de la URL coincida con el ID del usuario autenticado
    if ($urlUserId != $loggedInUserId) {
        
        header("Location: login.php");
        exit();
    }

    // Obtener el token asociado al usuario de la base de datos
    $stmt = $mysqli->prepare("SELECT token FROM users WHERE id = ?");
    $stmt->bind_param("i", $urlUserId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($userToken);
    $stmt->fetch();

    // Verificar si se obtuvo el token correctamente
    if ($stmt->num_rows > 0) {
        $_SESSION['token'] = $userToken; 
    } 

    $stmt->close(); 
} else {
    echo "ID de usuario no proporcionado.";
    exit();
}


$stmt = $mysqli->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $urlUserId);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($username);
$stmt->fetch();

// Funcionalidad para cerrar sesion
if (isset($_POST['logout'])) {
   
    $_SESSION = array();

    // Destruir la sesión
    session_destroy();


    header("Location: login.php");
    exit();
}

$mysqli->close(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <h1>Welcome back <?php echo $username; ?></h1>
    <div class="forum-button">
        <a href="changepass.php?id=<?php echo $urlUserId; ?>&token=<?php echo $_SESSION['token']; ?>"><button>Change password</button></a>
    </div>

    <div class="forum-button">
        <a href="forum.php?id=<?php echo $urlUserId; ?>&token=<?php echo $_SESSION['token']; ?>"><button>Go to Forum</button></a>
    </div>

    <form action="" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

    <div class="back">
        <h3><b><a href="login.php">Go Back</a></b></h3>
    </div>
</body>
</html>