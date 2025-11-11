<?php
// config.php
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params(['path' => '/']);
    session_start();
}

$DB_HOST = '127.0.0.1';
$DB_NAME = 'clubs_db';
$DB_USER = 'root';
$DB_PASS = ''; // cambiar si tienes contraseña

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die('DB error: ' . $e->getMessage());
}

define('BASE_URL', '/proyecto/educlubs/clubproject');

// helper para redirección simple
function redirect($url){
    header("Location: $url");
    exit;
}
