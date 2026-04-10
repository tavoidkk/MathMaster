<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
include 'ejercicios_lib.php';

$usuario_id = $_SESSION['usuario_id'];
$nombre_niño = $_SESSION['nombre'];
$tipo = '6d2';
$inicio = 9;
$fin = 16;
$config = obtener_config_tipo($tipo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    if ($accion === 'reiniciar_todo') {
        reiniciar_ejercicios($pdo, $usuario_id, $tipo, 8, false, $inicio);
    } elseif ($accion === 'reintentar') {
        reiniciar_ejercicios($pdo, $usuario_id, $tipo, 8, true, $inicio);
    }
}

$ejercicios = asegurar_ejercicios($pdo, $usuario_id, $tipo, 8, $inicio);
$stmt = $pdo->prepare("SELECT puntos_totales FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$puntos = (int)$stmt->fetchColumn();
$mensaje_incorrecto = isset($_GET['msg']) && $_GET['msg'] === 'incorrecto';
$reto_incorrecto = isset($_GET['ejercicio']) ? (int)$_GET['ejercicio'] : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Sumas - MathMaster 10</title>
    <style>
        @import url('https://fonts.cdnfonts.com/css/arial-rounded-mt-bold');
        
        body {
            font-family: 'Arial Rounded MT Bold', sans-serif;
            background: #f0f8ff;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .header {
            background: #4a90e2;
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .header-info {
            text-align: left;
            flex: 1;
        }

        .header-middle {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .puntos {
            background: #fff;
            color: #2c3e50;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 1rem;
            font-weight: bold;
            display: inline-block;
            margin-top: 8px;
        }

        .header-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: flex-end;
            flex: 1;
        }

        .acciones-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn-accion {
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            box-shadow: 0 5px 0 rgba(0,0,0,0.2);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }

        .btn-accion:active {
            transform: translateY(2px);
        }

        .btn-accion:hover {
            transform: translateY(-2px) scale(1.02);
            filter: brightness(1.05);
            box-shadow: 0 8px 0 rgba(0,0,0,0.2);
        }

        .btn-reiniciar {
            background: linear-gradient(135deg, #ff5252, #ff1744);
            color: white;
        }

        .btn-reintentar {
            background: linear-gradient(135deg, #ffd54f, #ffb300);
            color: #4e342e;
        }

        .nav-series {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 18px;
            max-width: 1000px;
            margin: 0 auto 20px;
        }

        .btn-nav {
            background: #4a90e2;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 999px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 5px 0 rgba(0,0,0,0.2);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }

        .btn-nav.activo {
            background: #2f6bb8;
        }

        .btn-nav:hover {
            transform: translateY(-2px) scale(1.02);
            filter: brightness(1.05);
            box-shadow: 0 8px 0 rgba(0,0,0,0.2);
        }

        .grid-ejercicios {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 columnas */
            gap: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .card {
            background: white;
            height: 170px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, background 0.3s;
            border: 4px solid transparent;
            position: relative;
        }

        .card:hover {
            transform: scale(1.05);
            border-color: #4a90e2;
        }

        .card.resuelto {
            background: #e8f5e9;
            border-color: #4caf50;
        }

        .card.incorrecto {
            background: #ffebee;
            border-color: #e53935;
        }

        .mini-suma {
            font-weight: bold;
            color: #333;
            text-align: center;
        }

        .mini-row {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }

        .mini-plus {
            width: 18px;
            color: #4a90e2;
            font-weight: bold;
        }

        .mini-plus.spacer {
            visibility: hidden;
        }

        .mini-line {
            width: 85%;
            border-bottom: 3px solid #4a90e2;
            margin: 4px auto 0;
        }

        .card-label {
            margin-top: 8px;
            color: #4a90e2;
            font-weight: bold;
        }

        .card-label.incorrecto {
            color: #d32f2f;
        }

        .alerta {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e53935;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .check {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            color: #4caf50;
        }

        .btn-logout {
            background: #ff5252;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 5px 0 rgba(0,0,0,0.2);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }

        .btn-logout:hover {
            transform: translateY(-2px) scale(1.02);
            filter: brightness(1.05);
            box-shadow: 0 8px 0 rgba(0,0,0,0.2);
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-backdrop.activo {
            opacity: 1;
            pointer-events: all;
        }

        .modal-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: min(420px, 90vw);
            text-align: center;
            animation: zoomIn 0.3s ease;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .modal-card h3 {
            margin: 0 0 10px;
            color: #2c3e50;
        }

        .modal-card button {
            margin-top: 20px;
            background: #4a90e2;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
        }

        .modal-actions {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .modal-btn {
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
            box-shadow: 0 5px 0 rgba(0,0,0,0.2);
        }

        .modal-btn:hover {
            transform: translateY(-2px) scale(1.02);
            filter: brightness(1.05);
            box-shadow: 0 8px 0 rgba(0,0,0,0.2);
        }

        .btn-confirmar {
            background: #ff1744;
            color: white;
        }

        .btn-cancelar {
            background: #cfd8dc;
            color: #37474f;
        }

        @keyframes zoomIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Contenedor del autor */
        .autor-container {
            margin: 0;
            display: flex;
            justify-content: center;
        }
        
        .autor-container h3 {
            background: white;
            padding: 15px 40px;
            border-radius: 50px;
            color: #2c3e50;
            font-size: 1.1rem;
            border: 2px solid #e0e6ed;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: inline-block;
            line-height: 1.4;
            transition: all 0.3s ease;
        }
        
        /* Efecto al pasar el mouse por el nombre */
        .autor-container h3:hover {
            transform: translateY(-5px);
            border-color: #4a90e2;
            box-shadow: 0 6px 20px rgba(74, 144, 226, 0.2);
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-info">
            <h1>¡Hola, <?php echo htmlspecialchars($nombre_niño); ?>! 🚀</h1>
            <div>MathMaster 10 - <?php echo $config['descripcion']; ?></div>
            <div class="puntos">⭐ Puntos: <?php echo $puntos; ?></div>
        </div>
        <div class="header-middle">
            <div class="autor-container">
                <h3>Realizado por:
                Gustavo Vidal 
                Jorge Jimenez</h3>
            </div>
        </div>
        <div class="header-actions">
            <div class="acciones-form">
                <form method="POST" id="formReiniciar">
                    <input type="hidden" name="accion" value="reiniciar_todo">
                    <button type="button" class="btn-accion btn-reiniciar" onclick="abrirConfirmReinicio()">Reiniciar todo</button>
                </form>
                <form method="POST">
                    <button class="btn-accion btn-reintentar" name="accion" value="reintentar">Volver a intentar</button>
                </form>
            </div>
            <a href="logout.php" class="btn-logout">Salir</a>
        </div>
    </div>

    <h2>Elige una suma para comenzar:</h2>

    <div class="nav-series">
        <a class="btn-nav" href="index.php">&larr; Retos 1-8</a>
        <a class="btn-nav activo" href="sumas_2dig.php">Retos 9-16 &rarr;</a>
    </div>

    <div class="grid-ejercicios" id="contenedor">
        <?php for($i=$inicio; $i<=$fin; $i++): ?>
            <?php
                $ejercicio = $ejercicios[$i];
                $esta_resuelto = $ejercicio['estado'] === 'resuelto';
                $suma_texto = $ejercicio['num1'] . " + " . $ejercicio['num2'];
            ?>
            
            <?php $esta_incorrecto = $ejercicio['estado'] === 'incorrecto'; ?>
            <div class="card <?php echo $esta_resuelto ? 'resuelto' : ($esta_incorrecto ? 'incorrecto' : ''); ?>" 
                 onclick="irAEjercicio(<?php echo $i; ?>, <?php echo $esta_resuelto ? 'true' : 'false'; ?>)">
                
                <?php if($esta_resuelto): ?>
                    <span class="check">✅</span>
                <?php endif; ?>
                <?php if($esta_incorrecto): ?>
                    <span class="alerta">!</span>
                <?php endif; ?>

                <div class="mini-suma">
                    <div class="mini-row">
                        <span class="mini-plus spacer">+</span>
                        <span><?php echo htmlspecialchars($ejercicio['num1']); ?></span>
                    </div>
                    <div class="mini-row">
                        <span class="mini-plus">+</span>
                        <span><?php echo htmlspecialchars($ejercicio['num2']); ?></span>
                    </div>
                    <div class="mini-line"></div>
                </div>
                <div class="card-label <?php echo $esta_incorrecto ? 'incorrecto' : ''; ?>">Reto #<?php echo $i; ?></div>
            </div>
        <?php endfor; ?>
    </div>
    
    <div class="modal-backdrop" id="modalInfo">
        <div class="modal-card">
            <h3 id="modalTitulo">¡Buen trabajo!</h3>
            <p id="modalMensaje"></p>
            <button onclick="cerrarModal()">Entendido</button>
        </div>
    </div>

    <div class="modal-backdrop" id="modalConfirm">
        <div class="modal-card">
            <h3>¿Reiniciar todas las sumas?</h3>
            <p>Se crearán nuevas sumas y se borrará el progreso de esta serie.</p>
            <div class="modal-actions">
                <button class="modal-btn btn-confirmar" onclick="confirmarReinicio()">Sí, reiniciar</button>
                <button class="modal-btn btn-cancelar" onclick="cerrarConfirmReinicio()">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        const avisoIncorrecto = <?php echo $mensaje_incorrecto ? 'true' : 'false'; ?>;
        const retoIncorrecto = <?php echo $reto_incorrecto; ?>;

        function irAEjercicio(numero, yaResuelto) {
            if(yaResuelto) {
                mostrarModal("¡Ya completaste este reto!", "Elige otra suma para seguir ganando puntos.");
                return;
            }
            window.location.href = "ejercicio.php?ejercicio=" + numero + "&tipo=6d2";
        }

        function mostrarModal(titulo, mensaje) {
            document.getElementById('modalTitulo').innerText = titulo;
            document.getElementById('modalMensaje').innerText = mensaje;
            document.getElementById('modalInfo').classList.add('activo');
        }

        function cerrarModal() {
            document.getElementById('modalInfo').classList.remove('activo');
        }

        if (avisoIncorrecto) {
            window.addEventListener('load', () => {
                const titulo = "Marcado para reintentar";
                const mensaje = retoIncorrecto ? "El reto #" + retoIncorrecto + " quedó incorrecto. ¡Puedes intentarlo de nuevo!" : "El reto quedó incorrecto. ¡Puedes intentarlo de nuevo!";
                mostrarModal(titulo, mensaje);
            });
        }

        function abrirConfirmReinicio() {
            document.getElementById('modalConfirm').classList.add('activo');
        }

        function cerrarConfirmReinicio() {
            document.getElementById('modalConfirm').classList.remove('activo');
        }

        function confirmarReinicio() {
            document.getElementById('formReiniciar').submit();
        }
    </script>

</body>
</html>
