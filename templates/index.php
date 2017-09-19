<?php 

$tasks = $templateData["tasks"];  

?>

<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <div class="radio-button-group">
        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" checked="">
            <span class="radio-button__text">Все задачи</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio">
            <span class="radio-button__text">Повестка дня</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio">
            <span class="radio-button__text">Завтра</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio">
            <span class="radio-button__text">Просроченные</span>
        </label>
    </div>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <?php
            $checked = '';
            $hidden = 'hidden';

            if($templateData["complete_tasks"]){
                $checked = 'checked';
                $hidden = '';
            }
        ?>
        <input id="show-complete-tasks" class="checkbox__input visually-hidden" type="checkbox" <?php print($checked); ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">

    <?php foreach ($tasks as $key => $value) :?>

        <?php
            $complete_task = '';
            $show_complete_tasks = '';

            if($value['complete']) {
                $complete_task = 'task--completed';
                $show_complete_tasks = $hidden;
            }
        ?>

        <tr class="tasks__item task <?php print($complete_task); ?>" <?php print($show_complete_tasks); ?>>
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden" type="checkbox">
                    <span class="checkbox__text"><?php print(htmlspecialchars($value["name"])); ?></span>
                </label>
            </td>

            <!-- <td class="task__file">
            <?php
               if(!empty($value["Загрузить"])){
                    print($value["Загрузить"]);
                }

            ?>

            </td> -->

            <td class="task__date" >
                <!--выведите здесь дату выполнения задачи-->
                <?php print(htmlspecialchars(date("d.m.Y", strtotime($value["deadline"])))); ?>
            </td>

            <td class="task__controls">
                <button class="expand-control" type="button" name="button">Выполнить первое задание</button>

                <ul class="expand-list hidden">
                    <li class="expand-list__item">
                        <a href="/index.php?is_complete=<?php print($value['id']); ?>">
                            <?php $value['complete'] ? print("Выполнить") : print("Выполнено") ?>
                        </a>
                    </li>

                    <li class="expand-list__item">
                        <a href="/delete-task.php?id=<?php print($value['id']); ?>">Удалить</a>
                    </li>
                </ul>
            </td>
        </tr>      
    <?php endforeach; ?>

</table>