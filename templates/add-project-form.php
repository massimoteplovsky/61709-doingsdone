
<!--модальное окно добавления проекта-->

<?php 

$errors = $templateData["errors"];
$project_name = $templateData["form_fields"]['name'] ?? '';

?>

<div class="modal" <?php isset($_GET['add_project']) || $templateData['show_form'] ? print("") : print("hidden") ?>>
  <a href="/index.php" class="modal__close">Закрыть</a>

  <h2 class="modal__heading">Добавление проекта</h2>

  <form class="form" method="post" action="index.php">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>

      <input class="form__input <?php isset($errors["name"]) ? print("form__input--error") : print(""); ?>" type="text" name="name" id="project_name" value="<?php print($project_name); ?>" placeholder="Введите название">
      <?php isset($errors["name"]) ? print("<span class='form__message'>".$errors['name']."</span>") : print(""); ?>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="project_form" value="Добавить">
    </div>
  </form>
</div>