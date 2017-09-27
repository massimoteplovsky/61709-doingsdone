<?php 

$tasks = $templateData["tasks"] ?? []; 

?>

<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php?search" method="post">
    <input class="search-form__input" type="text" name="search-field" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="search_form" value="Искать">
</form>

<div class="tasks-controls">
    <div class="radio-button-group">
        <label class="radio-button">
            <input class="radio-button__input visually-hidden" onclick="window.location.href='index.php';" 
                   type="radio" name="radio" <?php isset($_GET['filter']) ? print("") : print("checked"); ?>>
            <span class="radio-button__text">Все задачи</span>
        </label>

        <label class="radio-button"> 
            <input class="radio-button__input visually-hidden" onclick="window.location.href='index.php?filter=today';"
                   type="radio"   
                   name="radio" <?php isset($_GET['filter']) && $_GET['filter'] == 'today' ? print("checked") : print(""); ?>>
            <span class="radio-button__text">Повестка дня</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" onclick="window.location.href='index.php?filter=tomorrow';"
                   type="radio" 
                   name="radio" <?php isset($_GET['filter']) && $_GET['filter'] == 'tomorrow' ? print("checked") : print(""); ?>>
            <span class="radio-button__text">Завтра</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" onclick="window.location.href='index.php?filter=overdue';"
                   type="radio" 
                   name="radio" <?php isset($_GET['filter']) && $_GET['filter'] == 'overdue' ? print("checked") : print(""); ?>>
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
                    <input class="checkbox__input visually-hidden" onclick="location.href='/index.php?is_complete=<?php print($value['id']); ?>'" type="checkbox">
                    <span class="checkbox__text"><?php print(htmlspecialchars($value["name"])); ?></span>
                </label>
            </td>

            
            <td class="task__file">
                <?php if(!empty($value["file"])) :?>
                    <a class="download-link" href="<?php print($value["file"]); ?>"><?php print(basename($value['file'])) ?></a>
                <?php endif;  ?>
            </td>
            

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
                        <a href="/index.php?delete=<?php print($value['id']); ?>">Удалить</a>
                    </li>

                    <li class="expand-list__item">
                        <a href="/index.php?clone=<?php print($value['id']); ?>">Дублировать</a>
                    </li>
                </ul>
            </td>
        </tr>      
    <?php endforeach; ?>

</table>

