<?php 
	$conexion = pg_connect("host=localhost dbname=tickets user=postgres password=1234");
	if($conexion){
	} else {
		echo "No funciona la conexion pipipi";
	}
?>
