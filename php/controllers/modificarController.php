<?php
include '../conexionbd.php';
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['correo'])) {
    // Si no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: index.php");
    exit();
}

$folio = $_POST["folio1"];
$asunto = $_POST["asunto"];
$encargado = $_POST["encargado"];
$problema = $_POST["problema"];
$fechaEntrada = $_POST["fechaActual"];
$estado = $_POST["estado"];
$gerencia = $_POST["gerencia"];
$ubicacion = $_POST["ubicacion"];
$solucion = $_POST["solucion"];
$fechaSolucion = $_POST["fechaSolucion"];

// Cambia el formato de la fecha de 'yyyy-mm-dd' a 'yyyy/mm/dd'
$fechaEntrada = str_replace('-', '/', $fechaEntrada);

if ($asunto != null) {
    $query = "UPDATE ticket SET asunto='$asunto' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);
}
if ($encargado != null) {
    $query = "UPDATE ticket SET idencargado='$encargado' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);
}
if ($problema != null) {
    $query = "UPDATE ticket SET problema='$problema' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);
}
if ($fechaEntrada != null) {
    $query = "UPDATE ticket SET fechaentrada='$fechaEntrada' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);
}
if ($estado != null) {
    $query = "UPDATE ticket SET estado='$estado' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);
}
if ($gerencia != null) {
    $query = "UPDATE ticket SET gerencia='$gerencia' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);
}
if ($ubicacion != null) {
    $query = "UPDATE ticket SET ubicacion='$ubicacion' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);
}
if ($solucion != null) {
    $query = "UPDATE ticket SET solucionCancelacion='$solucion' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);
}
if ($fechaSolucion != null) {
    $query = "UPDATE ticket SET fechasolucion='$fechaSolucion' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);
}

// Redirigir al usuario basado en su rol
if ($_SESSION['es_admin'] == 't') {
    header("location: ../indexAdmin.php");
    exit();
} elseif ($_SESSION['es_superadmin'] === 't') {
    header("location: ../indexSuperadmin.php");
    exit();
} else {
    header("location: ../indexEncargado.php");
}
?>
