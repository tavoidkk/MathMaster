<?php
function obtener_config_tipo($tipo) {
    if ($tipo === '2d') {
        return [
            'min' => 10,
            'max' => 99,
            'titulo' => 'Suma de 2 Dígitos',
            'descripcion' => 'Sumas de 2 dígitos'
        ];
    }

    if ($tipo === '6d2') {
        return [
            'min' => 100000,
            'max' => 999999,
            'titulo' => 'Suma de 6 Dígitos',
            'descripcion' => 'Sumas de 6 dígitos - Retos 9-16'
        ];
    }

    return [
        'min' => 100000,
        'max' => 999999,
        'titulo' => 'Suma de 6 Dígitos',
        'descripcion' => 'Sumas de 6 dígitos'
    ];
}

function generar_numeros($tipo) {
    $config = obtener_config_tipo($tipo);
    return [rand($config['min'], $config['max']), rand($config['min'], $config['max'])];
}

function obtener_ejercicio($pdo, $usuario_id, $tipo, $ejercicio_n) {
    $stmt = $pdo->prepare("SELECT * FROM progreso_ejercicios WHERE usuario_id = ? AND tipo = ? AND ejercicio_n = ? LIMIT 1");
    $stmt->execute([$usuario_id, $tipo, $ejercicio_n]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function guardar_ejercicio($pdo, $usuario_id, $tipo, $ejercicio_n, $num1, $num2, $estado) {
    $existente = obtener_ejercicio($pdo, $usuario_id, $tipo, $ejercicio_n);
    if ($existente) {
        $stmt = $pdo->prepare("UPDATE progreso_ejercicios SET num1 = ?, num2 = ?, estado = ? WHERE id = ?");
        $stmt->execute([$num1, $num2, $estado, $existente['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO progreso_ejercicios (usuario_id, ejercicio_n, tipo, num1, num2, estado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $ejercicio_n, $tipo, $num1, $num2, $estado]);
    }
}

function obtener_o_crear_ejercicio($pdo, $usuario_id, $tipo, $ejercicio_n) {
    $ejercicio = obtener_ejercicio($pdo, $usuario_id, $tipo, $ejercicio_n);
    if ($ejercicio && $ejercicio['num1'] !== null && $ejercicio['num2'] !== null) {
        return $ejercicio;
    }

    list($num1, $num2) = generar_numeros($tipo);
    $estado = $ejercicio ? $ejercicio['estado'] : 'pendiente';
    guardar_ejercicio($pdo, $usuario_id, $tipo, $ejercicio_n, $num1, $num2, $estado);

    return obtener_ejercicio($pdo, $usuario_id, $tipo, $ejercicio_n);
}

function asegurar_ejercicios($pdo, $usuario_id, $tipo, $cantidad = 8, $inicio = 1) {
    $stmt = $pdo->prepare("SELECT * FROM progreso_ejercicios WHERE usuario_id = ? AND tipo = ?");
    $stmt->execute([$usuario_id, $tipo]);
    $ejercicios = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $ejercicios[(int)$row['ejercicio_n']] = $row;
    }

    $fin = $inicio + $cantidad - 1;
    for ($i = $inicio; $i <= $fin; $i++) {
        if (!isset($ejercicios[$i]) || $ejercicios[$i]['num1'] === null || $ejercicios[$i]['num2'] === null) {
            list($num1, $num2) = generar_numeros($tipo);
            $estado = isset($ejercicios[$i]) ? $ejercicios[$i]['estado'] : 'pendiente';
            guardar_ejercicio($pdo, $usuario_id, $tipo, $i, $num1, $num2, $estado);
            $ejercicios[$i] = obtener_ejercicio($pdo, $usuario_id, $tipo, $i);
        }
    }

    return $ejercicios;
}

function reiniciar_ejercicios($pdo, $usuario_id, $tipo, $cantidad = 8, $solo_no_resueltos = false, $inicio = 1) {
    $stmt = $pdo->prepare("SELECT * FROM progreso_ejercicios WHERE usuario_id = ? AND tipo = ?");
    $stmt->execute([$usuario_id, $tipo]);
    $actuales = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $actuales[(int)$row['ejercicio_n']] = $row;
    }

    $fin = $inicio + $cantidad - 1;
    for ($i = $inicio; $i <= $fin; $i++) {
        if ($solo_no_resueltos && isset($actuales[$i]) && $actuales[$i]['estado'] === 'resuelto') {
            continue;
        }
        list($num1, $num2) = generar_numeros($tipo);
        guardar_ejercicio($pdo, $usuario_id, $tipo, $i, $num1, $num2, 'pendiente');
    }

    return asegurar_ejercicios($pdo, $usuario_id, $tipo, $cantidad, $inicio);
}
?>
