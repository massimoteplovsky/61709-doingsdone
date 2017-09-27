<?php 
    $projects_list = $templateData['projects'] ?? [];
    $projects = array_merge([['id' => 0, 'name' => 'Все']], $projects_list);
    $tasks = $templateData['tasks'] ?? [];
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

                            <?php foreach ($projects as $key => $value) : ?>

                                <?php 
                                    $add_class = '';
                                    if(isset($_GET['project']) && $_GET['project'] == $value['id']){
                                        $add_class = 'main-navigation__list-item--active';
                                    }
                                ?> 

                                <?php if($value['id'] == 0) : ?>
                                <li class="main-navigation__list-item <?php !isset($_GET['project']) ? print('main-navigation__list-item--active') : print(''); ?>">
                                    <a class="main-navigation__list-item-link" href="index.php"><?php htmlspecialchars(print($value['name'])); ?></a>
                                    <span class="main-navigation__list-item-count"><?php print(task_counter($tasks, $value['id'])); ?></span>
                                </li>

                                <?php else : ?>


                                <li class="main-navigation__list-item <?php print($add_class); ?>">
                                    <a class="main-navigation__list-item-link" href="index.php?project=<?php print($value['id']); ?>"><?php htmlspecialchars(print($value['name'])); ?></a>
                                    <span class="main-navigation__list-item-count"><?php print(task_counter($tasks, $value['id'])); ?></span>
                                </li>
                                <?php endif; ?>
                            <?php endforeach; ?>

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

    <script type="text/javascript" src="js/script.js"></script>
</body>
</html>
