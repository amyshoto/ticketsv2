<?php 
	$conexion = pg_connect("host=localhost dbname=tickets user=postgres password=S0p0rt31234");
	if($conexion){
	} else {
		echo "No funciona la conexion";
	}
?>