<title>Registration</title>

<div id="short-main">
    <?php if (!empty($errors)) : ?>
        <div style='color:red;'>
            <?php foreach ($errors as $field => $error) : ?>
                <p><?= $error ?></p>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <form name='registrationForm' action="<?= site_url("Guest/registrationSubmit") ?>" method="POST">
        <span class="input-label">Username</span>
        <input type="text" name="username" class="full" value="<?= set_value('username') ?>" />

        <span class="input-label">Password</span>
        <input type="password" name="password" class="full">

        <input type="submit" class="btn" value="REGISTER">
    </form>
</div>