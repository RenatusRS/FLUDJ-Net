<!-- 

Autori:
  Luka Cvijan 2019/0154
	
  Opis: Stranica za uplacivanje novca
  @version 1.3

-->

<title>Add Funds</title>

<div id=short-main>
  <form name='addFundsForm' action="<?= site_url("User/addFundsSubmit/") ?>" method="POST">
    <span class="input-label">Add Funds</span>
    <input type="text" name="funds" class="full" required />
    <?php
    if (!empty($errors['funds'])) echo $errors['funds'];
    ?>
    <input type="submit" class="btn" value="CONTINUE">
  </form>
</div>