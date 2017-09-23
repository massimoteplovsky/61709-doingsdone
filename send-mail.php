<?php 

$user_data = $_SESSION['user'];
$timeFrom = date('Y-m-d H:i:s');
$timeTo = date('Y-m-d H:i:s', time() + 3600);

$query = select_data($con, "SELECT * FROM tasks WHERE user_id = ? AND deadline >= ? AND deadline <= ?", [$user_data['id'], $timeFrom, $timeTo]);

print_r($query);

?>