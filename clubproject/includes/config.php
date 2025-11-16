<?php
// config.php

// --- Error Reporting Setup ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // For AJAX requests, it's crucial to return a JSON response on failure.
    header('Content-Type: application/json');
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos. Por favor, contacta al administrador.'
    ]);
    exit;
}

// Define BASE_URL dinámicamente
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

// Ajusta la ruta para que apunte al directorio raíz del proyecto 'clubproject'
// Este ajuste asume que 'config.php' está en 'clubproject/includes'
$project_root = preg_replace('/\/includes\/$/', '/', $script_name);

// Si estás en un subdirectorio de 'clubproject' (ej. 'cultural'), necesitas ajustar la ruta
// Esta lógica asume que la estructura es consistente
if (strpos($_SERVER['REQUEST_URI'], '/cultural/') !== false || strpos($_SERVER['REQUEST_URI'], '/deportivo/') !== false || strpos($_SERVER['REQUEST_URI'], '/civil/') !== false || strpos($_SERVER['REQUEST_URI'], '/asesoria/') !== false) {
    // Sube un nivel si estás en una subcarpeta de club
    $project_root = preg_replace('/\/[a-zA-Z0-9_-]+\/$/', '/', $project_root);
}


define('BASE_URL', rtrim($protocol . $host . $project_root, '/'));

// helper para redirección simple
function redirect($url){
    header("Location: $url");
    exit;
}
