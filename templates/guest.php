<?php error_reporting(E_ALL); ?>

<?php 

$errors = $templateData["errors"];
$email = $templateData["form_fields"]['email'] ?? '';
$password = $templateData["form_fields"]['password'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?php print($templateData["title"]); ?></title>
  <link rel="stylesheet" href="../css/normalize.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<!--class="overlay"-->
<body class="<?php isset($_GET['login']) || $templateData['show_form'] ? print('overlay') : print('body-background') ?>">
  <h1 class="visually-hidden">Дела в порядке</h1> 

  <div class="page-wrapper">
    <div class="container">

      <?php print($templateData["header_content"]);  ?>

      <div class="content">
        <section class="welcome">
          <h2 class="welcome__heading">«Дела в порядке»</h2>

          <div class="welcome__text">
            <p>«Дела в порядке» — это веб приложение для удобного ведения списка дел. Сервис помогает пользователям не забывать о предстоящих важных событиях и задачах.</p>

            <p>После создания аккаунта, пользователь может начать вносить свои дела, деля их по проектам и указывая сроки.</p>
          </div>

          <a class="welcome__button button" href="/register.php">Зарегистрироваться</a>
        </section>
      </div>
    </div>
  </div>

  <?php print($templateData["footer_content"]); ?>

  <div class="modal" <?php isset($_GET['login']) || $templateData['show_form'] ? print('') : print('hidden') ?>>
    <a href="/" class="modal__close">Закрыть</a>

    <h2 class="modal__heading">Вход на сайт</h2>

    <form class="form" class="" action="index.php" method="post">
      <div class="form__row">
        <label class="form__label" for="email">E-mail <sup>*</sup></label>

        <input class="form__input <?php isset($errors["email"]) ? print("form__input--error") : print(""); ?>" type="text" name="email" id="email" value="<?php print($email);?>" placeholder="Введите e-mail"> 
        <?php isset($errors["email"]) ? print("<span class='form__message'>".$errors['email']."</span>") : print(""); ?>
        
      </div>

      <div class="form__row">
        <label class="form__label" for="password">Пароль <sup>*</sup></label>

        <input class="form__input <?php isset($errors["password"]) ? print("form__input--error") : print(""); ?>" type="password" name="password" id="password" value="<?php print($password);?>" placeholder="Введите пароль">
        <?php isset($errors["password"]) ? print("<span class='form__message'>".$errors['password']."</span>") : print(""); ?>
      </div>

      <div class="form__row">
        <label class="checkbox">
          <input class="checkbox__input visually-hidden" type="checkbox" checked>
          <span class="checkbox__text">Запомнить меня</span>
        </label>
      </div>

      <div class="form__row form__row--controls">
        <input class="button" type="submit" name="login_form" value="Войти">
      </div>
    </form>
  </div>
</body>
</html>
