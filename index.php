<?php 
session_start();
error_reporting(E_ALL);
require_once "functions.php";
require_once "db/init.php";

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

// $result = select_data($con, 'SELECT * FROM user WHERE id = ?', [7]);
// print("Функция получения данных   ");
// print_r($result);

$result2 = insert_data($con, 'users', ['email' => 'abc@bca.rue', 'name' => 'neo777']);
print("Функция вставки данных ");
print_r($result2);

// $result3 = exec_query($con, 'UPDATE user SET name=? WHERE id = ?', ["Сергей", 7]);
// print("Произвольная функция ");
// print($result3);

//Флаг показа или скрытия всплывающих форм
$show_form = false;

//Валидация форм

$fields = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //Поля формы добавления задач
    if(isset($_POST["task_form"])){

        if(empty($_POST["name"])){
            $errors['name'] = "Введите название задачи";
        } else {
            $fields['name'] = sanitizeInput($_POST["name"]);
        }

        if(empty($_POST['project'])){
            $errors["project"] = "Выберите проект";
        } else {
            $fields["project"] = sanitizeInput($_POST["project"]);
        }

        if(empty($_POST["date"])){
            $errors["date"] = "Введите дату";
        } else {
            $fields["date"] = sanitizeInput($_POST["date"]);
            if (!preg_match("/^(\d{2})\.(\d{2})(?:\.(\d{4}))?$/", $fields["date"])) {
                $errors["date"] = "Введите дату в формате дд.мм.гг"; 
            } else {
                $today_date = date("d.m.y");
                if( $today_date >= $fields["date"] ){
                    $errors["date"] = "Дата окончания не может быть раньше текущей даты!";
                }
            }
            
        }
        
        //Валидация загруженного файла
        $fields['link'] = "";

        if (isset($_FILES['preview']) && $_FILES["preview"]["error"] == UPLOAD_ERR_OK) {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_tmp_name = $_FILES['preview']['tmp_name'];
            $file_name = $_FILES["preview"]['name'];
            $file_size = $_FILES['preview']['size'];
            $file_type = finfo_file($finfo, $file_tmp_name);
            $file_path = __DIR__ . '/uploads/';
            $file_url = $file_path . $file_name;

            if (($file_type !== 'image/gif' && $file_type !== 'image/png' && $file_type !== 'image/jpeg')) {
                $errors["file"] = "Загрузите картинку в формате gif, png, jpg.";
            }

            if ($file_size > 102400) {
                $errors["file"] = "Максимальный размер файла: 100мб";
            }

            if(is_uploaded_file($file_tmp_name)){
               move_uploaded_file($file_tmp_name , $file_path . $file_name);
               $fields['link'] = "<a class='download-link' href='$file_url'>$file_name</a>";
             } 
        }

        // Выполнение дествий формы добавления задач
        if($errors){
            $show_form = true;
            print(renderTemplate('templates/add-task-form.php', ["projects" => $projects, "form_fields" => $fields, "errors" => $errors]));

        } else {
            $subarray = [
            "Задача" => $fields['name'],
            "Дата выполнения" => $fields['date'],
            "Категория" => $fields['project'],
            "Загрузить" => $fields['link'],
            "Выполнен" => false
            ];

            array_unshift($task_list, $subarray);
        }

    }

    //Форма добавления проекта
    if(isset($_POST["project_form"])){

        if(empty($_POST["name"])){
            $errors['name'] = "Введите название проекта";
        } else {
            $fields['name'] = sanitizeInput($_POST["name"]);
        }

        // Выполнение дествий формы добадения проекта
        if($errors){

            $show_form = true;

            $projectForm = renderTemplate('templates/add-project-form.php', ["projects" => $projects, "form_fields" => $fields, "errors" => $errors]);

            print($projectForm);
        } else {
            array_push($projects, $fields['name']);
        }
    }

    //Форма логина
    if(isset($_POST["login_form"])){

        if(empty($_POST["email"])){
            $errors["email"] = "Введите e-mail";
        } else {
            $fields["email"] = sanitizeInput($_POST["email"]);
            if (!filter_var($fields["email"], FILTER_VALIDATE_EMAIL)) {
              $errors['email'] = "Неверный формат почты"; 
            }     
        }

        if(empty($_POST["password"])){
            $errors["password"] = "Введите пароль";
        } else {
            $fields["password"] = sanitizeInput($_POST["password"]);
        } 

        if(!empty($_POST["password"]) && !empty($_POST["email"])){
            if ($user = searchUserByEmail($fields["email"], $users)) {
               if (password_verify($fields["password"], $user['password'])) {
                    $_SESSION['user'] = $user;
                } else {
                   $errors["password"] = "Неверный пароль"; 
                }
            } else {
                if (!filter_var($fields["email"], FILTER_VALIDATE_EMAIL)) {
                  $errors['email'] = "Неверный формат почты"; 
                } else {
                    $errors["email"] = "Неверный e-mail";
                }  
          }
        }

        // Выполнение дествий формы логина
        if($errors){

            $show_form = true;

            $header_content = renderTemplate('templates/header.php');

            $footer_content = renderTemplate('templates/footer.php');

            $guest_content = renderTemplate('templates/guest.php', ["header_content" => $header_content, "title" => "Дела в порядке!", "form_fields" => $fields, "errors" => $errors, "show_form" => $show_form]);

            print($guest_content);
        } else {
            header("Location:index.php");
        }
    }
}

//Показ формы добавления задач
if(isset($_GET['add'])){
    
    $taskForm = renderTemplate('templates/add-task-form.php', ["projects" => $projects, "form_fields" => $fields, "errors" => $errors]);

    print($taskForm);

}

//Показ формы добавления проекта
if(isset($_GET['add_project'])){
    
    $projectForm = renderTemplate('templates/add-project-form.php', ["projects" => $projects, "form_fields" => $fields, "errors" => $errors]);

    print($projectForm);

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
    $page_content = renderTemplate('templates/index.php', ["tasks" => $new_arr, "complete_tasks" => $showCompleted] );

    //Подключение базового шаблона layout.php
    $layout_content = renderTemplate('templates/layout.php', ["header_content" => $header_content, "footer_content" => $footer_content, "content" => $page_content, "tasks" => $task_list, "projects" => $projects, "title" => "Дела в порядке!", "show_form" => $show_form]);

    print($layout_content);
}

?>