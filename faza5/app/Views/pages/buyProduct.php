<!-- 

Autori:
  Luka Cvijan 2019/0154
	
  Opis: Stranica za kupovinu proizvoda
  @version 1.3

-->
<title>Buy Product</title>


<form name='buyProductForm' action="<?= site_url("User/buyProductSubmit/{$product->id}") ?>" method="POST">
  <br><br>

  <label for="cars">For:</label>

  <select name="buyOptions">
    <option value="myself">Myself</option>
    <?php
    foreach ($friends as $friend) {
    ?>
      <option value="<?php echo $friend->id ?>"> <?php echo $friend->username ?> </option>
    <?php
    }
    ?>
  </select>
  <br>
  <?php if (isset($message)) echo "$message" ?>
  <br>
  <input type="submit" class="btn" value="Confirm Purchase">
</form>