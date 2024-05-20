<?php
// Inicia la sesión
session_start();

// Destruye todas las variables de sesión
session_unset();

// Finaliza la sesión
session_destroy();

// Redirige a la página de inicio de sesión o a donde desees después del cierre de sesión
header("Location: ../index.php");
exit;
?>
