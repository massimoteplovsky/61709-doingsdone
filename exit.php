<?php 
require_once "functions.php";

session_start();

if(isset($_SESSION['task_form_fields'])){
	$_SESSION['task_form_fields'] = [];
	$_SESSION['errors'] = [];
}

if(isset($_SESSION['login_form_fields'])){
	$_SESSION['login_form_fields'] = [];
	$_SESSION['errors'] = [];
	unsetSession(['badmail', 'badpassword']);
}

header("Location:/")

?>