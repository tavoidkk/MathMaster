<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['usuario'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$user]);
    $usuario_db = $stmt->fetch();

    if ($usuario_db) {
        $hashInfo = password_get_info($usuario_db['password']);
        $esValida = false;

        if ($hashInfo['algo'] !== 0) {
            $esValida = password_verify($pass, $usuario_db['password']);
        } elseif ($pass === $usuario_db['password']) {
            $esValida = true;
            $nuevoHash = password_hash($pass, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $upd->execute([$nuevoHash, $usuario_db['id']]);
        }

        if ($esValida) {
            $_SESSION['usuario_id'] = $usuario_db['id'];
            $_SESSION['nombre'] = $usuario_db['nombre_usuario'];
            header("Location: index.php"); // Redirige al juego
        } else {
            $error = "Usuario o contraseña incorrectos";
        }
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - MathMaster 10</title>
    <style>
        @import url('https://fonts.cdnfonts.com/css/arial-rounded-mt-bold');
        body { font-family: 'Arial Rounded MT Bold', sans-serif; background: #eef2f7; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; width: 350px; border: 4px solid #4a90e2; }
        input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; }
        button { background: #4a90e2; color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>¡Bienvenido a MathMaster 10! 🌟</h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Tu nombre de usuario" required>
            <input type="password" name="password" placeholder="Tu contraseña" required>
            <button type="submit">Entrar a jugar</button>
        </form>
        <p>¿Eres nuevo? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
