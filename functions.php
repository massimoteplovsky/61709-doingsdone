<?php
require_once "mysql_helper.php";

function select_data($link, $sql, $data = []){

    $db_data = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);

    if($stmt){
        mysqli_stmt_execute($stmt);

        $res = mysqli_stmt_get_result($stmt);
        if (!mysqli_num_rows($res)) {
            return $db_data;
        }

        if($res){
            $db_data = mysqli_fetch_assoc($res); 
        }
        mysqli_stmt_close($stmt);
        return $db_data;
    }

    return $db_data;
    
}

function insert_data($link, $table, $data){

    $col = [];
    $field = [];

    foreach ($data as $key => $value) {
        $col[] = $key;
        $field[] = $value;
    }

    function placeholders($field){
        $str = "";
        for( $i = 0; $i<count($field); $i++){
            $str .= "?, ";
        }

        return $str = substr($str,0,-2);
    }

    $sql = "INSERT INTO $table (". implode(', ', $col) . ") VALUES (" . placeholders($field) . ")";

    $stmt = db_get_prepare_stmt($link, $sql, $field);
   
    if($stmt){
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return mysqli_insert_id($link);
    } else {
        return false;
    } 
    
}

function exec_query($link, $sql, $data = []){

    $stmt = db_get_prepare_stmt($link, $sql, $data);

    if($stmt){
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return true;
    } else {
        return false;
    }
    
}

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