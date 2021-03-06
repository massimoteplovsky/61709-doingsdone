<?php 
session_start();
error_reporting(E_ALL);
require_once "functions.php";

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime('now midnight'); // текущая метка времени

// запишите сюда дату выполнения задачи в формате дд.мм.гггг
$date_deadline = date("d.m.Y", $task_deadline_ts);

// в эту переменную запишите кол-во дней до даты задачи
$days_until_deadline = ($task_deadline_ts - $current_ts) / 86400;

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$projects = ["Все", "Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];

$task_list = [
    [
    "Задача" => "Собеседование в IT компании",
    "Дата выполнения" => "01.06.2018",
    "Категория" => "Работа",
    "Выполнен" => false
    ],
    [
    "Задача" => "Выполнить тестовое задание",
    "Дата выполнения" => "25.05.2018",
    "Категория" => "Работа",
    "Выполнен" => false
    ],
    [
    "Задача" => "Сделать задание первого раздела",
    "Дата выполнения" => "21.04.2018",
    "Категория" => "Учеба",
    "Выполнен" => true
    ],
    [
    "Задача" => "Встреча с другом",

    "Дата выполнения" => "22.04.2018",
    "Категория" => "Входящие",
    "Выполнен" => false
    ],
    [
    "Задача" => "Купить корм для кота",
    "Дата выполнения" => "-",
    "Категория" => "Домашние дела",
    "Выполнен" => false
    ],
    [
    "Задача" => "Заказать пиццу",
    "Дата выполнения" => "-",
    "Категория" => "Домашние дела",
    "Выполнен" => false
    ]
];

$users = [
    [
    'email' => 'ignat.v@gmail.com',
    'name' => 'Игнат',
    'password' => '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka'
    ],
    [
    'email' => 'kitty_93@li.ru',
    'name' => 'Леночка',
    'password' => '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa'
    ],
    [
    'email' => 'warrior07@mail.ru',
    'name' => 'Руслан',
    'password' => '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW'
    ]
];


//Валидация формы добавления задач

if (isset($_POST["task_form"])) {

    $required = ['name', 'project', 'date'];
    $errors = [];
    $rules = ['date' => 'validateDate'];
    $_SESSION['task_form_fields'] = $_POST;

    foreach ($_SESSION['task_form_fields'] as $key => $value) {

        if (in_array($key, $required) && $value == '') {
            $errors[] = $key;
        }

    }

    if(count($errors)){

        $_SESSION['errors'] = $errors;
        header("Location:index.php?add");

    } else {
        $subarray = [
            "Задача" => $_SESSION['task_form_fields']['name'],
            "Дата выполнения" => $_SESSION['task_form_fields']['date'],
            "Категория" => $_SESSION['task_form_fields']['project'],
            "Выполнен" => false
        ];

        array_unshift($task_list, $subarray);

        $_SESSION['errors'] = [];
        $_SESSION['task_form_fields'] = [];
    }
}

//Валидация формы входа
if (isset($_POST["login_form"])) {

    $required = ['email', 'password'];
    $errors = [];
    $_SESSION['login_form_fields'] = $_POST;

    foreach ($_SESSION['login_form_fields'] as $key => $value) {

        if (in_array($key, $required) && $value == '') {
            $errors[] = $key;
        }

    }

    if(count($errors)){

        $_SESSION['errors'] = $errors;
        unsetSession(['badmail', 'badpassword']);
        header("Location:index.php?login");

    } else if(!empty($_POST)) {

        $_SESSION['errors'] = [];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($user = searchUserByEmail($email, $users)) {

            unsetSession(['badmail']);

            if (password_verify($password, $user['password'])) {

                unsetSession(['badpassword']);
                $_SESSION['login_form_fields'] = [];
                $_SESSION['user'] = $user;

            }else{

                $_SESSION['badpassword'] = "Вы ввели неверный пароль";
                header("Location:index.php?login");

            }

        } else {

            $_SESSION['badmail'] = "Неверный email";
            header("Location:index.php?login");

        }
    }
}

//Проверка загруженного файла
if(isset($_FILES["preview"]['tmp_name']) && $_FILES["preview"]["error"] == UPLOAD_ERR_OK){

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

//Показ формы добавления задач
if(isset($_GET['add'])){
   
    $taskForm = renderTemplate('templates/add-task-form.php', ["projects" => $projects]);

    print($taskForm);

}

//Показ задач для каждого проекта
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

//Показ выполненных задач
$showCompleted = false;
if (isset($_GET['show_completed'])) {
    $showCompleted = sanitizeInput($_GET['show_completed']);
    setcookie('show_completed', $showCompleted, strtotime("+30 days"));
} else if (isset($_COOKIE['show_completed'])) {
    $showCompleted = $_COOKIE['show_completed'];
}

//Подключение header.php
$header_content = renderTemplate('templates/header.php', ["user" => $_SESSION['user']]);

//Подключение шаблонов
if(!$_SESSION['user']){
    //Подключение страницы guest.php
    $guest_content = renderTemplate('templates/guest.php', ["header_content" => $header_content, "title" => "Дела в порядке!"] );
    print($guest_content);

} else {

    //Подключение главной страницы index.php
    $page_content = renderTemplate('templates/index.php', ["tasks" => $new_arr, "complete_tasks" => $showCompleted] );

    //Подключение базового шаблона layout.php
    $layout_content = renderTemplate('templates/layout.php', ["header_content" => $header_content, "content" => $page_content, "tasks" => $task_list, "projects" => $projects, "title" => "Дела в порядке!", "user_name" => "Константин"]);

    print($layout_content);
}








?>