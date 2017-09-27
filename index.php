<?php 
session_start();
error_reporting(E_ALL);
require_once "vendor/autoload.php";
require_once "functions.php";
require_once "db/init.php";

$task_list = [];

$users = select_data($con, "SELECT * FROM user");

if(isset($_SESSION['user'])){

    //Массив с данными из созданой сессии при регистрации
    $user = $_SESSION['user'];
    //Массив с данными по проектам из бд определенного пользователя
    $projects = select_data($con, "SELECT id, name FROM projects WHERE user_id = ?", [$user['id']]);
    //Массив с данными по задачам из бд определенного пользователя
    $task_list = select_data($con, "SELECT id, name, project_id, deadline, complete, file FROM tasks WHERE user_id = ?", [$user['id']]);
    
    //Действия над задачами
    require_once "task-actions.php";

    //Фильтрация задач
    if(isset($_GET['filter'])){
        $filter_type = $_GET['filter'];
        $task_list = filter_tasks($con, $filter_type, $user);
    }
}

//Валидация форм
require_once "form-validation.php";

//Показ модальных окон форм
require_once "show-forms.php";

//Показ задач для каждого проекта
require_once "tasks-counter.php";

//Показ выполненных задач
require_once "show-completed-tasks.php";

//Подключение header.php
$header_content = renderTemplate('templates/header.php');

//Подключение footer.php
$footer_content = renderTemplate('templates/footer.php');

//Подключение шаблонов
if(!isset($_SESSION['user'])){

    //Подключение страницы guest.php
    $guest_content = renderTemplate('templates/guest.php', ["header_content" => $header_content, "footer_content" => $footer_content, "title" => "Дела в порядке!", "form_fields" => $fields, "errors" => $errors, "show_form" => $show_form] );

    print($guest_content);

} else {

    //Подключение главной страницы index.php
    $page_content = renderTemplate('templates/index.php', ["tasks" => $project_task, "complete_tasks" => $showCompleted] );

    //Подключение базового шаблона layout.php
    $layout_content = renderTemplate('templates/layout.php', ["header_content" => $header_content, "footer_content" => $footer_content, "content" => $page_content, "tasks" => $task_list, "projects" => $projects, "title" => "Дела в порядке!", "show_form" => $show_form]);

    print($layout_content);
}

?>