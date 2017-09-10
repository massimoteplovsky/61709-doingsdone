<!--модальное окно добавления задачи-->

<?php 

$errors = $templateData["errors"];
$task_name = $templateData["form_fields"]['name'] ?? '';
$project_name = $templateData["form_fields"]['project'] ?? '';
$date = $templateData["form_fields"]['date'] ?? '';


?>

<div class="modal">
  <a href="/exit.php" class="modal__close">Закрыть</a>

  <h2 class="modal__heading">Добавление задачи</h2>

  <form class="form" action="index.php" method="post" enctype="multipart/form-data">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>
      <input class="form__input <?php isset($errors["name"]) ? print("form__input--error") : print(""); ?>" 
             type="text" 
             name="name" 
             id="name"
             value="<?php print($task_name); ?>"
             placeholder="Введите название">
      <?php isset($errors["name"]) ? print("<span class='form__message'>".$errors['name']."</span>") : print(""); ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>
      
      <select class="form__input form__input--select <?php isset($errors["project"]) ? print("form__input--error") : print(""); ?>"
              name="project"
              id="project">
        <option value="" selected>Выберите проект</option>
      <?php foreach ($templateData['projects'] as $value): ?>
        <?php if($value == "Все") { continue;} ?>
        <option value="<?php print($value); ?>" <?php $project_name == $value ? print("selected") : print(""); ?>><?php print($value); ?></option> 
      <?php endforeach ?>
      </select>
      <?php isset($errors["project"]) ? print("<span class='form__message'>".$errors['project']."</span>") : print(""); ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>
      
      <input class="form__input form__input--date <?php isset($errors["date"]) ? print("form__input--error") : print(""); ?>" 
             type="text" 
             name="date" 
             id="date" 
             value="<?php print($date); ?>" 
             placeholder="Введите дату в формате ДД.ММ.ГГГГ">
      <?php isset($errors["date"]) ? print("<span class='form__message'>".$errors['date']."</span>") : print(""); ?>
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
      <input class="button" type="submit" name="task_form" value="Добавить">
    </div>
  </form>
</div>