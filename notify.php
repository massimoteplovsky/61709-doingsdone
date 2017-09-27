<?php 
require_once "vendor/autoload.php";
require_once "db/init.php";
require_once "functions.php";

$timeFrom = date('Y-m-d H:i:s');
$timeTo = date('Y-m-d H:i:s', time() + 3600);

$query = select_data($con, "SELECT tasks.name, tasks.deadline, user.email, user.name as user
	FROM tasks
	LEFT JOIN user
	ON tasks.user_id = user.id
	WHERE tasks.deadline >= ?
	AND tasks.deadline <= ? ", [$timeFrom, $timeTo]);

$mail_info = [];

foreach ($query as $key => $value) {
	if (array_key_exists($value['email'], $mail_info)){
		$mail_info[$value['email']]['task'] .= "- " . $value['name'] . " на " . $value['deadline'] . ";";
	} else {
		$mail_info[$value['email']]['task'] = "- " . $value['name'] . " на " . $value['deadline'] . ";\n";
		$mail_info[$value['email']]['name'] = $value['user'];
	}

}

//Отправка почты
foreach ($mail_info as $key => $value){
	sendMail($key, message($value['name'], $value['task']));
}

//Получение текста сообщения
function message($name, $task){
	return "Уважаемый пользователь, $name. У Вас запланирована задача:\n$task\n";
}


function sendMail($email, $message){

	$transport = (new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl'))
	->setUsername('doingsdone@mail.ru')
	->setPassword('rds7BgcL')
	;

	$mailer = new Swift_Mailer($transport);

	$message = (new Swift_Message('Уведомление от сервиса «Дела в порядке»'))
	->setFrom('doingsdone@mail.ru')
	->setTo($email)
	->setBody($message);

	$result = $mailer->send($message);
}

?>