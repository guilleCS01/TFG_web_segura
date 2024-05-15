<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $info = json_decode(file_get_contents('php://input'), true);
    if (isset($info['name']) && isset($info['message'])) {
        $msg = $info['name'] . ': ' . $info['message'] . PHP_EOL . PHP_EOL;
        file_put_contents('Xy7pRzQ2LbEaF9sG/messages.txt', $msg, FILE_APPEND | LOCK_EX);
    } else {
        http_response_code(400);
    }
} else {
    http_response_code(405);
}
?>
