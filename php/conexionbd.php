<?php 
	$conexion = pg_connect("host=10.25.96.150 dbname=tickets user=postgres password=S0p0rt31234");
	if($conexion){
	} else {
		echo "No funciona la conexion";
	}
?>
