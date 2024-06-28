<?php
    include 'conexionbd.php';
    // Iniciar la sesión
    session_start();

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['correo'])) {
        // Si no ha iniciado sesión, redirigirlo a la página de inicio de sesión
        header("Location: ../index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte cancelado</title>
    <link rel="stylesheet" href="../css/style_mod.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="banner">
        <div class="img-container">
            <img src="../img/logo.png" alt="Logotipo de AMIM">
        </div>
        <h1>Tickets</h1>
    </div>
    <form id="form_reporte" action="controllers/canceladoController.php" method="post" class="container" onload="limpiarFormulario()">
    <?php 
        // Recibir el parámetro 'folio' de la URLtype="hidden"
        $folio1 = isset($_GET['folio']) ? $_GET['folio'] : '';

        // Realizar la consulta SQL para obtener los datos del ticket con el folio proporcionado
        $query = "SELECT * FROM Ticket WHERE Folio = '$folio1'";
        $ejecuta = pg_query($conexion, $query);

        if ($mostrar=pg_fetch_array($ejecuta)) {
    ?>
        <div class="item item-1">
            <div class="info">
                <div class="info-folio">
                    <label for="folio"><p>Folio</p>
                        <input class="boton btn centro" id="folio"  name="folio1" value="<?php echo $folio1 ?>" readonly>             
                    </label>                    
                </div>
                <label for="asunto"><p>Asunto</p>
                    <input class="boton btn" id="asunto"  name="asunto" value="<?php echo $mostrar['asunto']; ?>" readonly>      
                </label>
                <label for="nombre"><p>Nombre</p>
                    <input class="boton btn" id="nombre" type="text" name="nombre" value="<?php echo $mostrar['nombre']; ?>" readonly>                 
                </label>
                <label for="encargado"><p>Encargado</p>
                    <input class="boton btn" id="encargado" name="encargado" value="<?php echo $mostrar['encargado']; ?>" readonly>
                </label>
                <label for="desProblema"><p id="problema">Descripción del problema</p>
                    <textarea name="problema" id="desProblema" class="boton btn" rows="4" readonly><?php echo $mostrar['problema']; ?></textarea>  
                </label> 

                <label for="fechaActual"><p>Fecha de entrada</p>  
                    <?php 
                        // Formatear la fecha en 'dd/mm/aaaa'
                        $fechaEntrada = date('d/m/Y', strtotime($mostrar['fechaentrada']));
                    ?>  
                    <input class=" btn centro" id="fecha" name="fechaActual" value="<?php echo $fechaEntrada; ?>" readonly>
                </label>
            </div>
        </div>

        <div class="item item-2">
            <div class="info2">
                <div class="info-estado">
                    <label for="estado"><p>Estado</p>
                        <input class="boton btn" id="estado" name="estado" value="Cancelado" readonly>   
                    </label>                    
                </div>
                <label for="gerencia"><p>Dirección/Unidad</p>
                     <input class="boton btn" id="gerencia" name="gerencia" value="<?php echo $mostrar['gerencia']; ?>" readonly>      
                </label>
                <label for="ubicacion"><p>Ubicación física</p>
                    <input class="boton btn" id="ubicacion" name="ubicacion" value="<?php echo $mostrar['ubicacion']; ?>" readonly>                 
                </label>  
                <label for="desCancelacion"><p id="cancelacion">Justificación de la cancelación</p>
                    <textarea name="cancelacion" id="desCancelacion" class="boton btn" rows="4" required></textarea>  
                </label>

                <label for="fechaSolucion"><p>Fecha de cancelación</p>  
                    <input required type="date" class="btn centro" id="fecha" name="fechaSolucion" value="<?php echo $fechaSolucion; ?>">
                </label>

            </div>
        </div>
        <div class="item item-3 boton3">
            <input type="submit" class="btn3" value="Guardar">
        </div>
        <?php
            } else {
                echo "No existe";
            }
        ?>  
    </form>
        <?php
        ?>
    <script src="../js/script.js"></script>
</body>
</html>