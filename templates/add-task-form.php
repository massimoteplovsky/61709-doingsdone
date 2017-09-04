<!--модальное окно добавления задачи-->

<?php 

$fields = $_SESSION['fields'];
$name = $fields['name'] ?? '';
$project = $fields['project'] ?? '';
$date = $fields['date'] ?? '';
$errors = $_SESSION['errors'];

print_r($project);

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
      <?php checkErrors($errors, "name") ? print("<span class='form__error'>Введите название задачи</span>") : print(""); ?>
      <input class="form__input <?php checkErrors($errors, "name") ? print("form__input--error") : print(""); ?>" type="text" name="name" id="name" value="<?php print($name); ?>" placeholder="Введите название">
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>
      <?php checkErrors($errors, "project") ? print("<span class='form__error'>Выберите проект</span>") : print(""); ?>
      <select class="form__input form__input--select <?php checkErrors($errors, "project") ? print("form__input--error") : print(""); ?>" name="project" id="project">
      <?php foreach ($templateData['projects'] as $value): ?>
        <?php if($value == "Все") { continue;} ?>
        <option value="<?php print($value); ?>" <?php $project == $value ? print("selected") : print(""); ?>><?php print($value); ?></option> 
      <?php endforeach ?>
      </select>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>
      <?php checkErrors($errors, "date") ? print("<span class='form__error'>Введите дату</span>") : print(""); ?>
      <input class="form__input form__input--date <?php checkErrors($errors, "date") ? print("form__input--error") : print(""); ?>" type="text" name="date" id="date" value="<?php print($date); ?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
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