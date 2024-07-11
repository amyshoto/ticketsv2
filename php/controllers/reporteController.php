<?php
include '../conexionbd.php';

// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['correo'])) {
    // Si no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: ../php/index.php");
    exit();
} else {
    $correo1 = $_SESSION['correo'];
    $query = "SELECT * FROM usuario WHERE correo = '$correo1'";
    $result = pg_query($conexion, $query);
    $row = pg_fetch_assoc($result);
    $idUsuario = $row['id'];

    $Asunto = $_POST["asunto"];
    $Nombre = $_POST["nombre"];
    $Gerencia = $_POST["gerencia"];
    $Ubicacion = $_POST["ubicacion"];
    $Problema = $_POST["problema"];
    $fechaEntrada = $_POST["fechaActual"];

    // Cambia el formato de la fecha de 'yyyy-mm-dd' a 'yyyy/mm/dd'
    $fechaEntrada = str_replace('-', '/', $fechaEntrada);

    // Obtener el siguiente folio basado en el máximo actual en la tabla de tickets
    $query = "SELECT MAX(Folio) as max_folio FROM Ticket";
    $result = pg_query($conexion, $query);
    $row = pg_fetch_assoc($result);
    $siguienteFolio = $row['max_folio'] + 1;

    // Insertar el nuevo ticket en la base de datos con el siguiente folio
    $query = "INSERT INTO ticket (Folio, Asunto, Nombre, Problema, fechaEntrada, Gerencia, Ubicacion, idusuario) 
              VALUES ('$siguienteFolio', '$Asunto', '$Nombre', '$Problema', '$fechaEntrada', '$Gerencia', '$Ubicacion', '$idUsuario')";
    $ejecuta = pg_query($conexion, $query);

    // Redirigir según el rol del usuario
    if ($_SESSION['es_admin'] == 't') {
        header("location: ../indexAdmin.php");
        exit();
    } elseif ($_SESSION['es_superadmin'] === 't') {
        header("location: ../indexSuperadmin.php");
        exit();
    } elseif ($_SESSION['es_encargado'] === 't') {
        header("location: ../indexEncargado.php");
        exit();
    }else {
        header("location: ../indexUsuario.php");
        exit();
    }  
}
?>
