<!-- 

Autori:
  Luka Cvijan 2019/0154
	
  Opis: Stranica za kupovinu proizvoda
  @version 1.3

-->
<?php

use App\Models\ProductM;
?>

<div id=short-main>
  <img style="width:100%; border-radius: 5px" src="<?php product_banner($product->id)  ?>" />
  <h2><?php echo $product->name ?></h2>
  <?php

  $productM = new ProductM();

  $discount = $productM->getDiscount($product->id);
  $discountedPrice = $productM->getDiscountedPrice($product->id);

  if ($discount != 0) { ?>
    <span class="discount-solid"><?php echo $discount ?>%</span> <span class="price-original-solid"><?php echo number_format($product->price, 2) ?></span>
  <?php } ?>
  <span class="price-solid">$<?php echo number_format($discountedPrice, 2) ?></span>
  <form name='buyProductForm' action="<?= site_url("User/buyProductSubmit/{$product->id}") ?>" method="POST">
    <div style="background-color: rgb(255, 196, 0); padding: 0 0 0 5px;color:black;margin-top: 10px;display: flex;">
      <span style="flex: 1">Buy For</span>

      <select name="buyOptions" style="flex: 1">

        <option value="myself">Myself</option>

        <?php if (count($friends) > 0) { ?>
          <option disabled>───────────────────</option>
          <optgroup label="Gift To">
            <?php foreach ($friends as $friend) { ?>
              <option value="<?php echo $friend->id ?>"><?php echo $friend->username ?></option>
            <?php } ?>
          </optgroup>
        <?php } ?>
      </select>
    </div>
    <?php if (isset($message)) echo "$message" ?>
    <input type="submit" class="btn" value="Confirm Purchase">
  </form>
</div>