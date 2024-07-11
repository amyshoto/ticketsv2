<?php
include 'conexionbd.php';
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['correo'])) {
    header("location: index.php");
    exit();
}

$correo = $_SESSION['correo'];
$query = "SELECT * FROM usuario WHERE correo = '$correo'";
$result = pg_query($conexion, $query);
$row = pg_fetch_assoc($result);
$nombreUsuario = $row['nombre'];
$rolSuperAdmin = $row['es_superadmin'];
$rolAdmin = $row['es_admin'];
$rolEncargado = $row['es_encargado']; 

// Obtener el siguiente folio basado en el máximo actual en la tabla de tickets
$query = "SELECT MAX(Folio) as max_folio FROM Ticket";
$result = pg_query($conexion, $query);
$row = pg_fetch_assoc($result);
$siguienteFolio = $row['max_folio'] + 1;

pg_close($conexion);

// Establecer la URL de redirección según el rol del usuario
if ($rolSuperAdmin == 't') {
    $redirectUrl = 'indexSuperadmin.php';
} elseif ($rolAdmin == 't') {
    $redirectUrl = 'indexAdmin.php';
}  elseif ($rolEncargado == 't') {
    $redirectUrl = 'indexEncargado.php';
}  else {
    $redirectUrl = 'indexUsuario.php';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible"="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de ticket</title>
    <link rel="stylesheet" href="../css/style_banner.css">
    <link rel="stylesheet" href="../css/style_reportes.css">
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
                <label for="folio"><p>Folio</p>
                    <input type="text" class="boton btn" id="folio" name="folio" value="<?php echo $siguienteFolio; ?>" readonly>                
                </label>
                <label for="asunto"><p>Asunto</p>
                    <select class="boton btn" id="asunto" name="asunto" required>
                        <option value="🖥️Computadora">🖥️Computadora</option>
                        <option value="🖨️Impresora">🖨️Impresora</option>
                        <option value="📇Scanner">📇Scanner</option>
                        <option value="🧑🏻‍💻Software">🧑🏻‍💻Software</option>
                        <option value="⌨️ Monitor">⌨️ Monitor</option>
                        <option value="💾Memoria USB">💾Memoria USB</option>
                        <option value="📞Telefonía">📞Telefonía</option>
                        <option value="🌐Internet">🌐Internet</option>
                        <option value="🎲Otro">🎲Otro</option>
                    </select>                
                </label>
                <label for="nombre"><p>Nombre</p>
                    <input class="boton btn" id="nombre" type="text" name="nombre" value="<?php echo $nombreUsuario; ?>" readonly>                 
                </label>
                <label for="gerencia"><p>Dirección/Unidad</p>
                    <select class="boton btn" id="gerencia" name="gerencia" required>
                        <option value="💼AG">💼AG</option>
                        <option value="🚴🏻‍♀️DMA">🚴🏻‍♀️DMA</option>
                        <option value="🚦DSYS">🚦DSYS</option>
                        <option value="📸DFI">📸DFI</option>
                        <option value="💼🔎DJYT">💼🔎DJYT</option>
                        <option value="📄DA">📄DA</option>
                        <option value="🖥️UTIC">🖥️UTIC</option>
                        <option value="⚖️OIC">⚖️OIC</option>
                    </select>                
                </label>
                <label for="ubicacion"><p>Ubicación física</p>
                    <input class="boton btn" id="ubicacion" type="text" name="ubicacion" required>                 
                </label>        
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
            <button type="button" class="btn3 btn-cancelar" onclick="window.location.href='<?php echo $redirectUrl; ?>'">Cancelar</button>
            <input type="submit" class="btn3 btn-guardar" value="Guardar">
        </div>


    </form>
    <script src="../js/script.js"></script>
</body>
</html>
