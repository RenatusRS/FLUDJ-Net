<?php

use App\Models\ProductM;
?>

<style>
    img {
        vertical-align: middle;
    }

    /* Hide the images by default */
    .mySlides {
        display: none;
    }

    /* Add a pointer when hovering over the thumbnail images */
    .cursor {
        cursor: pointer;
    }



    .row:after {
        content: "";
        display: table;
        clear: both;
    }

    /* Six columns side by side */
    .column {
        width: 16.66%;
    }

    /* Add a transparency effect for thumnbail images */
    .demo {
        opacity: 0.6;
        transition: 0.2s;

    }

    .activet,
    .demo:hover {
        opacity: 1;
    }
</style>

<title><?php echo $product->name; ?></title>
<div id="main">
    <h1><?php echo $product->name ?></h1>
    <div style="display: flex; margin-bottom: 0px">
        <div style="flex: 6">
            <div style="margin-bottom: 10px;">
                <div class="mySlides">
                    <video id="video" style="width: 100%; color: yellow" autoplay muted loop controls poster="assets/thumbnail.png">
                        <source src="<?php echo base_url('uploads/product/' . $product->id . '/video.webm')  ?>" type="video/webm">
                    </video>
                </div>

                <div class="mySlides">
                    <img src="<?php echo base_url('uploads/product/' . $product->id . '/ss1.jpg')  ?>" style="width:100%">
                </div>

                <div class="mySlides">
                    <img src="<?php echo base_url('uploads/product/' . $product->id . '/ss2.jpg')  ?>" style="width:100%">
                </div>

                <div class="mySlides">
                    <img src="<?php echo base_url('uploads/product/' . $product->id . '/ss3.jpg')  ?>" style="width:100%">
                </div>
            </div>
            <div style="display:flex">
                <div style="flex:1">
                    <img class="demo cursor" src="<?php echo base_url('assets/thumbnail.png')  ?>" style="width:100%" onclick="currentSlide(1)">
                </div>
                <div style="flex:1">
                    <img class="demo cursor" src="<?php echo base_url('uploads/product/' . $product->id . '/ss1.jpg')  ?>" style="width:100%" onclick="currentSlide(2)">
                </div>
                <div style="flex:1">
                    <img class="demo cursor" src="<?php echo base_url('uploads/product/' . $product->id . '/ss2.jpg')  ?>" style="width:100%" onclick="currentSlide(3)">
                </div>
                <div style="flex:1">
                    <img class="demo cursor" src="<?php echo base_url('uploads/product/' . $product->id . '/ss3.jpg')  ?>" style="width:100%" onclick="currentSlide(4)">
                </div>
            </div>
            <?php if ($product_base != null) { ?>
                <a href="<?php echo site_url($controller . "/product/" . $product_base->id) ?>">
                    <div style="background-color:black;">
                        <img style="vertical-align: middle; width: 120px" src="<?php echo base_url('uploads/product/' . $product_base->id . '/banner.jpg')  ?>">
                        <span style="vertical-align: middle;">This product is a DLC for <?php echo $product_base->name ?>.</span>
                    </div>
                </a>
            <?php } ?>
            <div>
                <h2>Bundles</h2>
            </div>
            <?php if (count($product_dlc) > 0) { ?>
                <div>
                    <h2>Downloadable Content</h2>
                    <?php foreach ($product_dlc as $dlc) { ?>
                        <a href="<?php echo site_url($controller . "/product/" . $dlc->id) ?>">
                            <div class="dlc">
                                <img style="vertical-align: middle; width: 80px" src="<?php echo base_url('uploads/product/' . $dlc->id . '/banner.jpg')  ?>">
                                <span style="vertical-align: middle;"><?php echo $dlc->name ?></span>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            <div>
                <h2>About</h2>
                <?php foreach ($description as $row) { ?>
                    <p style="text-align:justify">
                        <?php echo $row ?>
                    </p>
                <?php } ?>
            </div>
            <div>
                <?php if (isset($product_review)) { ?>
                    <hr>
                    <h2>Submit Review</h2>
                    <?php if (isset($product_review)) { ?>
                        <form action="<?= site_url("user/makeReviewSubmit/{$product->id}") ?>" method="POST">
                            <textarea style="width:100%; min-width: 100%; max-width: 100%;" name="text" id="" cols="30" rows="10"><?php echo $product_review->text ?></textarea>
                            <div style="text-align: center;">
                                <?php if (isset($product_review->rating)) : ?>
                                    Current rating: <?php echo $product_review->rating ?>/5
                                <?php endif ?>
                            </div>
                            <input type="range" style="width:100%" name="rating" value="<?php echo $product_review->rating ?>" min="1" max="5">
                            <input type="submit" class="btn" value="Create Review">
                        </form>
                        <?php if ($product_review != null) { ?>
                            <form action="<?= site_url("user/deleteReviewSubmit/{$product->id}") ?>" method="POST">
                                <input type="submit" class="btn" value="Delete Review">
                            </form>
                        <?php } ?>

                <?php }
                } ?>
            </div>
        </div>
        <div style="flex: 2; margin-left: 35px; margin-bottom: 15px; min-width: 180px;">
            <img style="width:100%" src="<?php echo base_url('uploads/product/' . $product->id . '/banner.jpg')  ?>" />
            <br>
            <?php

            $productM = new ProductM();

            $discount = $productM->getDiscount($product->id);
            $discountedPrice = $productM->getDiscountedPrice($product->id);

            if ($discount != 0) { ?>
                <span class="discount"><?php echo $discount ?>%</span> <span class="price-original"><?php echo number_format($product->price, 2) ?></span>
            <?php } ?>
            <span class="price"><?php echo number_format($discountedPrice, 2) ?></span>
            <form action="<?= site_url("user/buyproduct/{$product->id}") ?>" method="POST">
                <input type="submit" class="btn" value="BUY">
            </form>
            <div class="product-detail">
                <span class="product-detail-left">Developer</span><span class="product-detail-right"><?php echo $product->developer ?></span>
            </div>
            <div class="product-detail">
                <span class="product-detail-left">Publisher</span><span class="product-detail-right"><?php echo $product->publisher ?></span>
            </div>
            <div class="product-detail">
                <span class="product-detail-left">Release Date</span><span class="product-detail-right"><?php echo $product->release_date ?></span>
            </div>
            <div class="product-detail-center">
                <?php
                if ($product->rev_cnt != 0) {
                    $rating = $product->rev_sum / $product->rev_cnt;
                    $roundedRating = round($rating);
                } else {
                    $rating = "No Reviews";
                    $roundedRating = 0;
                }

                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $roundedRating ? "★" : "☆";
                } ?>
                <br>
                <span style="font-size: 20px"><?php echo $rating ?></span>
            </div>
            <div>
                <?php foreach ($genres as $genre) { ?>
                    <span class="genre"><?php echo $genre ?></span>
                <?php  } ?>
            </div>
            <div>
                Already owned by ? friends:
            </div>
            <div style="margin-left: 20px; margin-top: 10px;flex:1;" class="grid-container2">
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
    <h2>More Like This</h2>

    <div style="display: flex;">
        <?php for ($i = 0; $i < 4; $i++) { ?>
            <div style="flex: 1;">
                <img style="width:100%" src="<?php echo base_url('uploads/product/' . $similar_products[$i]->id . '/banner.jpg') ?>">
            </div>
        <?php } ?>
    </div>
    <div>
        <?php
        if (isset($reviews)) {
            foreach ($reviews as $review) {
                $name = $review['user']->nickname;
                $id = $review['user']->id;
        ?>

                <div style="color: rgb(255, 196, 0); background-color:black; border-radius: 5px">
                    <div style="display:flex">
                        <div style="flex: 3">
                            <img width=70px class=smooth-border style="padding-right: 13px; border-radius: 5px 0 0 0;vertical-align: middle;" src="<?php echo $review['avatar'] ?>">
                            <span style="vertical-align: middle;"><?php echo $name ?></span>
                        </div>
                        <div style="flex: 1; text-align: right;">
                            <span style="vertical-align: middle; font-size: 28px"><?php
                                                                                    for ($i = 1; $i <= 5; $i++) {
                                                                                        echo $i <= $review['review']->rating ? "★" : "☆";
                                                                                    } ?>
                            </span>
                            <?php if (isset($user) && $user->admin_rights != 0) { ?>
                                <form style="display:inline-block" action="<?= site_url("admin/DeleteReviewAdminSubmit/{$product->id}/{$id}") ?>" method="POST">
                                    <input style="width: 57px; height: 57px;margin: 0; vertical-align: middle; border-radius: 0 5px 0 0" type="submit" class="btn" name="action" value="🗑️">
                                </form>
                            <?php } ?>
                        </div>

                    </div>
                    <div style="padding: 10px;background-color: rgb(64,64,64);text-align: justify;">
                        <?php echo $review["review"]->text ?>
                    </div>
                    <div style="display:flex">
                        <form style="flex: 1;" action=" <?= site_url("user/LikeDislikeSubmit/{$product->id}/{$id}") ?>" method="POST">
                            <input style="border-radius: 0 0 0 5px; margin: 0" type="submit" class="btn" name="action" value="👍 <?php echo $review["positive"] ?>">
                            <input type="hidden" name="like" value="1">
                        </form>
                        <form style="flex: 1" action="<?= site_url("user/LikeDislikeSubmit/{$product->id}/{$id}") ?>" method="POST">
                            <input style="border-radius: 0 0 5px 0; margin: 0" type="submit" class="btn" name="action" value="👎 <?php echo $review["negative"] ?>">
                            <input type="hidden" name="like" value="0">
                        </form>
                    </div>
                </div>
        <?php }
        } ?>
    </div>
</div>

<script>
    let video = document.getElementById("video");
    let slideIndex = 1;
    showSlides(slideIndex);

    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    function showSlides(n) {
        let i;
        let slides = document.getElementsByClassName("mySlides");
        let dots = document.getElementsByClassName("demo");

        if (n != 1) video.pause();
        else video.play();

        if (n > slides.length) {
            slideIndex = 1
        }
        if (n < 1) {
            slideIndex = slides.length
        }
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" activet", "");
        }
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " activet";
    }
</script>