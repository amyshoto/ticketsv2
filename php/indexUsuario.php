<?php
    include 'conexionbd.php';
    session_start();

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['correo'])) {
        header("location: ../index.php");
        exit();
    }

    $correo = $_SESSION['correo'];
    //$correo1 = $_SESSION['correo'];
    $query = "SELECT * FROM usuario WHERE correo = '$correo'";
    $result = pg_query($conexion, $query);
    $row = pg_fetch_assoc($result);
    $idUsuario = $row['id'];
    $nombreUsuario = $row['nombre'];

    // Número de tickets por página
    $ticketsPorPagina = 10;
    
    // Página actual, por defecto es 1 si no se pasa en la URL
    $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($paginaActual < 1) {
        $paginaActual = 1;
    }

    // Estado del ticket
    $estado = isset($_GET['estado']) ? $_GET['estado'] : '';

    // Calcular el OFFSET
    $offset = ($paginaActual - 1) * $ticketsPorPagina;

    // Realizar consulta SQL para obtener los tickets del usuario actual con LIMIT, OFFSET y filtro de estado
    $query = "SELECT t.*, u.nombre AS nombre_encargado 
          FROM ticket t 
          LEFT JOIN usuario u ON t.idencargado = u.id 
          WHERE t.idusuario = '$idUsuario'";
if ($estado) {
    $query .= " AND t.estado = '$estado'";
}
$query .= " ORDER BY t.folio ASC LIMIT $ticketsPorPagina OFFSET $offset";
$result = pg_query($conexion, $query);

if (!$result) {
    echo "Error al ejecutar la consulta.\n";
    exit;
}

    // Almacena los resultados en un array.
    $tickets = pg_fetch_all($result);

    // Obtener el total de tickets para el usuario con el filtro de estado
    $queryTotal = "SELECT COUNT(*) AS total FROM ticket WHERE idusuario = '$idUsuario'";
    if ($estado) {
        $queryTotal .= " AND estado = '$estado'";
    }
    $resultTotal = pg_query($conexion, $queryTotal);
    $totalTickets = pg_fetch_assoc($resultTotal)['total'];

    // Calcular el total de páginas
    $totalPaginas = ceil($totalTickets / $ticketsPorPagina);

    // Libera el resultado y cierra la conexión.
    pg_free_result($result);
    pg_free_result($resultTotal);
    pg_close($conexion);

    // Libera el resultado y cierra la conexión.
    //pg_free_result($result);
    //pg_close($conexion);
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
    <link rel="stylesheet" href="../css//style_table.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="banner">
        <div class="img-container">
            <img src="../img/AMIM.png" alt="Logotipo de AMIM">
        </div>
        <h1>Tickets <?php echo $nombreUsuario; ?></h1>
        <div class="login-container">
            <p class="login"><a href="logout.php">Salir</a></p>
        </div>
    </div>
    <div class="boton1">
        <!-- <p>hola <?php //echo $nombreUsuario; ?></p> -->
        <button class="btn1" onclick="location.href='reporte.php'">Añadir ticket</button>
    </div>
    <div class="linea"></div>

    <form method="GET" action="">
        <label for="estado">Filtrar por estado:</label>
        <select name="estado" id="estado">
            <option value="">Todos</option>
            <option value="Nuevo" <?php if ($estado == 'Nuevo') echo 'selected'; ?>>Nuevo</option>
            <option value="En proceso" <?php if ($estado == 'En proceso') echo 'selected'; ?>>En Proceso</option>
            <option value="Terminado" <?php if ($estado == 'Terminado') echo 'selected'; ?>>Terminado</option>
            <option value="Cancelado" <?php if ($estado == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
        </select>
        <button class="boton1" type="submit">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Asunto</th>
                <th>Folio</th>
                <th>Encargado</th>
                <th>Estado</th>
                <th>Gerencia</th>
                <th>Ubicación</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Si hay tickets, muestra la lista.
            if ($tickets) {
                // Itera sobre los tickets y muéstralos en la lista.
                foreach ($tickets as $ticket) {
                    echo "<tr>";
                    echo "<td class='item'><a href='reporteConsulta.php?folio=" . $ticket['folio'] . "'>" . $ticket['asunto'] . "</a></td>";
                    echo "<td>" . $ticket['folio'] . "</td>";
                    echo "<td>" . $ticket['nombre_encargado'] . "</td>";
                    echo "<td>" . $ticket['estado'] . "</td>";
                    echo "<td>" . $ticket['gerencia'] . "</td>";
                    echo "<td>" . $ticket['ubicacion'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No hay tickets disponibles.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <link rel="stylesheet" href="../css/pagination.css">
    <div class="pagination">
        <?php if ($paginaActual > 1): ?>
        <a href="?page=<?php echo $paginaActual - 1; ?>&estado=<?php echo $estado; ?>" class="prev">&laquo;</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="?page=<?php echo $i; ?>&estado=<?php echo $estado; ?>" <?php if ($i == $paginaActual) echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($paginaActual < $totalPaginas): ?>
        <a href="?page=<?php echo $paginaActual + 1; ?>&estado=<?php echo $estado; ?>" class="next">&raquo;</a>
        <?php endif; ?>
    </div>

    <div class="linea"></div>

</body>
</html>