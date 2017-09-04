<?php 
session_start();
error_reporting(E_ALL);
require_once "functions.php";

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$projects = ["Все", "Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];

$task_list = [["Задача" => "Собеседование в IT компании", "Дата выполнения" => "01.06.2018", "Категория" => "Работа", "Выполнен" => false],
["Задача" => "Выполнить тестовое задание", "Дата выполнения" => "25.05.2018", "Категория" => "Работа", "Выполнен" => false],
["Задача" => "Сделать задание первого раздела", "Дата выполнения" => "21.04.2018", "Категория" => "Учеба", "Выполнен" => true],
["Задача" => "Встреча с другом", "Дата выполнения" => "22.04.2018", "Категория" => "Входящие", "Выполнен" => false],
["Задача" => "Купить корм для кота", "Дата выполнения" => "-", "Категория" => "Домашние дела", "Выполнен" => false],
["Задача" => "Заказать пиццу", "Дата выполнения" => "-", "Категория" => "Домашние дела", "Выполнен" => false]]; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(isset($_FILES["preview"]) && $_FILES["preview"]["name"]){

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_name = $_FILES["preview"]['name'];
        $file_tmp_name = $_FILES["preview"]['tmp_name'];
        $file_size = $_FILES["preview"]['size'];
        $file_path = __DIR__ . '/uploads/';
        $file_type = $_FILES["preview"]['type'];

        if (($file_type !== 'image/gif' && $file_type !== 'image/png' && $file_type !== 'image/jpg') || ($file_size > 102400)) {
            print("Загрузите картинку в формате gif, png, jpg. Размер файла не должен быть более 100мб");
            exit();
        }

        if(is_uploaded_file($file_tmp_name)){
            move_uploaded_file($file_tmp_name, $file_path . $file_name);
        }
    
    }

    $required = ['name', 'project', 'date'];
    $errors = [];
    
    $_SESSION['fields'] = $_POST;

    foreach ($_POST as $key => $value) {

        if (in_array($key, $required) && $value == '') {
            $errors[] = $key;
        }

    }

    if(count($errors)){

        $_SESSION['errors'] = $errors;
        header("Location:index.php?add");

    } else {
        $_SESSION['errors'] = [];
        $subarray = [
        "Задача" => $_SESSION['fields']['name'],
        "Дата выполнения" => $_SESSION['fields']['date'],
        "Категория" => $_SESSION['fields']['project'],
        "Выполнен" => false
        ];

        array_unshift($task_list, $subarray);
    }
}



if(isset($_GET['add'])){

    $taskForm = renderTemplate('templates/add-task-form.php', ["projects" => $projects]);

    print($taskForm);

}

if(isset($_GET['project'])){

    $number = intval($_GET['project']);

    if($number){

        if(!isset($projects[$number])) {
            http_response_code(404);
        }

        $new_arr = [];
        $project = $projects[$number];

        foreach ($task_list as $key => $value) {
            if($value["Категория"] == $project){
                array_push($new_arr, $task_list[$key]);
            }
        }
    } else {
        $new_arr = $task_list;
    }
    
} else {
    $new_arr = $task_list;
}              

$page_content = renderTemplate('templates/index.php', ["tasks" => $new_arr, "complete_tasks" => $show_complete_tasks] );

$layout_content = renderTemplate('templates/layout.php', ["content" => $page_content, "title" => "Дела в порядке!", "user_name" => "Константин"]);

print($layout_content);





?>