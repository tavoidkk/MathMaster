<?php
// db.php - Conexión segura a phpMyAdmin

$host = 'localhost'; // Dentro del servidor se usa localhost
$db   = 'grup_grupo10proyecto'; // Nombre de tu bdd 
$user = 'root'; // Nombre de usuaro de tu bdd
$pass = ''; // Contraseña de tu bdd
$port = '3306'; // Puerto interno de MySQL (diferente al 8090 del panel)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>