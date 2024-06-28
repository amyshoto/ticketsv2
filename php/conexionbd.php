<?php 
	$conexion = pg_connect("host=10.25.96.155 port=5432 dbname=dbtickets user=postgres password=");
	if($conexion){
	} else {
		echo "No funciona la conexion1";
	}
?>