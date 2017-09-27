<!--модальное окно добавления задачи-->

<?php 

//Получение данных шаблона: ошибки, значения полей.
$errors = $templateData["errors"] ?? [];
$task_name = $templateData["form_fields"]['name'] ?? '';
$project_id = $templateData["form_fields"]['project'] ?? '';
$date = $templateData["form_fields"]['date'] ?? '';
$projects = array_merge([['id' => 0, 'name' => 'Выберите проект']], $templateData['projects']);

?>

<div class="modal">
  <a href="/index.php" class="modal__close">Закрыть</a>

  <h2 class="modal__heading">Добавление задачи</h2>
  <?php if(isset($errors) && $errors) : ?>
    <p class="error-massage">Пожалуйста, исправьте ошибки в форме</p>
  <?php endif; ?>
  <form class="form" action="index.php" method="post" enctype="multipart/form-data">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>
      <input class="form__input <?php isset($errors["name"]) ? print("form__input--error") : print(""); ?>" 
             type="text" 
             name="name" 
             id="name"
             value="<?php print($task_name); ?>"
             placeholder="Введите название">
      <?php isset($errors["name"]) ? print("<span class='form__message'>" . $errors['name'] . "</span>") : print(""); ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>
      
      <select class="form__input form__input--select <?php isset($errors["project"]) ? print("form__input--error") : print(""); ?>"
              name="project"
              id="project">

      <?php foreach ($projects as $key => $value): ?>
        <option value="<?php print($value['id']); ?>" <?php $project_id == $value['id'] ? print("selected") : print(""); ?>><?php print($value['name']); ?></option> 
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