<?php
session_start();
include 'db.php';

if (isset($_SESSION['usuario_id'])) {
    $uid = $_SESSION['usuario_id'];
    // Sumamos un punto al usuario
    $stmt = $pdo->prepare("UPDATE usuarios SET puntos_totales = puntos_totales + 1 WHERE id = ?");
    $stmt->execute([$uid]);
    echo "Punto guardado";
}
?>