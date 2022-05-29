<title>Buy Bundle</title>


<form name='buyBundleForm' action="<?= site_url("User/buyBundleSubmit/{$bundle->id}") ?>" method="POST">
  <br><br>

  <div>Price: </div>
  <div><strike>$<?php echo $price['price'] ?></strike> $<?php echo "{$price['final']} with discount of {$price['discount']}%" ?></div>

  <?php if (isset($message)) echo "$message" ?>

  <br>
  <div style="color:red;"><?php if ($price['final'] == 0) echo "You already own all the products in this bundle." ?></div>
  <input type="hidden" name="final" value="<?php echo $price['final'] ?>" />
  <input type="submit" class="btn" <?php if ($price['final'] == 0) : ?> disabled <?php endif ?> value="Confirm Purchase">
</form>