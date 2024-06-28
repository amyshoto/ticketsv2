<?php
    include '../conexionbd.php';

    // Iniciar la sesión
    session_start();

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['correo'])) {
        // Si no ha iniciado sesión, redirigirlo a la página de inicio de sesión
        header("Location: ../php/index.php");
        //echo 'no ha iniciado';
        exit();
    } else {
        $correo1 = $_SESSION['correo'];
        $query = "SELECT * FROM usuario WHERE correo = '$correo1'";
        $result = pg_query($conexion, $query);
        $row = pg_fetch_assoc($result);
        $idUsuario = $row['id'];
        //echo $idUsuario;

        $Asunto = $_POST["asunto"];
        $Nombre = $_POST["nombre"];
        $Gerencia = $_POST["gerencia"];
        $Ubicacion = $_POST["ubicacion"];
        $Problema = $_POST["problema"];
        $fechaEntrada = $_POST["fechaActual"];

        // Cambia el formato de la fecha de 'yyyy-mm-dd' a 'yyyy/mm/dd'
        $fechaEntrada = str_replace('-', '/', $fechaEntrada);

        //echo $fechaEntrada;

        $query = "INSERT INTO ticket (Asunto, Nombre, Problema, fechaEntrada, Gerencia, Ubicacion, idusuario) 
            VALUES ('$Asunto', '$Nombre', '$Problema', '$fechaEntrada', '$Gerencia', '$Ubicacion', '$idUsuario')";

        $ejecuta = pg_query($conexion, $query);

        //echo $_SESSION['es_admin'];
        if ($_SESSION['es_admin'] == 't') {
            header("location: ../indexAdmin.php");
            exit();
        } elseif ($_SESSION['es_superadmin'] === 't') {
            header("location: ../indexSuperadmin.php");
            exit();
        } else {
            header("location: ../indexUsuario.php");
            exit();
        }  
    } 
?>