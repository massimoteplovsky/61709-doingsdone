<?php 

//Флаг показа или скрытия всплывающих форм
$show_form = false;

//Валидация форм
$fields = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //Форма добавления задач
    if(isset($_POST["task_form"])){

        if(empty($_POST["name"])){
            $errors['name'] = "Введите название задачи";
        } else {
            $fields['name'] = $_POST["name"];
        }

        if(empty($_POST['project'])){
            $errors["project"] = "Выберите проект";
        } else {
            $fields["project"] = sanitizeInput($_POST["project"]);
        }

        if(empty($_POST["date"])){
            $errors["date"] = "Введите дату";
        } else {
            $fields["date"] = $_POST["date"];
            $formattedDate = check_date($fields["date"]);
            if (!$formattedDate) {
                $errors["date"] = "Введите дату в формате дд.мм.гг. Введенная дата не должна быть раньше текущей даты!"; 
            }  
        }
        
        //Валидация загруженного файла
        $fields['link'] = "";

        if(isset($_FILES['preview']) && $_FILES["preview"]["error"] == UPLOAD_ERR_OK) {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_tmp_name = $_FILES['preview']['tmp_name'];
            $file_name = $_FILES["preview"]['name'];
            $file_size = $_FILES['preview']['size'];
            $file_type = finfo_file($finfo, $file_tmp_name);
            $file_path = __DIR__ . '/uploads/';
            $file_url = $file_path . $file_name;

            if(($file_type !== 'image/gif' && $file_type !== 'image/png' && $file_type !== 'image/jpeg')) {
                $errors["file"] = "Загрузите картинку в формате gif, png, jpg.";
            }

            if($file_size > 102400) {
                $errors["file"] = "Максимальный размер файла: 100мб";
            }

            if(is_uploaded_file($file_tmp_name)){
               move_uploaded_file($file_tmp_name , $file_path . $file_name);
               $fields['link'] = "/uploads/$file_name";
             } 
        }

        // Выполнение действий формы добавления задач
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
            $fields['name'] = $_POST["name"];
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
            if($user = searchUserByEmail($fields["email"], $users)){
               if(password_verify($fields["password"], $user['password'])){
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

            $guest_content = renderTemplate('templates/guest.php', ["header_content" => $header_content, "footer_content" => $footer_content, "title" => "Дела в порядке!", "form_fields" => $fields, "errors" => $errors, "show_form" => $show_form]);

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
    
    //Форма поиска
    if(isset($_POST["search_form"])){
        if(!empty($_POST["search-field"])){

            $search_value = sanitizeInput($_POST["search-field"]);
            $query = select_data($con, "SELECT * FROM tasks WHERE name LIKE '$search_value'");

            if($query){
                $task_list = $query;
                $project_task = $task_list;
            } else {
                $task_list = $task_list;
            }
        } else {
            header("Location: index.php");
        }
    }
}

//Удаление сесcий форм
unset_sessions(['errors', 'fields']);

?>