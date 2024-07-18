<?php
// index.php
// Conexión a la base de datos
include 'php/conexionbd.php';

// Obtener la URL solicitada
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Analizar la URL y redirigir a los archivos correspondientes
switch ($url) {
    case 'index':
        include 'php/login.php';
        break;
    case 'admin':
        include 'php/indexAdmin.php';
        break;
    case 'superadmin':
        include 'php/indexSuperadmin.php';
        break;
    case 'usuario':
        include 'php/indexUsuario.php';
        break;
    case 'encargado':
        include 'php/encargado.php';
        break;
    default:
        // Puedes incluir una página de error o redirigir a una página por defecto
        include '404.html';
        break;
}
?>
