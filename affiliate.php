<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_data'])) {
    $HOST = 'localhost';
    $USER = 'root';
    $PASS = 'password';
    $NAME = 'web_users';

    $conexion = new mysqli($HOST, $USER, $PASS, $NAME);

    if ($conexion->connect_error) {
        die("There has been an error in the connection to the database try again later" . $conexion->connect_error);
    }

    $nombre = $_POST['fname'];
    $apellidos = $_POST['lname'];
    $direccion = $_POST['dir'];
    $correo = $_POST['correo'];

    // Verifica si se ha subido un archivo
    if (isset($_FILES["file"])) {
        $allowedExtensions = array("jpg", "jpeg", "png");
        $image_file = $apellidos . $_FILES["file"]["name"];
        $locate = "Xy7pRzQ2LbEaF9sG/" . $image_file;
        $fileExtension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions) &&
            $nombre != "" && $apellidos != "" && $direccion != "" && $correo != "") {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $locate)) {
                // Ahora procesa los datos en la base de datos
                $insertStmt = $conexion->prepare("INSERT INTO afiliados (dni, nombre, apellidos, direccion, correo) VALUES (?, ?, ?, ?, ?)");
                $insertStmt->bind_param("sssss", $image_file, $nombre, $apellidos, $direccion, $correo);

                if ($insertStmt->execute()) {
                    $message = "Proceso de afiliación aceptado";
                } else {
                    $message = '<i><p style="color:red;">'. "There has been a mistake in the entered data"  . $conexion->error . '</p></i>';
                }
                $insertStmt->close();
            } else {
                $message = '<i><p style="color:red;">'. "The picture has not been correctrly processed" . '</p></i>';
            }
        } else {
            $message = '<i><p style="color:red;">'. "Some fields are missing" . '</p></i>';
        }
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate</title>
    <link rel="stylesheet" type="text/css" href="affiliate_style.css">
</head>
<body>
    <div class="affiliate">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        Enter your personal infromation and apply to become a member of the organitation. With this you will receibe all the infromation regarding activities inside the organization and together we can change the world.<br><br>
            <label for="fname">Name:</label>
            <input type="text" id="fname" name="fname"><br><br>
            <label for="lname">Surname:</label>
            <input type="text" id="lname" name="lname"><br><br>
            <label for="dir">Address:</label>
            <input type="text" id="dir" name="dir"><br><br>
            <label for="correo">Email:</label>
            <input type="text" id="correo" name="correo"><br><br>

            <label for="dni_foto">ID Card picture:</label>
            <input type="file" name="file" />

            <input type="submit" name="submit_data" value="Send Application" />

            <?php
            if (!empty($message)) {
                echo '<p>' . $message . '</p>';
            }
            ?>
        </form>
    </div>
    <div class="back">
        <h3><b><a href="index.html">Go Back</a></b></h3>
    </div>
</body>
</html>