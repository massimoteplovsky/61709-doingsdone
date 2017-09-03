<?php 
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

$required = ['name', 'project', 'date'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    foreach ($_POST as $key => $value) {

        if (in_array($key, $required) && $value == '') {
            $errors[] = $key;
            break;
        }

    }

    if(count($errors)){

        print(renderTemplate('templates/add-task-form.php', ["errors" => $errors]));
        
    }
}   

if(isset($_GET['add'])){

    $taskForm = renderTemplate('templates/add-task-form.php', ["errors" => $errors]);

    print($taskForm);

} 

if(isset($_GET['project'])){

    $number = intval($_GET['project']);

    if($number){

        if(!isset($projects[$number])){
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