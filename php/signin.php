<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style_banner.css">
    <link rel="stylesheet" href="../css/style_signin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Registro de usuario</title>
</head>
<body>
    <div class="banner">
        <div class="img-container">
            <a href="../index.php"><img src="../img/AMIM.png" alt="Logotipo de AMIM"></a>
        </div>
        <h1>Registro de usuario</h1>
    </div>
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="correo">Correo:</label>
            <input type="email" name="correo" required>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" required>

            <label for="confirmar_contrasena">Confirmar contraseña:</label>
            <input type="password" name="confirmar_contrasena" required>

            <input class="btn" type="submit" value="Registrar">
        </form>

        <?php
            include 'conexionbd.php';
            // Manejo del formulario y registro en la base de datos
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Recuperar datos del formulario
                $correo = $_POST['correo'];
                $nombre = $_POST['nombre'];
                $contrasena = $_POST['contrasena'];
                $confirmar_contrasena = $_POST['confirmar_contrasena'];

                // Verificar si el correo ya existe en la base de datos
                $query = "SELECT correo FROM usuario WHERE correo = $1";
                $result = pg_query_params($conexion, $query, array($correo));
                $existeUsuario = pg_fetch_assoc($result);

                if ($existeUsuario) {
                    ?>
                    <div class="error">
                        <p><?php echo 'El correo ya está registrado'; ?></p>
                    </div>
                    <?php
                } elseif ($contrasena !== $confirmar_contrasena) {
                    ?>
                    <div class="error">
                        <p><?php echo 'Las contraseñas no coinciden'; ?></p>
                    </div>
                    <?php
                } else {
                    // Insertar datos en la base de datos
                    $queryInsert = "INSERT INTO usuario (correo, nombre, contrasena) 
                    VALUES ('$correo', '$nombre', '$contrasena')";

                    $ejecuta = pg_query($conexion, $queryInsert);
                    //header("location:../index.php"); 

                    if ($ejecuta) {
                        //echo "<p>¡Registro exitoso!</p>";
                        header("location: ../index.php");
                    } else {
                        ?>
                        <div class="error">
                            <p><?php echo 'Error al registrar el usuario'; ?></p>
                        </div>
                        <?php
                    }
                }
            }
        ?>
    </div>
</body>
</html>
