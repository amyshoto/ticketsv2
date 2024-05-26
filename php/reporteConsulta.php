<?php
    include 'conexionbd.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de ticket</title>
    <link rel="stylesheet" href="../css/style_cons.css">
    <link rel="stylesheet" href="../css/style_banner.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="banner">
        <div class="img-container">
            <img src="../img/AMIM.png" alt="Logotipo de AMIM">
        </div>
        <h1>Consulta de tickets</h1>
    </div>
    <div class="container">
        <?php 
            // Recibir el parámetro 'folio' de la URL
            $folio = isset($_GET['folio']) ? $_GET['folio'] : '';

            // Realizar la consulta SQL para obtener los datos del ticket con el folio proporcionado
            $query = "SELECT * FROM Ticket WHERE Folio = '$folio'";
            $ejecuta = pg_query($conexion, $query);

            if ($mostrar=pg_fetch_array($ejecuta)) {
        ?>
        <div class="item item-1">
            <div class="info">
                <div class="info-folio">
                    <label for="folio"><p>Folio</p>
                        <button class="boton btn" id="folio"><?php echo $mostrar['folio']; ?></button>
                    </label>                    
                </div>
                <label for="asunto"><p>Asunto</p>
                    <button class="boton btn" id="asunto"><?php echo $mostrar['asunto']; ?></button>          
                </label>
                <label for="encargado"><p>Encargado</p>
                    <button class="boton btn" id="encargado"><?php echo $mostrar['encargado']; ?></button> 
                </label>
                <label for="desProblema"><p id="problema">Descripción del problema</p>
                    <textarea name="problema" id="desProblema" class="boton btn" rows="4" readonly><?php echo $mostrar['problema']; ?></textarea>  
                </label>
                <label for="fechaActual"><p>Fecha de entrada</p>  
                    <?php 
                        // Formatear la fecha en 'dd/mm/aaaa'
                        $fechaEntrada = date('d/m/Y', strtotime($mostrar['fechaentrada']));
                    ?> 
                    <button class="boton btn centro" id="fecha"><?php echo $fechaEntrada; ?></button> 
                </label>

            </div>
        </div>
        <div class="item item-2">
            <div class="info2">
                <div class="info-estado">
                    <label for="estado"><p>Estado</p>
                        <button class="boton btn" id="estado"><?php echo $mostrar['estado']; ?></button>
                    </label>              
                </div>
                <label for="gerencia"><p>Dirección/Unidad</p>
                    <button id="gerencia" class="boton btn"><?php echo $mostrar['gerencia']; ?></button>        
                </label>
                <label for="ubicacion"><p>Ubicación física</p>
                    <button id="ubicacion" class="boton btn"><?php echo $mostrar['ubicacion']; ?></button>             
                </label>  
                <label for="desSolucion"><p id="solucion">Descripción de la solución/cancelación</p>
                    <textarea name="solucion" id="desSolucion" class="boton btn" rows="4" readonly><?php echo $mostrar['solucioncancelacion']; ?></textarea>  
                </label>

                <?php if ($mostrar['fechasolucion']==NULL) { ?>
                    <label for="fechaSolucion"><p>Fecha de resolución</p>
                        <button id="fechaSolucion" class="btn centro">00/00/0000</button>
                    </label>
                <?php } else { ?>
                    <label for="fechaSolucion"><p>Fecha de resolución</p>  
                    <?php 
                        // Formatear la fecha en 'dd/mm/aaaa'
                        $fechaSolucion = date('d/m/Y', strtotime($mostrar['fechasolucion']));
                    ?>  
                    <button class="btn centro" id="fechaSolucion"><?php echo $fechaSolucion; ?></button>
                    </label>
                <?php } ?>
            </div>
        </div>
        <?php
            } else {
                echo "No existe";
            }
        ?>       
    </div>
    <script src="js/script.js"></script>
</body>
</html>