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
            while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                $db_data[] = $row;
            }
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

        $str = substr($str,0,-2);

        return $str;
    }

    $sql = "INSERT INTO $table (". implode(', ', $col) . ") VALUES (" . placeholders($field) . ")";   

    $stmt = db_get_prepare_stmt($link, $sql, $field);
   
    if($stmt){
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return mysqli_insert_id($link);
    } else {
        return null;
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

function task_counter($tasks, $project_id){

    $counter = 0;

    foreach ($tasks as $key => $value) {
        if($value["project_id"] == $project_id || $project_id == 0){
            ++$counter;
        }
    }

    return $counter;
}

function unset_sessions($array){
    foreach ($array as $value) {
        unset($_SESSION[$value]);
    }
}

function check_date($str){
    $translate = [
        'сегодня' => strtotime('23:59:59'),
        'завтра' => time() + 86400,
        'послезавтра' => time() + 172800,
        'понедельник' => strtotime('Monday'),
        'вторник' => strtotime('Tuesday'),
        'среда' => strtotime('Wednesday'),
        'четверг' => strtotime('Thursday'),
        'пятница' => strtotime('Friday'),
        'суббота' => strtotime('Saturday'),
        'воскресенье' => strtotime('Sunday')
    ];
    $pattern = '(((\d{2})\.(\d{2})\.(\d{4}))|' . implode('|', array_keys($translate)) . ')(\s+в\s+((\d{2}):(\d{2})))?';
    $matches = [];
    $matched = preg_match("/^$pattern$/", mb_strtolower($str), $matches);
    if (!$matched) {
        return false;
    }
    if (isset($matches[8]) && (int)$matches[8] > 23) {
        return false;
    }
    if (isset($matches[9]) && (int)$matches[9] > 59) {
        return false;
    }
    $date = $matches[1];
    if (isset($translate[$date])) {
        $date = date('Y-m-d', $translate[$date]);
    } else {
        $date = $matches[5] . '-' . $matches[4] . '-' . $matches[3];
    }
    //Подставляю время в зависимости от текущего времени и переданной строки
    if (isset($matches[7])) {
        $time = $matches[7];
    } else if ($date == date('Y-m-d', time())) {
        $time = "23:59:59";
    } else {
        $time = date('H:i:s', time());
    }
    $result = "$date $time";
    return ($result >= date('Y-m-d H:i:s', time())) ? $result : null;
}

function filter_tasks($con, $filter_type, $user){
        switch ($filter_type) {
            case 'today' :
                return select_data($con, "SELECT id, name, project_id, complete, deadline FROM tasks 
                                          WHERE user_id = ? AND DATE_FORMAT(deadline, '%Y-%m-%d') = CURDATE()", [$user['id']]);
                break;
            case 'tomorrow':
                return select_data($con, "SELECT id, name, project_id, complete, DATE_FORMAT(deadline, '%Y-%m-%d %H:%i') as deadline FROM tasks 
                                          WHERE user_id = ? AND DATE_FORMAT(deadline, '%Y-%m-%d') = DATE_ADD(CURDATE(), INTERVAL + 1 DAY)", [$user['id']]);
                break;
            case 'overdue':
                return select_data($con, "SELECT id, name, project_id, deadline, complete FROM tasks 
                                          WHERE user_id = ? AND deadline < CURDATE() AND complete != 1", [$user['id']]);
                break;
            default: 
                header("Location: index.php");    
        }


    }

?>