<?php 
    $conexion = pg_connect("host=10.25.96.155 port=5432 dbname=dbtickets user=postgres password=");
    if(!$conexion){
        die("No funciona la conexión");
    }
?>
