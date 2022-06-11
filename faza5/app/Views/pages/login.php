<!--
Autori:
	Uros Loncar 2019/0691
	Fedja Mladenovic 2019/0613
	
Opis: Stranica za logovanje u sistem

@version 1.1

-->

<title>Login</title>

<div id="short-main">
    <form name='loginForm' action="<?= site_url("guest/loginSubmit") ?>" method="POST">
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
        <?php if (isset($message)) echo "$message" ?>

        <input type="submit" class="btn" value="SIGN-IN">
    </form>
</div>