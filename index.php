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

$task_list = [];

if(isset($_SESSION['user'])){
    $user = $_SESSION['user'];
    $projects = select_data($con, "SELECT id, name FROM projects WHERE user_id = ?", [$user['id']]);
    $task_list = select_data($con, "SELECT id, name, project_id, deadline, complete FROM tasks WHERE user_id = ?", [$user['id']]);

    if(isset($_GET['is_complete'])){
        $is_exist = false;
        $task_id = $_GET['is_complete'];
        $is_task_exist = select_data($con, 'SELECT id, complete FROM tasks WHERE user_id = ?', [$user['id']]);

        foreach ($is_task_exist as $key => $value) {
            if($value['id'] == $task_id){
                $is_completed = $value['complete'];
                $is_exist = true;
            } 
        }

        if($is_exist){
            $is_completed ? $is_completed = 0 : $is_completed = 1;
            exec_query($con, 'UPDATE tasks SET complete=? WHERE id = ?', [$is_completed, $task_id]);
            header("Location:index.php");
        } else {
            header("HTTP/1.0 404 Not Found");
            exit("Не могу отобразить страницу");
        }
    }
}

// $result = select_data($con, 'SELECT * FROM user WHERE id = ?', [7]);
// print("Функция получения данных   ");
// print_r($result);

// $result2 = insert_data($con, 'users', ['email' => 'abc@bca.rue', 'name' => 'neo777']);
// print("Функция вставки данных ");
// print_r($result2);

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
            $formattedDate = check_date($fields["date"]);
            if (!$formattedDate) {
                $errors["date"] = "Введите дату в формате дд.мм.гг. Введенная дата не должна быть раньше текущей даты!"; 
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
            insert_data($con, 'tasks', ['user_id' => $user['id'] ,'name' => $fields['name'], 'complete' => 0, 'deadline' => $formattedDate, 'project_id' => $fields['project'], 'file' => $fields['link']]);
            header("Location:index.php");
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

            $projectForm = renderTemplate('templates/add-project-form.php', ["show_form" => $show_form, "form_fields" => $fields, "errors" => $errors]);

            print($projectForm);
        } else {
            insert_data($con, 'projects', ['user_id' => $user['id'] ,'name' => $fields['name']]);
            header("Location:index.php");
        }
    }

    //Форма логина
    if(isset($_POST["login_form"])){

        $users = select_data($con, "SELECT * FROM user");

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
                $errors["email"] = "Неверный e-mail";
            }
        }

        // Выполнение дествий формы логина
        if($errors){
            
            $show_form = true;

            unset_sessions(['login_title']);

            $header_content = renderTemplate('templates/header.php');

            $footer_content = renderTemplate('templates/footer.php');

            $guest_content = renderTemplate('templates/guest.php', ["header_content" => $header_content, "title" => "Дела в порядке!", "form_fields" => $fields, "errors" => $errors, "show_form" => $show_form]);

            print($guest_content);
        } else {
            unset_sessions(['login_title']);
            header("Location:index.php");
        }
    }

    //Форма регистрации
    if(isset($_POST["registration_form"])){
        
        if(empty($_POST["email"])){
            $errors["email"] = "Введите e-mail";
        } else {
            $fields["email"] = sanitizeInput($_POST["email"]);
            if (!filter_var($fields["email"], FILTER_VALIDATE_EMAIL)) {
              $errors['email'] = "Неверный формат почты"; 
            }
            if ($user = searchUserByEmail($fields["email"], $users)) {
                $errors['email'] = "Почта уже используется!";
            }
        }

        if(empty($_POST["password"])){
            $errors["password"] = "Введите пароль";
        } else {
            $fields["password"] = sanitizeInput($_POST["password"]);
        } 

        if(empty($_POST["name"])){
            $errors["name"] = "Введите имя";
        } else {
            $fields["name"] = sanitizeInput($_POST["name"]);
        } 

        // Выполнение дествий формы регистрации
        if($errors){

            $_SESSION['errors'] = $errors;
            $_SESSION['fields'] = $fields;
     
            header("Location: register.php");
            exit();
        } else {
            $_SESSION['login_title'] = "Теперь вы можете войти, используя свой email и пароль";
            insert_data($con, 'user', ['email' => $fields['email'], 'name' => $fields['name'], 'password' => password_hash($fields['password'], PASSWORD_DEFAULT)]);
            header("Location: index.php?login");
            exit();
        }
    }
}

//Удаление ссесий форм
unset_sessions(['errors', 'fields']);

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
$project_task = [];

if(isset($_GET['project'])){

    $project_id = intval($_GET['project']);

    if($project_id){

        $id_in_projects_array = 0;

        foreach ($projects as $key => $value) {
            if($value["id"] == $project_id){
                ++$id_in_projects_array;
            }  
        } 

        if(!$id_in_projects_array){
            http_response_code(404);
            exit("Не могу отобразить страницу. Передано неверное значение параметра!!!");
        }

        foreach ($task_list as $key => $value) {
            if($value["project_id"] == $project_id){
                array_push($project_task, $value);
            }
        }
    } else {
        http_response_code(404);
        exit("Не могу отобразить страницу. Передано строковое значение параметра!!!");
    }
    
} else {
    $project_task = $task_list;
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
    $page_content = renderTemplate('templates/index.php', ["tasks" => $project_task, "complete_tasks" => $showCompleted] );

    //Подключение базового шаблона layout.php
    $layout_content = renderTemplate('templates/layout.php', ["header_content" => $header_content, "footer_content" => $footer_content, "content" => $page_content, "tasks" => $task_list, "projects" => $projects, "title" => "Дела в порядке!", "show_form" => $show_form]);

    print($layout_content);
}

?>