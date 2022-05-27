<title>Registration</title>

<div id="short-main">
    <form name='registrationForm' action="<?= site_url("Guest/registrationSubmit") ?>" method="POST">
        <span class="input-label">Username</span>
        <input type="text" name="username" class="full" value="<?= set_value('username') ?>" />
        <?php
        if (!empty($errors['username'])) echo $errors['username'];
        ?>

        <span class="input-label">Password</span>
        <input type="password" name="password" class="full">
        <?php
        if (!empty($errors['password'])) echo $errors['password'];
        ?>

        <input type="submit" class="btn" value="REGISTER">
    </form>
</div>