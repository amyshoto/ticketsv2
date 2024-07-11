<?php 
    $conexion = pg_connect("host=127.0.0.1 port=5432 dbname=ticket user=postgres password=passwors");
    if(!$conexion){
        die("No funciona la conexión");
    }
?>
