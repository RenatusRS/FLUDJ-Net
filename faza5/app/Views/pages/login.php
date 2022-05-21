<title>Login</title>

<?php if (isset($message)) echo "$message" ?>
<div id="short-main">
    <form name='loginForm' action="<?= site_url("Guest/loginSubmit") ?>" method="POST">
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

        <input type="submit" class="btn" value="SIGN-IN">
    </form>
</div>