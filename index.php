<?php
    include 'php/conexionbd.php';

    // Iniciar la sesión
    session_start();

    $error_message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener el nombre de usuario y la contraseña del formulario
        $correo = $_POST["correo"];
        $contrasena = $_POST["contrasena"];

        // Consulta SQL para verificar las credenciales en la base de datos
        $query = "SELECT * FROM usuario WHERE correo = '$correo' AND contrasena = '$contrasena'";
        $result = pg_query($conexion, $query);

        if ($result && pg_num_rows($result) > 0) {
            // Si hay coincidencia, iniciar sesión y redirigir a index_admin.php
            $row = pg_fetch_assoc($result);
            $_SESSION['correo'] = $row['correo'];
            $_SESSION['es_admin'] = $row['es_admin'];
            $_SESSION['es_superadmin'] = $row['es_superadmin'];

            if ($_SESSION['es_admin'] === 't') {
                header("location: php/indexAdmin.php");
                exit();
            } elseif ($_SESSION['es_superadmin'] === 't') {
                header("location: php/indexSuperadmin.php");
                exit();
            } else {
                header("location: php/indexUsuario.php");
                exit();
            }
            exit();
        } else {
            $error_message = "Usuario o contraseña incorrectos";
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_login.css">
    <link rel="stylesheet" href="css/style_banner.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Iniciar sesión</title>
</head>
<body>
    <div class="banner">
        <div class="img-container">
            <a href="index.php"><img src="img/AMIM.png" alt="Logotipo de AMIM"></a>
        </div>
        <h1>Ayuda UTIC</h1>
    </div>

    <div class="container">    
        <div class="img-container2">
            <img src="img/UTI.png" alt="Logotipo de UTI">
        </div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onload="limpiarFormulario()">
            <label for="correo">Correo:</label>
            <input type="text" name="correo" id="correo" required>
            <br>
            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" required>
            <br>
            <input class="btn" type="submit" value="Iniciar sesión"> 
            <p class="registrar">¿No tienes cuenta?<a href="php/signin.php"> Regístrate aquí</a></p>
            <?php
                if ($error_message !== "") {
                    ?>
                    <div class="error">
                        <p><?php echo $error_message; ?></p>
                    </div>
                    <?php
                }
            ?>
        </form>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
