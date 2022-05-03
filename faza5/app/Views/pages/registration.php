<title>Registration</title>

<div id="main" style="margin: 100px auto; width: 325px; padding: 15px; border-radius: 9px;">
    <form name='registrationForm' action="<?= site_url("Guest/registrationSubmit") ?>" method="POST">
        <h3>Username</h3>
        <input type="text" name="username" class="full" value="<?= set_value('username') ?>" />
        <?php
        if (!empty($errors['username'])) echo $errors['username'];
        ?>

        <h3>Password</h3>
        <input type="password" name="password" class="full">
        <?php
        if (!empty($errors['password'])) echo $errors['password'];
        ?>

        <input type="submit" class="btn" value="REGISTER">
    </form>
</div>