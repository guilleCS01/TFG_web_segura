<?php
session_start();

$mysqli = new mysqli("localhost", "root", "password", "web_users");

if ($mysqli->connect_error) {
    die("There has been an error in the connection to the database try again later " . $mysqli->connect_error);
}

$userId = '';
$userToken = '';


if (isset($_GET['id']) && isset($_GET['token'])) {
    $userId = $_GET['id'];
    $userToken = $_GET['token'];

    $stmt = $mysqli->prepare("SELECT token, username FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($dbToken, $username);
        $stmt->fetch();


        if ($dbToken !== $userToken) {
            header("Location: login.php");
            exit();
        }
    } else {
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debate Forum</title>
    <link rel="stylesheet" type="text/css" href="forum.css">
</head>
<body>
    <header>
        <h1>Debate Forum</h1>
    </header>
    <div class="pregunta">
        <h2><b>Let us know you opinion about the 2024 climate summit</b></h2>
        <a href="https://www.theclimategroup.org/us-climate-action-summit-2024">Reference</a>
    </div>
    <div id="messages">
        <?php include 'getMessages.php'; ?>
    </div>

    <?php if(isset($_GET['id']) && isset($_GET['token'])) { ?>
    <div id="messageForm">
        <form>

            <div>
                <label for="name">Name: <?php echo htmlspecialchars($_SESSION['username']); ?></label>
                <?php if (!isset($_SESSION['username'])) { ?>
                    <input type="text" id="name" required>
                <?php } ?>
            </div>

            <div>
                <label for="message">Message:</label>
                <textarea id="message" placeholder="Escribe el mensaje" required><?php echo htmlspecialchars($message); ?></textarea>
                <div id="errorMessages" style="color: red;"></div>
            </div>

            <button type="button" onclick="postMessage()">Send</button>
        </form>
    </div>
    <?php } else { ?>
        <p style="text-align: center; color: red; font-style: italic;">You must login in the platform to be able to write in the forum</p>
    <?php } ?>

    <div class="back">
        <h3><b><a href="<?php echo isset($_SESSION['loggedin']) ? 'dashboard.php?id='.$_SESSION['id'].'&token='.$_SESSION['token'] : 'index.html'; ?>">Go Back</a></b></h3>
    </div>


    <script>
        
        function displayMessages() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var messagesDiv = document.getElementById('messages');
                    messagesDiv.innerHTML = xhr.responseText;
                }
            };
            xhr.open('GET', 'getMessages.php', true);
            xhr.send();
        }

        
        function postMessage() {
            var name = document.getElementById('name') ? document.getElementById('name').value : '<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>';
            var message = document.getElementById('message').value;
            var errorMessagesDiv = document.getElementById('errorMessages');
            errorMessagesDiv.innerHTML = ''; 

            
            if (message) {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4) {
                        if (xhr.status == 200) {
                            displayMessages();
                            document.getElementById('name').value = '';
                            document.getElementById('message').value = '';
                        } else {
                            errorMessagesDiv.innerHTML = 'Error al enviar el mensaje: ' + xhr.responseText;
                        }
                    }
                };
                xhr.open('POST', 'postMessage.php', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify({ name: name, message: message }));
            }
        }

     
        displayMessages();
    </script>
</body>
</html>
