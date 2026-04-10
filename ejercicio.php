<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
include 'ejercicios_lib.php';

$usuario_id = $_SESSION['usuario_id'];
$tipo = '6d';
if (isset($_GET['tipo'])) {
    if ($_GET['tipo'] === '6d2') {
        $tipo = '6d2';
    } elseif ($_GET['tipo'] === '2d') {
        $tipo = '2d';
    }
}
$ejercicio_n = isset($_GET['ejercicio']) ? (int)$_GET['ejercicio'] : 1;
$min_ej = $tipo === '6d2' ? 9 : 1;
$max_ej = $tipo === '6d2' ? 16 : 8;
if ($ejercicio_n < $min_ej || $ejercicio_n > $max_ej) {
    $ejercicio_n = $min_ej;
}

$config = obtener_config_tipo($tipo);
$ejercicio = obtener_o_crear_ejercicio($pdo, $usuario_id, $tipo, $ejercicio_n);

$num1 = (int)$ejercicio['num1'];
$num2 = (int)$ejercicio['num2'];
$total = $num1 + $num2;

$stringTotal = (string)$total;
$columnas = strlen($stringTotal);

// Alineación
$num1_arr = str_split(str_pad((string)$num1, $columnas, " ", STR_PAD_LEFT));
$num2_arr = str_split(str_pad((string)$num2, $columnas, " ", STR_PAD_LEFT));

$fichas = str_split($stringTotal);
shuffle($fichas);
$volver_url = $tipo === '6d2' ? 'sumas_2dig.php' : 'index.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $config['titulo']; ?> - MathMaster 10</title>
    <style>
        @import url('https://fonts.cdnfonts.com/css/arial-rounded-mt-bold');
        body { font-family: 'Arial Rounded MT Bold', sans-serif; background: #f4f9ff; display: flex; flex-direction: column; align-items: center; padding: 20px; }
        
        .pizarra { background: white; padding: 40px; border-radius: 30px; border: 6px solid #4a90e2; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        .tabla-suma { border-collapse: separate; border-spacing: 10px; }
        .celda { width: 70px; height: 80px; font-size: 4rem; text-align: center; color: #333; }
        .linea-final { border-bottom: 6px solid #333; }
        
        /* ZONA DE SOLTAR MEJORADA */
        .drop-zone { 
            width: 75px; 
            height: 90px; 
            border: 3px dashed #bbb; 
            border-radius: 12px; 
            background: #fafafa;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .drop-zone.hover { background: #e1f0ff; border-color: #4a90e2; }
        .drop-zone.swap { animation: swapPulse 0.2s ease; }

        /* FICHAS */
        .ficha { 
            width: 70px; 
            height: 70px; 
            background: #ffcc00; 
            border-radius: 12px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            font-size: 3rem; 
            cursor: grab; 
            box-shadow: 0 5px 0 #ccaa00;
            z-index: 10;
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }

        .ficha.dragging {
            opacity: 0.6;
            transform: scale(1.08) rotate(2deg);
            box-shadow: 0 10px 15px rgba(0,0,0,0.2);
        }

        .acciones {
            margin-top: 30px;
        }

        .btn-verificar {
            padding: 15px 30px;
            font-size: 1.5rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-family: 'Arial Rounded MT Bold';
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
            z-index: 100;
        }

        .modal-backdrop.activo {
            opacity: 1;
            pointer-events: all;
        }

        .modal-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: min(440px, 90vw);
            text-align: center;
            animation: zoomIn 0.3s ease;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .modal-card h3 {
            margin: 0 0 10px;
            color: #2c3e50;
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
            padding: 12px 24px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
        }

        .btn-reintentar {
            background: #ff8a65;
            color: white;
            box-shadow: 0 6px 0 #e76c4d;
        }

        .btn-panel {
            background: #4a90e2;
            color: white;
        }

        @keyframes zoomIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes swapPulse {
            from { transform: scale(1); }
            to { transform: scale(1.04); }
        }
    </style>
</head>
<body>
        <div style="width: 100%; max-width: 800px; display: flex; justify-content: flex-start; margin-bottom: 20px;">
        <a href="<?php echo $volver_url; ?>" style="text-decoration: none; background-color: #95a5a6; color: white; padding: 10px 20px; border-radius: 10px; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
        ⬅️ Volver a los retos
         </a>
        </div>
    <h1><?php echo $config['titulo']; ?> - MathMaster 10</h1>

    <div class="pizarra">
        <table class="tabla-suma">
            <tr>
                <td></td>
                <?php foreach($num1_arr as $d): ?>
                    <td class="celda"><?= htmlspecialchars($d) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td class="celda" style="color:#4a90e2">+</td>
                <?php foreach($num2_arr as $d): ?>
                    <td class="celda linea-final"><?= htmlspecialchars($d) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td></td>
                <?php for($i=0; $i < $columnas; $i++): ?>
                    <td>
                        <div class="drop-zone" id="zona-<?= $i ?>" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="leave(event)">
                            <div class="ficha" id="f-<?= $i ?>" draggable="true" ondragstart="drag(event)">
                                <?= htmlspecialchars($fichas[$i]) ?>
                            </div>
                        </div>
                    </td>
                <?php endfor; ?>
            </tr>
        </table>
    </div>
    
    <div class="acciones">
        <button id="btnVerificar" class="btn-verificar" onclick="validarRespuesta()">
            ¡Verificar mi suma!
        </button>
    </div>

    <div class="modal-backdrop" id="modalInfo">
        <div class="modal-card">
            <h3 id="modalTitulo"></h3>
            <p id="modalMensaje"></p>
            <div class="modal-actions" id="modalAcciones"></div>
        </div>
    </div>
 
    <script>
    const resultadoCorrecto = "<?php echo $total; ?>";
    const ejercicioActual = <?php echo $ejercicio_n; ?>;
    const tipoActual = "<?php echo $tipo; ?>";
    const panelDestino = "<?php echo $volver_url; ?>";
    let draggedId = null;
    let lastZoneId = null;

    function allowDrop(ev) {
        ev.preventDefault();
        let target = ev.target.classList.contains('drop-zone') ? ev.target : ev.target.closest('.drop-zone');
        if (target) {
            target.classList.add('hover');
        }
    }

    function leave(ev) {
        let target = ev.target.classList.contains('drop-zone') ? ev.target : ev.target.closest('.drop-zone');
        if(target) target.classList.remove('hover');
    }

    function drag(ev) {
        ev.dataTransfer.setData("fichaId", ev.target.id);
        const zona = ev.target.closest('.drop-zone');
        if (zona) {
            ev.dataTransfer.setData("fromZoneId", zona.id);
        }
        ev.target.classList.add('dragging');
        draggedId = ev.target.id;
        lastZoneId = zona ? zona.id : null;
    }

    function dragEnter(ev) {
        const zona = ev.currentTarget;
        if (!zona || !draggedId || zona.id === lastZoneId) {
            return;
        }
        swapFichas(zona);
        lastZoneId = zona.id;
    }

    function drop(ev) {
        ev.preventDefault();
        let fichaId = draggedId || ev.dataTransfer.getData("fichaId");
        let fromZoneId = lastZoneId || ev.dataTransfer.getData("fromZoneId");
        let fichaElement = document.getElementById(fichaId);
        let zona = ev.target.classList.contains('drop-zone') ? ev.target : ev.target.closest('.drop-zone');
        if (!zona || !fichaElement) return;

        if (zona.id === fromZoneId || zona.contains(fichaElement)) {
            zona.classList.remove('hover');
            return;
        }
        swapFichas(zona, fromZoneId);
        zona.classList.remove('hover');
    }

    function validarRespuesta() {
        let respuestaUsuario = "";
        const zonas = document.querySelectorAll('.drop-zone');
        zonas.forEach(zona => {
            const ficha = zona.querySelector('.ficha');
            if (ficha) {
                respuestaUsuario += ficha.innerText.trim();
            }
        });

        if (respuestaUsuario === resultadoCorrecto) {
            fetch('logica_guardado.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ejercicio_n: ejercicioActual, tipo: tipoActual })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    mostrarModal("¡Excelente! ✅", "La suma es correcta. ¡Sigue sumando!", [
                        { texto: "Volver al panel", clase: "btn-panel", accion: () => window.location.href = panelDestino }
                    ]);
                } else {
                    mostrarModal("Ups...", data.message, [
                        { texto: "Reintentar", clase: "btn-reintentar", accion: cerrarModal }
                    ]);
                }
            })
            .catch(() => {
                mostrarModal("Oops 😅", "Hubo un problema al conectar con el servidor.", [
                    { texto: "Reintentar", clase: "btn-reintentar", accion: cerrarModal }
                ]);
            });
        } else {
            mostrarModal("¡Sigue intentando! ❌", "La suma no es correcta todavía. Si sales al lobby quedará marcada como incorrecta.", [
                { texto: "Reintentar", clase: "btn-reintentar", accion: cerrarModal },
                { texto: "Ir al lobby", clase: "btn-panel", accion: marcarIncorrectoYSalir }
            ]);
        }
    }

    function mostrarModal(titulo, mensaje, acciones) {
        const modal = document.getElementById('modalInfo');
        const modalTitulo = document.getElementById('modalTitulo');
        const modalMensaje = document.getElementById('modalMensaje');
        const modalAcciones = document.getElementById('modalAcciones');
        modalTitulo.innerText = titulo;
        modalMensaje.innerText = mensaje;
        modalAcciones.innerHTML = '';

        acciones.forEach(accion => {
            const btn = document.createElement('button');
            btn.className = 'modal-btn ' + accion.clase;
            btn.innerText = accion.texto;
            btn.onclick = accion.accion;
            modalAcciones.appendChild(btn);
        });

        modal.classList.add('activo');
    }

    function cerrarModal() {
        document.getElementById('modalInfo').classList.remove('activo');
    }

    function animarZona(zona) {
        zona.classList.add('swap');
        setTimeout(() => zona.classList.remove('swap'), 200);
    }

    function flipAnim(elemento, firstRect, lastRect) {
        if (!elemento) return;
        const dx = firstRect.left - lastRect.left;
        const dy = firstRect.top - lastRect.top;
        if (dx === 0 && dy === 0) return;
        elemento.style.transition = 'transform 0s';
        elemento.style.transform = `translate(${dx}px, ${dy}px)`;
        requestAnimationFrame(() => {
            elemento.style.transition = 'transform 0.2s ease';
            elemento.style.transform = 'translate(0, 0)';
        });
        elemento.addEventListener('transitionend', () => {
            elemento.style.transition = '';
            elemento.style.transform = '';
        }, { once: true });
    }

    function swapFichas(zona, origenId = null) {
        const fichaElement = document.getElementById(draggedId);
        if (!zona || !fichaElement) return;
        const fromZone = document.getElementById(origenId || lastZoneId);
        if (!fromZone || fromZone === zona) return;

        const fichaEnZona = zona.querySelector('.ficha');
        const draggedFirst = fichaElement.getBoundingClientRect();
        const targetFirst = fichaEnZona ? fichaEnZona.getBoundingClientRect() : null;

        if (fichaEnZona) {
            fromZone.appendChild(fichaEnZona);
        }
        zona.appendChild(fichaElement);

        const draggedLast = fichaElement.getBoundingClientRect();
        const targetLast = fichaEnZona ? fichaEnZona.getBoundingClientRect() : null;

        flipAnim(fichaElement, draggedFirst, draggedLast);
        if (fichaEnZona && targetFirst && targetLast) {
            flipAnim(fichaEnZona, targetFirst, targetLast);
        }

        animarZona(fromZone);
        animarZona(zona);
    }

    function marcarIncorrectoYSalir() {
        fetch('logica_guardado.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ejercicio_n: ejercicioActual, tipo: tipoActual, accion: 'incorrecto' })
        })
        .finally(() => {
            window.location.href = panelDestino + "?msg=incorrecto&ejercicio=" + ejercicioActual;
        });
    }

    document.querySelectorAll('.ficha').forEach(ficha => {
        ficha.addEventListener('dragend', () => {
            ficha.classList.remove('dragging');
            draggedId = null;
            lastZoneId = null;
        });
    });

    document.querySelectorAll('.drop-zone').forEach(zona => {
        zona.addEventListener('dragenter', dragEnter);
    });
    </script>
</body>
</html>
