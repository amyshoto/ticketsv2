<?php
    include 'conexionbd.php';
    session_start();

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['correo'])) {
        header("location: index.php");
        exit();
    }

    $correo = $_SESSION['correo'];
    //$correo1 = $_SESSION['correo'];
    $query = "SELECT * FROM usuario WHERE correo = '$correo'";
    $result = pg_query($conexion, $query);
    $row = pg_fetch_assoc($result);
    //$idUsuario = $row['id'];
    $nombreUsuario = $row['nombre'];
    //echo $idUsuario;
    pg_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de ticket</title>
    <link rel="stylesheet" href="../css/style_banner.css">
    <link rel="stylesheet" href="../css/style_reporte.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="banner">
        <div class="img-container">
            <img src="../img/AMIM.png" alt="Logotipo de AMIM">
        </div>
        <h1>Reporte de ticket</h1>
    </div>
    <form id="form_reporte" action="controllers/reporteController.php" method="post" class="container" onload="limpiarFormulario()">
        <div class="item item-1">
            <div class="info">
                <!-- <div class="info-folio">
                    <label for="folio"><p>Folio</p>
                        <input type="text" class="boton btn" id="folio" name="folio" required>                
                    </label>                    
                </div> -->
                <label for="asunto"><p>Asunto</p>
                    <input class="boton btn" id="asunto" type="text" name="asunto" required>                 
                </label>
                <label for="nombre"><p>Nombre</p>
                    <input class="boton btn" id="nombre" type="text" name="nombre" value="<?php echo $nombreUsuario; ?>" readonly>                 
                </label>
                <label for="gerencia"><p>Dirección/Unidad</p>
                    <select class="boton btn" id="gerencia" name="gerencia" required>
                        <option value="AG">AG</option>
                        <option value="DMA">DMA</option>
                        <option value="DSYS">DSYS</option>
                        <option value="DFI">DFI</option>
                        <option value="DJYT">DJYT</option>
                        <option value="DA">DA</option>
                        <option value="UTIC">UTIC</option>
                        <option value="OIC">OIC</option>
                    </select>                
                </label>
                <label for="ubicacion"><p>Ubicación física</p>
                    <input class="boton btn" id="ubicacion" type="text" name="ubicacion" required>                 
                </label>        
            <!-- <div class="boton" onload="limpiarFormulario()"> -->
            <!-- </div> -->
            </div>
        </div>
        <div class="item item-2">
            <div class="info2">
                <label for="desProblema"><p id="problema">Descripción del problema</p>
                    <textarea name="problema" id="desProblema" class="boton btn" rows="4" required></textarea>  
                </label>
                <label for="fechaActual"><p>Fecha de entrada</p>
                    <div id="fecha">
                        <input type="date" class="btn" name="fechaActual" id="fechaActual" readonly>
                    </div>
                </label>
            </div>
        </div>
        <div class="item item-3 boton3">
            <input type="submit" class="btn3" value="Guardar">
        </div>
    </form>
    <script src="../js/script.js"></script>
</body>
</html>