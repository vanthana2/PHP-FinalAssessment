<?php
session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/register':
        include 'views/register.php';
        break;
    case '/login':
        include 'views/login.php';
        break;
    default:
        include 'views/login.php';
        break;
}
?>
