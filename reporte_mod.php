<?php
    include 'php/conexionbd.php';
    // Iniciar la sesión
    session_start();

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['correo'])) {
        // Si no ha iniciado sesión, redirigirlo a la página de inicio de sesión
        header("Location: index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de ticket</title>
    <link rel="stylesheet" href="css/style_mod.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="banner">
        <div class="img-container">
            <img src="img/logo.png" alt="Logotipo de AMIM">
        </div>
        <h1>Tickets</h1>
    </div>
    <form id="form_reporte" action="php/reporte_modbd.php" method="post" class="container" onload="limpiarFormulario()">
    <?php 
        // Recibir el parámetro 'folio' de la URLtype="hidden"
        $folio1 = isset($_GET['folio']) ? $_GET['folio'] : '';

        // Realizar la consulta SQL para obtener los datos del ticket con el folio proporcionado
        $query = "SELECT * FROM Ticket WHERE Folio = '$folio1'";
        $ejecuta = pg_query($conexion, $query);

        if ($mostrar=pg_fetch_array($ejecuta)) {
            if ($mostrar['estado'] == 'Cancelado') {
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
                        <input class="boton btn centro" id="fecha" name="fechaActual" value="<?php echo $fechaEntrada; ?>" readonly>
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
                        <textarea name="cancelacion" id="desCancelacion" class="boton btn" rows="4" readonly><?php echo $mostrar['solucioncancelacion']; ?></textarea>
                    </label>
                    <label for="fechaSolucion"><p>Fecha de resolución</p>
                    <?php 
                        // Formatear la fecha en 'dd/mm/aaaa'
                        $fechaSolucion = date('d/m/Y', strtotime($mostrar['fechasolucion']));
                    ?> 
                    <input name="fechaSolucion" id="fechaSolucion" class="btn centro" value="<?php echo $fechaSolucion; ?>" readonly/>
                </label>
                </div>
            </div>
            <?php
            } else {
        ?> 
        <div class="item item-1">
            <div class="info">
                <div class="info-folio">
                    <label for="folio"><p>Folio</p>
                        <input class="boton btn centro" id="folio"  name="folio1" value="<?php echo $folio1 ?>" readonly>             
                    </label>                    
                </div>
                <label for="asunto"><p>Asunto</p>
                    <input class="boton btn" id="asunto" type="text" name="asunto" value="<?php echo $mostrar['asunto']; ?>" required>                 
                </label>
                <label for="nombre"><p>Nombre</p>
                    <input class="boton btn" id="nombre" type="text" name="nombre" value="<?php echo $mostrar['nombre']; ?>" readonly>                 
                </label>

                <label for="encargado"><p>Encargado</p>
                    <select class="boton btn" id="encargado" name="encargado" required>
                    <?php
                        // Ejecutar la consulta para obtener la lista de encargados desde la base de datos
                        $query2 = "SELECT * FROM encargado WHERE eliminado = false";
                        $ejecuta2 = pg_query($conexion, $query2);

                        // Verificar si la consulta se ejecutó correctamente
                        if ($ejecuta2) {
                            // Iterar sobre los resultados y construir las opciones del select
                            while ($encargado = pg_fetch_assoc($ejecuta2)) {
                                $nombreEncargado = $encargado['nombreencargado'];
                                echo '<option value="' . $nombreEncargado . '"';
                                if ($mostrar['encargado'] == $nombreEncargado) {
                                    echo ' selected';
                                }
                                echo '>' . $nombreEncargado . '</option>';
                            }
                        } else {
                            echo '<option value="">Error al obtener encargados</option>';
                        }
                    ?>
                    </select>
                </label>

                <label for="desProblema"><p id="problema">Descripción del problema</p>
                    <textarea name="problema" id="desProblema" class="boton btn" rows="4" required><?php echo $mostrar['problema']; ?></textarea>  
                </label> 

                <label for="fechaActual"><p>Fecha de entrada</p>  
                    <input type="date" class="btn centro" id="fecha" name="fechaActual" value="<?php echo $mostrar['fechaentrada']; ?>">
                </label>
            </div>
        </div>

        <div class="item item-2">
            <div class="info2">
                <div class="info-estado">
                    <label for="estado"><p>Estado</p>
                        <select class="boton btn" id="estado" name="estado" required>
                            <option value="Nuevo" <?php if ($mostrar['estado'] == 'Nuevo') echo 'selected'; ?>>Nuevo</option>
                            <option value="En proceso" <?php if ($mostrar['estado'] == 'En proceso') echo 'selected'; ?>>En proceso</option>
                            <option value="Terminado" <?php if ($mostrar['estado'] == 'Terminado') echo 'selected'; ?>>Terminado</option>
                            <option disabled value="Terminado" <?php if ($mostrar['estado'] == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
                        </select>      
                    </label>                    
                </div>
                <label for="gerencia"><p>Dirección/Unidad</p>
                    <select class="boton btn" id="gerencia" name="gerencia" required>                   
                        <option value="AG" <?php if ($mostrar['gerencia'] == 'AG') echo 'selected'; ?>>AG</option>
                        <option value="DMA" <?php if ($mostrar['gerencia'] == 'DMA') echo 'selected'; ?>>DMA</option>
                        <option value="DSYS" <?php if ($mostrar['gerencia'] == 'DSYS') echo 'selected'; ?>>DSYS</option>
                        <option value="DFI" <?php if ($mostrar['gerencia'] == 'DFI') echo 'selected'; ?>>DFI</option>
                        <option value="DJYT" <?php if ($mostrar['gerencia'] == 'DJYT') echo 'selected'; ?>>DJYT</option>
                        <option value="DA" <?php if ($mostrar['gerencia'] == 'DA') echo 'selected'; ?>>DA</option>    
                        <option value="UTIC" <?php if ($mostrar['gerencia'] == 'UTIC') echo 'selected'; ?>>UTIC</option>    
                        <option value="OIC" <?php if ($mostrar['gerencia'] == 'OIC') echo 'selected'; ?>>OIC</option>    
                    </select>                
                </label>
                <label for="ubicacion"><p>Ubicación física</p>
                    <input class="boton btn" id="ubicacion" type="text" name="ubicacion" value="<?php echo $mostrar['ubicacion']; ?>" required>                 
                </label>  
                <label for="desSolucion"><p id="solucion">Descripción de la solución</p>
                    <textarea name="solucion" id="desSolucion" class="boton btn" rows="4" required><?php echo $mostrar['solucioncancelacion']; ?></textarea>  
                </label>
                <label for="fechaSolucion"><p>Fecha de resolución</p>
                    <input type="date" name="fechaSolucion" id="fechaSolucion" class="btn centro" value="<?php echo $mostrar['fechasolucion']; ?>"/>
                </label>
            </div>
        </div>
        <div class="item item-3 boton3">
            <a class="btn3" id="cancelar" href="php/reporte_cancelado.php?folio=<?php echo $folio1 ?>">Cancelar ticket</a>
            <input type="submit" class="btn3" value="Guardar">
        </div>
        <?php
            }
        }
        ?>  
    </form>
    <script src="js/script.js"></script>
</body>
</html>