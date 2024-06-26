<?php 
    include 'conexionbd.php';
    session_start();

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['correo'])) {
        header("Location: ../index.php");
        exit();
    }

    $correo = $_SESSION['correo'];
    $query = "SELECT * FROM usuario WHERE correo = '$correo'";
    $result = pg_query($conexion, $query);
    $row = pg_fetch_assoc($result);
    $rolSuperAdmin = $row['es_superadmin'];

    // Verificar si el usuario tiene el rol adecuado para esta página
    if ($rolSuperAdmin != 't' ) {
        // Si el usuario no tiene el rol de superadministrador, redirigirlo
        header("location: ../index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de tickets</title>
    <link rel="stylesheet" href="../css/style_banner.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="banner">
        <div class="img-container">
            <img src="../img/AMIM.png" alt="Logotipo de AMIM">
        </div>
        <h1>Tickets</h1>
        <div class="login-container">
            <p class="login"><a href="logout.php">Salir</a></p>
        </div>
    </div>
    <div class="boton2">
        <button class="btn2" onclick="location.href='encargado.php'">Editar información</button>
        <button class="btn2" onclick="location.href='reporte.php'">Añadir ticket</button>
    </div>
    <div class="linea"></div> 
    <div class="titulo">
        <div class="fondo item item-1">
            <p>Asunto</p>
        </div>
        <div class="fondo item item-2">
            <p>Folio</p>
        </div>
        <div class="fondo item item-3">
            <p>Encargado</p>
        </div>
        <div class="fondo item item-4">
            <p>Estado</p>
        </div>
    </div>   
    <div class="linea"></div>


    <?php
        // Realiza la consulta SQL para obtener los datos de los tickets y ordenarlos por folio ascendente.
        $query = "SELECT * FROM ticket ORDER BY folio ASC";
        $result = pg_query($conexion, $query);

        if (!$result) {
            echo "Error al ejecutar la consulta.\n";
            exit;
        }

        // Almacena los resultados en un array.
        $tickets = pg_fetch_all($result);

        // Libera el resultado y cierra la conexión.
        pg_free_result($result);
        pg_close($conexion);

        // Si hay tickets, muestra la lista.
        if ($tickets) {
            // Itera sobre los tickets y muéstralos en la lista.
            foreach ($tickets as $ticket) {
                ?>
                <div class="lista">
                    <div class="item item-1">
                        <p><a href="reporteModificar.php?folio=<?php echo $ticket['folio']; ?>">
                            <?php echo $ticket['asunto']; ?>
                        </a></p>
                    <div class="linea2"></div>
                    </div>
                    <div class="item item-2">
                        <p><?php echo $ticket['folio']; ?></p>
                        <div class="linea2"></div>
                    </div>
                    <div class="item item-3">
                        <p><?php echo $ticket['encargado']; ?></p>
                        <div class="linea2"></div>
                    </div>
                    <div class="item item-4">
                        <p><?php echo $ticket['estado']; ?></p>
                        <div class="linea2"></div>
                    </div>
                </div>
                <?php 
            }
        } else {
            ?>
            <div class="lista">
                <div class="item item-1">
                    <p><?php echo "No hay tickets disponibles."; ?></p>
                </div>
            </div>
            <?php
        }
    ?>
</body>
</html>