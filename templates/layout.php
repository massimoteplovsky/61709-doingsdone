<?php

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime('now midnight'); // текущая метка времени

// запишите сюда дату выполнения задачи в формате дд.мм.гггг
$date_deadline = date("d.m.Y", $task_deadline_ts);

// в эту переменную запишите кол-во дней до даты задачи
$days_until_deadline = ($task_deadline_ts - $current_ts) / 86400;

$task_counter = function($task_list, $project_name = "Все"){

    $counter = 0;

    foreach ($task_list as $key => $value) {
        if($value["Категория"] == $project_name || $project_name == "Все"){
            ++$counter;
        }
    }

    return $counter;
}    

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php print($templateData["title"]); ?></title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<!--class="overlay"-->
<body class="<?php isset($_GET['add']) || isset($_GET['add_project']) || $templateData['show_form'] ? print('overlay') : print('') ?>">
    <h1 class="visually-hidden">Дела в порядке</h1>

    <div class="page-wrapper">
        <div class="container container--with-sidebar">
            
           <?php print($templateData["header_content"]); ?>

            <div class="content">
                <section class="content__side">
                    <h2 class="content__side-heading">Проекты</h2>

                    <nav class="main-navigation">
                        <ul class="main-navigation__list">

                            <?php 

                            $count = count($templateData["projects"]);
                            $index = 0;

                            ?>
                            
                            <?php while( $index < $count ) :?>

                                <?php if($index == 0) :?>

                                    <li class="main-navigation__list-item main-navigation__list-item--active">
                                        <a class="main-navigation__list-item-link" href="index.php"><? echo $templateData["projects"][$index] ?></a>
                                        <span class="main-navigation__list-item-count"><?php echo $task_counter($templateData["tasks"], $templateData["projects"][$index]); ?></span>
                                    </li>

                                <?php else: ?>

                                    <li class="main-navigation__list-item">
                                        <a class="main-navigation__list-item-link" href="index.php?project=<?php print($index) ?>"><? echo $templateData["projects"][$index] ?></a>
                                        <span class="main-navigation__list-item-count"><?php echo $task_counter($templateData["tasks"], $templateData["projects"][$index]); ?></span>
                                    </li>

                                <?php endif ?>    
                                <?php $index++; ?>
                            <?php endwhile; ?>

                        </ul>
                    </nav>

                    <a class="button button--transparent button--plus content__side-button" href="/index.php?add_project">Добавить проект</a>
                </section>

                <main class="content__main">
                   <?php print($templateData["content"]); ?>
                </main>
            </div>
        </div>
    </div>
    
    <?php print($templateData["footer_content"]); ?>

    <div class="modal" hidden>
        <button class="modal__close" type="button" name="button">Закрыть</button>

        <h2 class="modal__heading">Добавление задачи</h2>

        <form class="form" class="" action="index.html" method="post">
            <div class="form__row">
                <label class="form__label" for="name">Название <sup>*</sup></label>

                <input class="form__input" type="text" name="name" id="name" value="" placeholder="Введите название">
            </div>

            <div class="form__row">
                <label class="form__label" for="project">Проект <sup>*</sup></label>

                <select class="form__input form__input--select" name="project" id="project">
                    <option value="">Входящие</option>
                </select>
            </div>

            <div class="form__row">
                <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>

                <input class="form__input form__input--date" type="text" name="date" id="date" value="" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
            </div>

            <div class="form__row">
                <label class="form__label" for="file">Файл</label>

                <div class="form__input-file">
                    <input class="visually-hidden" type="file" name="preview" id="preview" value="">

                    <label class="button button--transparent" for="preview">
                        <span>Выберите файл</span>
                    </label>
                </div>
            </div>

            <div class="form__row form__row--controls">
                <input class="button" type="submit" name="" value="Добавить">
            </div>
        </form>
    </div>

    <script type="text/javascript" src="js/script.js"></script>
</body>
</html>
