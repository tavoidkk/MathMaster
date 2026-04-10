<?php
session_start(); // Localizamos la sesión actual
session_unset(); // Borramos las variables (nombre, id, etc.)
session_destroy(); // Destruimos la sesión por completo

// Redirigimos al Login inmediatamente
header("Location: login.php");
exit();
?>