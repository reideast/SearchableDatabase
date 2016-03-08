<?php
	//Config file for Monster Project database connection
	//Andrew East
	
	DEFINE ('DB_HOST', 'localhost');
	DEFINE ('DB_NAME', 'monster_project');
	DEFINE ('DB_USER', 'root');
	DEFINE ('DB_PASSWORD', '');
	$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die('Could not connect to MySQL'); //TOO MUCH INFO FOR SECURITY: ' . mysqli_connect_error());
?>