<?php
include 'conexionbd.php';
session_start();
$msg = "";

if (!isset($_SESSION['correo'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_submit"])) {
        $nuevo = $_POST["nuevo"];
        $query = "INSERT INTO encargado (nombreencargado) VALUES ('$nuevo')";
        $ejecuta = pg_query($conexion, $query);

        if ($ejecuta) {
            header("location: indexSuperadmin.php");
        } else {
            $msg = "Error al añadir encargado";
        }
    } elseif (isset($_POST["delete_submit"])) {
        $eliminar = $_POST["eliminar"];

        // Actualiza la columna eliminado en lugar de eliminar físicamente
        $query_actualizar = "UPDATE encargado SET eliminado = true WHERE nombreencargado = '$eliminar'";
        $ejecuta_actualizar = pg_query($conexion, $query_actualizar);

        if ($ejecuta_actualizar) {
            header("location: indexSuperadmin.php");
        } else {
            $msg = "Error al marcar como eliminado el encargado";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar información</title>
    <link rel="stylesheet" href="../css/style_modinfo.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="banner">
        <div class="img-container">
            <img src="../img/logo.png" alt="Logotipo de AMIM">
        </div>
        <h1>Editar información</h1>
    </div>

    <form class="container" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="nuevo">
            <p>Nuevo encargado</p>
            <input class="texto" type="text" id="nuevo" name="nuevo">
        </label><br>
        <input type="submit" class="btn" name="add_submit" value="Añadir">
    </form>

    <form class="container" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="eliminar">
            <p>Eliminar encargado</p>
            <input class="texto" type="text" id="eliminar" name="eliminar">
        </label><br>
        <input type="submit" class="btn" name="delete_submit" value="Eliminar">
    </form>

    <?php if ($msg !== "") { ?>
        <div class="mensaje">
            <p><?php echo $msg; ?></p>
        </div>
    <?php } ?>
</body>

</html>

