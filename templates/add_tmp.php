
<?= $section; ?>

<main class="content__main">
  <h2 class="content__main-heading">Добавление задачи</h2>

  <form class="form"  action="/add.php" method="post" autocomplete="off" enctype="multipart/form-data">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>

      <input class="form__input <?php if (array_key_exists("name", $errors)) { print("form__input--error"); } ?>" type="text" name="name" id="name" value="<?= $new_task_name; ?>" placeholder="Введите название">
      <?php if (array_key_exists("name", $errors)) : ?>
      <p class="form__message"><?= $errors["name"]; ?></p>
      <?php endif; ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>

      <select class="form__input form__input--select <?php if (array_key_exists("project", $errors)) { print("form__input--error"); } ?>" name="project" id="project">
          <?php foreach ($projects as $project) : ?>
          <option <?php if (htmlspecialchars($project["title"]) == $new_task_project) { print("selected"); } ?> value="<?= htmlspecialchars($project["title"]); ?>"><?= htmlspecialchars($project["title"]); ?></option>
          <?php endforeach; ?>
      </select>
      <?php if (array_key_exists("project", $errors)) : ?>
      <p class="form__message"><?= $errors["project"]; ?></p>
      <?php endif; ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Дата выполнения</label>

      <input class="form__input form__input--date <?php if (array_key_exists("date", $errors)) { print("form__input--error"); } ?>" type="text" name="date" id="date" value="<?= $new_task_date; ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
      <?php if (array_key_exists("date", $errors)) : ?>
      <p class="form__message"><?= $errors["date"]; ?></p>
      <?php endif; ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="file">Файл</label>

      <div class="form__input-file">
        <input class="visually-hidden" type="file" name="file" id="file" value="">

        <label class="button button--transparent" for="file">
          <span>Выберите файл</span>
        </label>
        <?php if (array_key_exists("file", $errors)) : ?>
        <p class="form__message"><?= $errors["file"]; ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="" value="Добавить">
    </div>
  </form>
</main>