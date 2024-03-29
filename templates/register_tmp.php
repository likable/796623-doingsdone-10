<section class="content__side">
    <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

    <a class="button button--transparent content__side-button" href="/auth.php">Войти</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Регистрация аккаунта</h2>

    <form class="form" action="/register.php" method="post" autocomplete="off">
      <div class="form__row">
        <label class="form__label" for="email">E-mail <sup>*</sup></label>

        <input class="form__input <?php if ($errors["email"]) { print("form__input--error"); } ?>" type="text" name="email" id="email" value="<?= $new_user_email; ?>" placeholder="Введите e-mail">

        <?php if (array_key_exists("email", $errors)) : ?>
        <p class="form__message"><?= $errors["email"]; ?></p>
        <?php endif; ?>
      </div>

      <div class="form__row">
        <label class="form__label" for="password">Пароль <sup>*</sup></label>

        <input class="form__input <?php if ($errors["password"]) { print("form__input--error"); } ?>" type="password" name="password" id="password" value="<?= $new_user_password; ?>" placeholder="Введите пароль">
        
        <?php if (array_key_exists("password", $errors)) : ?>
        <p class="form__message"><?= $errors["password"]; ?></p>
        <?php endif; ?>
      </div>

      <div class="form__row">
        <label class="form__label" for="name">Имя <sup>*</sup></label>

        <input class="form__input <?php if ($errors["name"]) { print("form__input--error"); } ?>" type="text" name="name" id="name" value="<?= $new_user_name; ?>" placeholder="Введите имя">
        
        <?php if (array_key_exists("name", $errors)) : ?>
        <p class="form__message"><?= $errors["name"]; ?></p>
        <?php endif; ?>
      </div>

      <div class="form__row form__row--controls">
        <?php if (count($errors) !== 0) : ?>
        <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
        <?php endif; ?>

        <input class="button" type="submit" name="" value="Зарегистрироваться">
      </div>
    </form>
</main>