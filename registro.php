<?php
include 'db.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['usuario'];
    $pass = $_POST['password'];

    try {
        // Insertamos el nuevo usuario
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, password) VALUES (?, ?)");
        $stmt->execute([$user, $hash]);
        $mensaje = "<p style='color:green'>¡Registro exitoso! Ya puedes <a href='login.php'>iniciar sesión</a>.</p>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Error de duplicado
            $mensaje = "<p style='color:red'>El nombre de usuario ya existe.</p>";
        } else {
            $mensaje = "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - MathMaster 10</title>
    <style>
        @import url('https://fonts.cdnfonts.com/css/arial-rounded-mt-bold');
        body { font-family: 'Arial Rounded MT Bold', sans-serif; background: #f0f4f8; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .reg-box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; width: 350px; border: 4px solid #4CAF50; }
        input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; }
        button { background: #4CAF50; color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="reg-box">
        <h2>Crear Cuenta en MathMaster 10 🎨</h2>
        <?= $mensaje ?>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Elige un nombre de usuario" required>
            <input type="password" name="password" placeholder="Elige una contraseña" required>
            <button type="submit">Registrarme</button>
        </form>
        <p><a href="login.php">Volver al Login</a></p>
    </div>
</body>
</html>
