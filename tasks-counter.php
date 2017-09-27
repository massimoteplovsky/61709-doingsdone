<?php 

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

?>