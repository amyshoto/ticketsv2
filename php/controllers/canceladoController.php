<?php 
    include '../conexionbd.php';

    // Iniciar la sesión
    session_start();

    $folio = $_POST["folio1"];
    $estado = $_POST['estado'];
    $cancelacion = $_POST['cancelacion'];

    $query = "UPDATE ticket SET estado='$estado', solucionCancelacion='$cancelacion' WHERE folio='$folio'";
    $ejecuta = pg_query($conexion, $query);

    if (($_SESSION['es_admin'] == 't')) {
        header("location: ../indexAdmin.php");
        exit();
    } elseif ($_SESSION['es_superadmin'] === 't') {
        header("location: ../indexSuperadmin.php");
        exit();
    }
                
?>