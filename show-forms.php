<?php 

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

?>