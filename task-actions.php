<?php 

//Изменение статуса выполнения задачи
if(isset($_GET['is_complete'])){

    $is_exist = false;
    $task_id = $_GET['is_complete'];

    foreach ($task_list as $key => $value){
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

    //Удаление задачи
if(isset($_GET['delete'])){

    $is_exist = false;
    $task_id = $_GET['delete'];

    foreach ($task_list as $key => $value){
        if($value['id'] == $task_id){
            $is_exist = true;
        } 
    }

    if($is_exist){
        exec_query($con, 'DELETE FROM tasks WHERE id = ?', [$task_id]);
        header("Location:index.php");
    } else {
        header("HTTP/1.0 404 Not Found");
        exit("Не могу отобразить страницу");
    }
}

    //Дублирование задачи
if(isset($_GET['clone'])){

    $is_exist = false;
    $task_id = $_GET['clone'];
    $is_task_exist = select_data($con, 'SELECT * FROM tasks WHERE user_id = ? AND id = ?', [$user['id'], $task_id]);
    
    foreach ($is_task_exist as $key => $value) {
        if($value['id'] == $task_id){
            $is_exist = true;
        } 
    }

    extract($is_task_exist[0], EXTR_PREFIX_SAME, "wddx");

    if($is_exist){
        insert_data($con, 'tasks', ['user_id' => $user_id ,'name' => $name, 'complete' => $complete, 'deadline' => $deadline, 'project_id' => $project_id, 'file' => $file]);
        header("Location:index.php");
    } else {
        header("HTTP/1.0 404 Not Found");
        exit("Не могу отобразить страницу");
    }
}

?>