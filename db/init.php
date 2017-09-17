<?php
$con = mysqli_connect("localhost", "root", "", "doingsdone");
if ($con == false) {
	$error_connection = mysqli_connect_error();
	$error_template = renderTemplate('templates/error.php', ["error_connection" => $error_connection]);
	print($error_template);
	exit();
}
else {
	
}
