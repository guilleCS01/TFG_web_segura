<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php"); 
    exit();
}

$mysqli = new mysqli("localhost", "root", "password", "web_users");

if ($mysqli->connect_error) {
    die("There has been an error in the connection to the database try again later");
}


if (isset($_GET['id']) && isset($_GET['token'])) {
    $userId = $_GET['id'];
    $token = $_GET['token'];

    $stmt = $mysqli->prepare("SELECT token FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($dbToken);
        $stmt->fetch();


        if ($token != $dbToken) {
            header("Location: login.php");
            exit();
        }
    } else {
        header("Location: login.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['changePassword'])) {
        $password = $_POST['newPassword'];
        if (!preg_match('/^(?=.*[0-9])(?=.*[A-Z])(?=.*[!@#$%^&*.,]).{8,}$/', $password)) {
            $_SESSION['error'] = "The password must be at least 8 characters long, include a number, a capital letter and a symbol.";
        } 
        else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();
            $stmt->close();
            $_SESSION['success'] = "Password changed successfully";

            header("Location: changepass.php?id=$userId&token=$token&success=1");
            exit();
        }
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <h1>Change Password</h1>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<i><p style="color:red; text-align: center;">' . $_SESSION['error'] . '</p></i>';
        unset($_SESSION['error']);
    } elseif (isset($_SESSION['success'])) {
        echo '<i><p style="color:blue; text-align: center;">' . $_SESSION['success'] . '</p></i>';
        unset($_SESSION['success']); 
    }
    ?>
    <form action="changepass.php?id=<?php echo $userId; ?>&token=<?php echo $token; ?>" method="post">
        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword" required>
        <input type="submit" name="changePassword" value="Change Password">
    </form>
    <div class="back">
        <h3><b><a href="dashboard.php?id=<?php echo $userId; ?>">Go Back</a></b></h3>
    </div>
</body>
</html>
