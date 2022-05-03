<title>Add Funds</title>


<form name='addFundsForm' action="<?= site_url("User/addFundsSubmit/") ?>" method="POST">
    <br><br>
    <h3>Add funds:</h3>
    <input type="text" name="funds" class="full" required />
    <?php
    if (!empty($errors['funds'])) echo $errors['funds'];
    ?>
    <input type="submit" class="btn" value="CONTINUE">
</form>