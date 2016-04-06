<?php
//404 Not Found 
	$name = $_SERVER['SCRIPT_NAME'];
	header("HTTP/1.0 404 Not Found");
	echo '<!DOCTYPE html><html><head><title>404 Not Found</title>';
	echo '</head><body><h1>Not Found</h1>';
	echo "<p>The requested URL {$name} was not found on this server.</p></body></html>";
	exit();
?>