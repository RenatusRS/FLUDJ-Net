<title>
    <?php
    echo $product->name;
    ?>
</title>

<div style="display:flex">
    <div width="30%" style="padding-left: 20px;">
        <img width=100% class=smooth-border src="<?php echo base_url('uploads/product/' . $product->id . '/banner.jpg')  ?>">
        <p class=highlight-text><?php echo $product->description; ?></p>
        <h4><?php echo $product->name . " " . $product->price; ?></h4>
        <form action="<?= site_url("User/buyProduct/{$product->id}") ?>" method="POST">
            <input type="submit" class="btn" value="BUY">
        </form>
    </div>
</div>
<?php if ($product_review!=NULL) { ?>
    <form action="<?= site_url("User/makeReviewSubmit/{$product->id}") ?>" method="POST">
        <textarea name="text" id="" cols="30" rows="10"><?php echo $product_review->text ?></textarea>
        <br>
        <input type="range" name="rating" value="<?php echo $product_review->rating ?>" min="1" max="5">
        <input type="submit" class="btn" value="Make Review">
    </form>
<?php
}
?>
<div style="display:flex;">
    <p style="margin:20px 5px 20px 0px">Manage Product</p>
    <input type="button" style="margin:20px 5px 20px 5px;" class="btn" value="Add Discount">
    <a href=manage_product.html class="button">Edit Product</a>
    <a href=index.html class="button" style="margin:20px 0px 20px 5px">Delete Product</a>
</div>

<div style="display:flex;">
    <div style=" text-align: justify;flex:2;">
        <h3>DESCRIPTION</h3>
        <?php echo $product->description ?>
    </div>

    <div>
        <div style="background-color: black; margin-left: 20px; margin-top: 10px;flex:1;" class=grid-container>
            <div>DEVELOPER</div>
            <div><?php echo $product->developer ?></div>

            <div>PUBLISHER</div>
            <div><?php echo $product->publisher ?></div>

            <div>RELEASE DATE</div>
            <div><?php echo $product->release_date ?></div>
        </div>

        <div style="background-color: black; margin-left: 20px; margin-top: 10px;flex:1; padding:1px 20px">
            <h3>Genres</h3>
            <?php echo $genres ?>
        </div>

        <div style="background-color: black; margin-left: 20px; margin-top: 10px;flex:1;" class="grid-container2">
            <div>
                <h3>Specifications</h3>
            </div>
            <div></div>
            <div></div>

            <div></div>
            <div>RECOMMENDED</div>
            <div>MINIMUM</div>

            <div>OS</div>
            <div><?php echo $product->os_rec ?></div>
            <div><?php echo $product->os_min ?></div>

            <div>Processor</div>
            <div><?php echo $product->cpu_rec ?></div>
            <div><?php echo $product->cpu_min ?></div>

            <div>Graphics</div>
            <div><?php echo $product->gpu_rec ?></div>
            <div><?php echo $product->gpu_min ?></div>

            <div>Memory</div>
            <div><?php echo $product->ram_rec ?> RAM</div>
            <div><?php echo $product->ram_min ?> RAM</div>

            <div>Storage</div>
            <div><?php echo $product->mem_rec ?> available space</div>
            <div><?php echo $product->mem_min ?> available space</div>
        </div>
    </div>
</div>
</div>

<div>
    <h1>Top 5 Reviews</h1>
    <?php
    if (isset($reviews)) {
        foreach ($reviews as $name => $review) {
            if ($review == NULL) break;
    ?>
            <h1><?php echo $name ?></h1>
            <p><?php echo $review["review"]->text ?></p>
            <h2><?php echo $review["review"]->rating ?></h2>
            <form action="<?= site_url("User/LikeSubmit/{$product->id}/{$name}") ?>" method="POST">
                <input type="submit" class="btn" name="action" value="Like <?php echo $review["positive"] ?>">
            </form>
            <form action="<?= site_url("User/DislikeSubmit/{$product->id}/{$name}") ?>" method="POST">
                <input type="submit" class="btn" name="action" value="Dislike <?php echo $review["negative"]?>">
            </form>
    <?php
        }
    }
    ?>
</div>