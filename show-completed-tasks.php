<?php 

$showCompleted = false;

if (isset($_GET['show_completed'])){

	$showCompleted = sanitizeInput($_GET['show_completed']);
	setcookie('show_completed', $showCompleted, strtotime("+30 days"));

} else if (isset($_COOKIE['show_completed'])){
	$showCompleted = $_COOKIE['show_completed'];
}


?>