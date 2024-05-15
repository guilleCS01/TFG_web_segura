<?php
$file = 'Xy7pRzQ2LbEaF9sG/messages.txt';

if (file_exists($file)) {
    $content = file_get_contents($file);
    $content = htmlspecialchars($content); // Escapar el contenido para prevenir XSS
    $content = nl2br($content); // Convertir saltos de línea a etiquetas <br>
    echo $content;
} else {
    echo 'No messages yet.';
}
?>
