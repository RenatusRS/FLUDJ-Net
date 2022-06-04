<!--
Autori:
	Uros Loncar 2019/0691
	Fedja Mladenovic 2019/0613
	
Opis: Stranica za prikaz bundla

@version 1.1

-->

<div id=middle-main>
    <h1><?php echo $bundle->name ?></h1>
    <img class=smooth-border style="width:100%" src=" <?php echo base_url('uploads/bundle/' . $bundle->id . '/banner.jpg')  ?>">
    <h2>About</h2>
    <?php foreach ($bundle->description as $line) { ?>
        <p>
            <?php echo $line ?>
        </p>
    <?php } ?>

    <h2>Products Included</h2>
    <div style="background-color: rgb(0,0,0,0.6);">
        <?php foreach ($bundledProducts as $product) { ?>
            <a href="<?php product_url($controller, $product->id) ?>">
                <div style="display: flex;align-items: center;">
                    <div style="flex:6">
                        <img style="width: 200px;vertical-align: middle" src="<?php product_banner($product->id) ?>">
                        <span><?php echo $product->name ?></span>
                    </div>
                    <div style="flex:1;text-align: right; padding: 15px">
                        $<?php echo number_format($product->price, 2) ?>
                    </div>
                </div>
            </a>
        <?php } ?>
        <div style="display: flex;align-items: center;padding: 15px">
            <div style="flex:2">
                <h3>TOTAL PRICE</h3>
            </div>
            <div style="flex:1;text-align: right">
                <strike>$<?php echo number_format($price['price'], 2) ?></strike> -<?php echo $price['discount'] ?>% $<?php echo number_format($price['final'], 2) ?>
            </div>
        </div>
    </div>

    <form action="<?= site_url("User/buyBundle/{$bundle->id}") ?>" method="POST">
        <input type="hidden" name="price" value="<?php echo $price['price'] ?>" />
        <input type="hidden" name="discount" value="<?php echo $price['discount'] ?>" />
        <input type="hidden" name="final" value="<?php echo $price['final'] ?>" />
        <input type="submit" class="btn" value="BUY">
    </form>
</div>