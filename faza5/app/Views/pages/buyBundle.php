<!--
Autori:
	Uros Loncar 2019/0691
	Fedja Mladenovic 2019/0613
	
Opis: Stranica za kupovinu bundla

@version 1.1


-->

<title>Buy Bundle</title>

<div id="short-main">
    <img class="smooth-border full-width" src="<?php bundle_banner($bundle->id)  ?>" />
    <h2><?php echo $bundle->name ?></h2>
    <form name='buyBundleForm' action="<?= site_url("User/buyBundleSubmit/{$bundle->id}") ?>" method="POST">
        <div>Price: </div>
        <div>$<?php echo number_format($price['final'], 2) ?></div>

        <?php if (isset($message)) echo "$message" ?>

        <br>
        <div style="color:red;"><?php if ($price['final'] == 0) echo "You already own all the products in this bundle." ?></div>
        <input type="hidden" name="final" value="<?php echo $price['final'] ?>" />
        <input type="submit" class="btn" <?php if ($price['final'] == 0) : ?> disabled <?php endif ?> value="Confirm Purchase">
    </form>
</div>