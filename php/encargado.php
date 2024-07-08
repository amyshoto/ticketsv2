<?php
include 'conexionbd.php';
session_start();
$msg_nuevo = "";
$msg_eliminar = "";
$msg_admin = "";
$msg_eliminar_admin = "";

if (!isset($_SESSION['correo'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_submit"])) {
        $nuevo = $_POST["nuevo"];
        
        // Verificar si el encargado ya existe
        $query_verificar = "SELECT * FROM encargado WHERE nombreencargado = '$nuevo'";
        $resultado_verificar = pg_query($conexion, $query_verificar);
        
        if (pg_num_rows($resultado_verificar) > 0) {
            $msg_nuevo = "Nombre de encargado existente";
        } else {
            // Insertar el nuevo encargado si no existe
            $query = "INSERT INTO encargado (nombreencargado) VALUES ('$nuevo')";
            $ejecuta = pg_query($conexion, $query);

            if ($ejecuta) {
                $msg_nuevo = "Se agregó un nuevo encargado";
            } else {
                $msg_nuevo = "Error al añadir encargado";
            }
        }
    } elseif (isset($_POST["delete_submit"])) {
        $eliminar = $_POST["eliminar"];

        // Actualiza la columna eliminado en lugar de eliminar físicamente
        $query_actualizar = "UPDATE encargado SET eliminado = true WHERE nombreencargado = '$eliminar'";
        $ejecuta_actualizar = pg_query($conexion, $query_actualizar);

        if ($ejecuta_actualizar) {
            $msg_eliminar = "Se eliminó un encargado";
        } else {
            $msg_eliminar = "Error al marcar como eliminado el encargado";
        }
    } elseif (isset($_POST["admin_submit"])) {
        $admin = $_POST["admin"];

        // Verificar si el correo existe en la tabla usuario
        $query_verificar = "SELECT id FROM usuario WHERE correo = '$admin'";
        $resultado_verificar = pg_query($conexion, $query_verificar);

        if (pg_num_rows($resultado_verificar) > 0) {
            // Actualizar el usuario para hacerlo admin
            $query_admin = "UPDATE usuario SET es_admin = true WHERE correo = '$admin'";
            $ejecuta_admin = pg_query($conexion, $query_admin);

            if ($ejecuta_admin) {
                $msg_admin = "Se hizo admin a un usuario";
            } else {
                $msg_admin = "Error al hacer admin";
            }
        } else {
            $msg_admin = "El correo ingresado no está registrado";
        }
    } elseif (isset($_POST["delete_admin_submit"])) {
        $eliminar_admin = $_POST["eliminar_admin"];

        // Verificar si el correo existe y es admin
        $query_verificar = "SELECT id FROM usuario WHERE correo = '$eliminar_admin' AND es_admin = true";
        $resultado_verificar = pg_query($conexion, $query_verificar);

        if (pg_num_rows($resultado_verificar) > 0) {
            // Actualizar el usuario para quitarle el admin
            $query_eliminar_admin = "UPDATE usuario SET es_admin = false WHERE correo = '$eliminar_admin'";
            $ejecuta_eliminar_admin = pg_query($conexion, $query_eliminar_admin);

            if ($ejecuta_eliminar_admin) {
                $msg_eliminar_admin = "Se eliminó admin a un usuario";
            } else {
                $msg_eliminar_admin = "Error al eliminar admin";
            }
        } else {
            $msg_eliminar_admin = "El correo ingresado no es un admin registrado";
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
    <link rel="stylesheet" href="../css/style_editseccion.css">
    <link rel="stylesheet" href="../css/style_reportes.css">
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

    <div class="container">
    <div class="section">
        <div class="titulo">
            <label><b>Información Encargado</b></label>
        </div>
        <form class="form-section" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="nuevo">
                <p>Nuevo encargado</p>
                <input class="texto" type="text" id="nuevo" name="nuevo">
            </label>
            <input type="submit" class="btn" name="add_submit" value="Añadir">
        </form>

        <form class="form-section" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="eliminar">
                <p>Eliminar encargado</p>
                <input class="texto" type="text" id="eliminar" name="eliminar">
            </label>
            <input type="submit" class="btn" name="delete_submit" value="Eliminar">
        </form>
    </div>

    <!--<div class="separator"></div>
  Línea separadora -->
    
    <div class="section">
        <div class="titulo">
            <label><b>Información Administrador</b></label>
        </div>
        <form class="form-section" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="admin">
                <p>Nuevo administrador</p>
                <input class="texto" type="text" id="admin" name="admin">
            </label>
            <input type="submit" class="btn" name="admin_submit" value="Añadir">
        </form>

        <form class="form-section" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="eliminar_admin">
                <p>Eliminar administrador</p>
                <input class="texto" type="text" id="eliminar_admin" name="eliminar_admin">
            </label>
            <input type="submit" class="btn" name="delete_admin_submit" value="Eliminar">
        </form>
    </div>
</div>


<div class="item item-3 boton3">
            <form method="post" action="indexSuperadmin.php">
            <button type="submit" class="btn3 .btn-guardar" style="background-color: #F56161; border: none; color: black;"> Regresar</button>
            </form>
        </div>
    <script>
        <?php if ($msg_nuevo !== "") { ?>
            alert('<?php echo $msg_nuevo; ?>');
        <?php } ?>
        <?php if ($msg_eliminar !== "") { ?>
            alert('<?php echo $msg_eliminar; ?>');
        <?php } ?>
        <?php if ($msg_admin !== "") { ?>
            alert('<?php echo $msg_admin; ?>');
        <?php } ?>
        <?php if ($msg_eliminar_admin !== "") { ?>
            alert('<?php echo $msg_eliminar_admin; ?>');
        <?php } ?>
    </script>
</body>
</html>
