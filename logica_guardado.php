<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

// Leemos los datos enviados por el JavaScript
$data = file_get_contents('php://input');
$request = json_decode($data, true);

if (isset($_SESSION['usuario_id']) && isset($request['ejercicio_n']) && isset($request['tipo'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $ejercicio_n = $request['ejercicio_n'];
    $tipo = $request['tipo'];
    if (!in_array($tipo, ['6d', '6d2'], true)) {
        $tipo = '6d';
    }

    $accion = $request['accion'] ?? 'resolver';

    try {
        $stmt = $pdo->prepare("SELECT id, estado FROM progreso_ejercicios WHERE usuario_id = ? AND tipo = ? AND ejercicio_n = ? LIMIT 1");
        $stmt->execute([$usuario_id, $tipo, $ejercicio_n]);
        $ejercicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ejercicio) {
            echo json_encode(['status' => 'error', 'message' => 'Ejercicio no encontrado.']);
            exit;
        }

        if ($ejercicio['estado'] === 'resuelto') {
            echo json_encode(['status' => 'success', 'message' => '¡Ya estaba resuelto!']);
            exit;
        }

        if ($accion === 'incorrecto') {
            $upd = $pdo->prepare("UPDATE progreso_ejercicios SET estado = 'incorrecto' WHERE id = ?");
            $upd->execute([$ejercicio['id']]);
            echo json_encode(['status' => 'success', 'message' => 'Marcado para reintento.']);
            exit;
        }

        $pdo->beginTransaction();
        $upd = $pdo->prepare("UPDATE progreso_ejercicios SET estado = 'resuelto' WHERE id = ?");
        $upd->execute([$ejercicio['id']]);

        $puntos = $pdo->prepare("UPDATE usuarios SET puntos_totales = puntos_totales + 1 WHERE id = ?");
        $puntos->execute([$usuario_id]);
        $pdo->commit();

        echo json_encode(['status' => 'success', 'message' => '¡Progreso guardado!']);
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
}
