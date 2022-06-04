<!-- 

Autori:
  Luka Cvijan 2019/0154
	
  Opis: Stranica za uplacivanje novca
  @version 1.3

-->

<div id=short-main>
  <form name='addFundsForm' action="<?= site_url("user/addFundsSubmit/") ?>" method="POST">
    <span class="input-label">Add Funds</span>
    <input type="text" name="funds" placeholder="5.00" class="full" required />
    <?php
    if (!empty($errors['funds'])) echo $errors['funds'];
    ?>
    <input type="submit" class="btn" value="CONTINUE">
  </form>
</div>