<!--модальное окно добавления задачи-->
<?php var_dump($templateData["errors"]) ?>
<?php print($_POST["project"]) ?>

<?php 

function checkErrors($errors_arr, $field){
  foreach ($errors_arr as $value) {
    if ($value == $field) {
      return true;
    }
  }
}

?>

<div class="modal">
  <button class="modal__close" type="button" name="button">Закрыть</button>

  <h2 class="modal__heading">Добавление задачи</h2>

  <form class="form" action="index.php" method="post">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>
      <?php checkErrors($templateData["errors"], "name") ? print("<span class='form__error'>Введите название задачи</span>") : print(""); ?>
      <input class="form__input <?php checkErrors($templateData["errors"], "name") ? print("form__input--error") : print(""); ?>" type="text" name="name" id="name" value="<?php isset($_POST['name']) ? print($_POST['name']) : ''; ?>" placeholder="Введите название">
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>

      <select class="form__input form__input--select <?php checkErrors($templateData["errors"], "project") ? print("form__input--error") : print(""); ?>" name="project" id="project">
        <option value="Входящие" <?php isset($_POST['project']) && $_POST['project'] == 'Входящие' ? print("selected") : print(""); ?>>Входящие</option>
        <option value="Работа" <?php isset($_POST['project']) && $_POST['project'] == 'Работа' ? print("selected") : print(""); ?>>Работа</option> 
      </select>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>

      <input class="form__input form__input--date <?php checkErrors($templateData["errors"], "date") ? print("form__input--error") : print(""); ?>" type="text" name="date" id="date" value="<?php isset($_POST['date']) ? print($_POST['date']) : ''; ?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
    </div>

    <div class="form__row">
      <label class="form__label">Файл</label>

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