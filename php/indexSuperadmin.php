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
    
    <!-- Formulario de filtro de estado -->
    <form method="GET" action="">
        <label for="estado">Filtrar por estado:</label>
        <select name="estado" id="estado" onchange="this.form.submit()">
            <option value="Nuevo" <?php if (isset($_GET['estado']) && $_GET['estado'] == 'Nuevo') echo 'selected'; ?>>Nuevo</option>
            <option value="En proceso" <?php if (isset($_GET['estado']) && $_GET['estado'] == 'En proceso') echo 'selected'; ?>>En proceso</option>
            <option value="Terminado" <?php if (isset($_GET['estado']) && $_GET['estado'] == 'Terminado') echo 'selected'; ?>>Terminado</option>
            <option value="Cancelado" <?php if (isset($_GET['estado']) && $_GET['estado'] == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
        </select>
    </form>

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
        // Obtener el estado seleccionado del formulario o usar "nuevo" por defecto.
        $estado = isset($_GET['estado']) ? $_GET['estado'] : 'nuevo';

        // Obtener el número de página actual, por defecto será 1.
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Realizar la consulta SQL para obtener los datos de los tickets filtrados por estado con paginación.
        $query = "SELECT * FROM ticket WHERE estado = '$estado' ORDER BY folio ASC LIMIT $limit OFFSET $offset";
        $result = pg_query($conexion, $query);

        if (!$result) {
            echo "Error al ejecutar la consulta.\n";
            exit;
        }

        // Almacenar los resultados en un array.
        $tickets = pg_fetch_all($result);

        // Consulta para contar el número total de tickets para paginación.
        $count_query = "SELECT COUNT(*) AS total FROM ticket WHERE estado = '$estado'";
        $count_result = pg_query($conexion, $count_query);
        $total_tickets = pg_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_tickets / $limit);

        // Liberar el resultado y cerrar la conexión.
        pg_free_result($result);
        pg_free_result($count_result);
        pg_close($conexion);

        // Si hay tickets, muestra la lista.
        if ($tickets) {
            // Iterar sobre los tickets y mostrarlos en la lista.
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

    <!-- Paginación -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?estado=<?php echo $estado; ?>&page=<?php echo $page - 1; ?>">&laquo; Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?estado=<?php echo $estado; ?>&page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>>
                <?php echo 'Página: '.$i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?estado=<?php echo $estado; ?>&page=<?php echo $page + 1; ?>">Siguiente &raquo;</a>
        <?php endif; ?>
    </div>

</body>
</html>
