<?php
$file = 'Xy7pRzQ2LbEaF9sG/messages.txt';

if (file_exists($file)) {
    $content = file_get_contents($file);
    $content = htmlspecialchars($content);
    $content = nl2br($content); 
    echo $content;
} else {
    echo 'No messages yet.';
}
?>
