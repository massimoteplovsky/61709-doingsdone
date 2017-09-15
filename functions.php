<?php

// Функция подключения шаблонов 
function renderTemplate($template, $templateData = []){

	if (!isset($template)) {
        return "";
    }

	ob_start();

    require_once $template;

    return ob_get_clean();
}

//Функция поиска пользователя по email
function searchUserByEmail($email, $users){
    $result = null;
    foreach ($users as $user) {
        if ($user['email'] == $email) {
            $result = $user;
            break;
        }
    }
    return $result;
}

//Функция удаления сессий
function unsetSession($session_name){
    foreach ($session_name as $key) {
            unset($_SESSION[$key]);
    }
}

//Функция проверки ошибок в формах
function checkErrors($errors_arr, $field){
  foreach ($errors_arr as $value) {
    if ($value == $field) {
      return true;
    }
  }
}

function sanitizeInput($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>