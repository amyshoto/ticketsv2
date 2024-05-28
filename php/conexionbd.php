<?php 
	$conexion = pg_connect("host=10.25.96.154 dbname=ticketsbd user=postgres password=S0p0rt31234");
	if($conexion){
	} else {
		echo "No funciona la conexion";
	}
?>
