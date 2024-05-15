<?php
// Verifica si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene el contenido JSON de la solicitud y lo decodifica en un array asociativo
    $info = json_decode(file_get_contents('php://input'), true);

    // Verifica si se recibieron las claves 'name' y 'message' en el JSON
    if (isset($info['name']) && isset($info['message'])) {
        // Construye el mensaje concatenando el nombre y el mensaje con dos saltos de línea
        $msg = $info['name'] . ': ' . $info['message'] . PHP_EOL . PHP_EOL;
        
        // Guarda el mensaje en un archivo de texto, con bloqueo exclusivo para evitar escrituras simultáneas
        file_put_contents('Xy7pRzQ2LbEaF9sG/messages.txt', $msg, FILE_APPEND | LOCK_EX);
    } else {
        // Si faltan datos, devuelve un código de respuesta HTTP 400 (Bad Request)
        http_response_code(400);
    }
} else {
    // Si la solicitud no es de tipo POST, devuelve un código de respuesta HTTP 405 (Method Not Allowed)
    http_response_code(405);
}
?>
