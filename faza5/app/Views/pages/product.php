<!--
Autori:
    Uros Loncar 2019/0691
	Djordje Stanojevic 2019/0288
    Luka Cvijan 2019/0154
	
Opis: Stranica za prikaz produkta

@version 1.1

-->

<?php

use App\Models\UserM;
use App\Models\ProductM;
?>

<?= link_tag('search.css') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<style>
    .demo {
        opacity: 0.6;
        transition: 0.2s;
    }

    .activet,
    .demo:hover {
        opacity: 1;
    }
</style>

<title>
    <?php echo $product->name ?>
</title>


<div id="main">
    <div style="margin-bottom: 10px;">
        <select class="search" name="search" style="width: 300px; color: black;"></select>
    </div>
    <h1><?php echo $product->name ?></h1>
    <div class="flex" style="margin-bottom: 0px">
        <div style="flex: 6">
            <div style="margin-bottom: 10px;">
                <div class="mySlides" style="display:none">
                    <video id="video" style="width: 100%" autoplay muted loop controls poster="assets/thumbnail.png">
                        <source src="<?php echo base_url('uploads/product/' . $product->id . '/video.webm')  ?>" type="video/webm">
                    </video>
                </div>

                <div class="mySlides" style="display:none">
                    <img src="<?php echo base_url('uploads/product/' . $product->id . '/ss1.jpg')  ?>" style="width:100%">
                </div>

                <div class="mySlides" style="display:none">
                    <img src="<?php echo base_url('uploads/product/' . $product->id . '/ss2.jpg')  ?>" style="width:100%">
                </div>

                <div class="mySlides" style="display:none">
                    <img src="<?php echo base_url('uploads/product/' . $product->id . '/ss3.jpg')  ?>" style="width:100%">
                </div>
            </div>
            <div class="flex">
                <div style="flex:1">
                    <img class="demo pointer" src="<?php echo base_url('assets/thumbnail.png')  ?>" style="width:100%; height: 100%;" onclick="currentSlide(1)">
                </div>
                <div style="flex:1">
                    <img class="demo pointer" src="<?php echo base_url('uploads/product/' . $product->id . '/ss1.jpg')  ?>" style="width:100%" onclick="currentSlide(2)">
                </div>
                <div style="flex:1">
                    <img class="demo pointer" src="<?php echo base_url('uploads/product/' . $product->id . '/ss2.jpg')  ?>" style="width:100%" onclick="currentSlide(3)">
                </div>
                <div style="flex:1">
                    <img class="demo pointer" src="<?php echo base_url('uploads/product/' . $product->id . '/ss3.jpg')  ?>" style="width:100%" onclick="currentSlide(4)">
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
            <?php if (count($product_bundle) > 0) { ?>
                <h2>Bundles</h2>
                <?php foreach ($product_bundle as $bundle) { ?>
                    <a href="<?php echo site_url($controller . "/bundle/" . $bundle->id) ?>">
                        <div class="dlc">
                            <img style="vertical-align: middle; width: 200px" src="<?php echo base_url('uploads/bundle/' . $bundle->id . '/banner.jpg')  ?>">
                            <span style="vertical-align: middle;"><?php echo $bundle->name ?></span>
                        </div>
                    </a>
                <?php } ?>
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
                            <textarea style="width:100%;" name="text" id="" cols="30" rows="10"><?php echo $product_review->text ?></textarea>

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
            <img style="width:100%; margin-bottom: 15px;" src="<?php echo base_url('uploads/product/' . $product->id . '/banner.jpg')  ?>" />
            <br>
            <?php

            $productM = new ProductM();

            $discount = $productM->getDiscount($product->id);
            $discountedPrice = $productM->getDiscountedPrice($product->id);

            if ($discount != 0) { ?>
                <span class="discount"><?php echo $discount ?>%</span> <span class="price-original">$<?php echo number_format($product->price, 2) ?></span>
            <?php } ?>
            <span class="price">$<?php echo number_format($discountedPrice, 2) ?></span>
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
                    $rating = number_format($product->rev_sum / $product->rev_cnt, 2);
                    $roundedRating = round($rating);
                } else {
                    $rating = "No Reviews";
                    $roundedRating = 0;
                }

                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $roundedRating ? "???" : "???";
                } ?>
                <br>
                <span style="font-size: 20px"><?php echo $rating ?></span>
            </div>
            <div style="margin-top: 20px">
                <?php foreach ($genres as $genre) { ?>
                    <span class=" genre"><?php echo $genre ?></span>
                <?php  } ?>
            </div>
            <?php if (count($friends) > 0) { ?>
                <div>
                    Already owned by <?php echo count($friends) ?> friends:
                    <?php $limit = min(6, count($friends));
                    for ($i = 0; $i < $limit; $i++) { ?>
                        <a href="<?php echo site_url($controller . "/profile/" . $friends[$i]->id) ?>">
                            <div style="margin:5px 0">
                                <img style=" width:25%;vertical-align: middle; max-width: 60px" src="<?php echo (new UserM())->getAvatar($friends[$i]->id) ?>">
                                <span style="vertical-align: middle"><?php echo $friends[$i]->nickname ?></span>
                            </div>
                        </a>
                    <?php } ?>

                </div>
            <?php } ?>
            <div style="margin-top: 10px;flex:1;" class="grid-container2">
                <div></div>
                <div>MINIMUM</div>
                <div>RECOMMENDED</div>

                <div>OS</div>
                <div><?php echo $product->os_min ?></div>
                <div><?php echo $product->os_rec ?></div>

                <div>Processor</div>
                <div><?php echo $product->cpu_min ?></div>
                <div><?php echo $product->cpu_rec ?></div>

                <div>Graphics</div>
                <div><?php echo $product->gpu_min ?></div>
                <div><?php echo $product->gpu_rec ?></div>

                <div>Memory</div>
                <div><?php echo $product->ram_min ?> RAM</div>
                <div><?php echo $product->ram_rec ?> RAM</div>

                <div>Storage</div>
                <div><?php echo $product->mem_min ?> available space</div>
                <div><?php echo $product->mem_rec ?> available space</div>
            </div>
        </div>
    </div>

    <h2>More Like This</h2>
    <div class="flex" style="margin:0 -5px 0 -5px">
        <?php for ($i = 0; $i < 4; $i++) { ?>
            <div style="flex: 1; margin: 5px;">
                <?php if (count($similar_products) > $i) { ?>
                    <a href="<?php echo site_url($controller . "/product/" . $similar_products[$i]->id) ?>">
                        <img class="full-width" src="<?php echo base_url('uploads/product/' . $similar_products[$i]->id . '/banner.jpg') ?>">
                        <h3><?php echo $similar_products[$i]->name ?></h3>
                    </a>
                <?php } ?>
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
                <div id=<?php echo $id ?> style="color: rgb(255, 196, 0); background-color:rgb(0,0,0,0.6); border-radius: 5px; margin-bottom: 10px">
                    <div class="flex" style="align-items: center;">
                        <a href="<?php echo site_url($controller . "/profile/" . $id) ?>">
                            <div style="flex: 1">
                                <img width=70px class="smooth-border" style="padding-right: 13px; border-radius: 5px 0 0 0;vertical-align: middle;" src="<?php echo $review['avatar'] ?>">
                                <span><?php echo $name ?></span>
                            </div>
                        </a>
                        <div style="flex: 1; text-align: right;">
                            <span style="font-size: 28px; margin-right: 5px">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $review['review']->rating ? "???" : "???";
                                } ?>
                            </span>
                            <?php if (isset($user) && $user->admin_rights != 0) { ?>
                                <input id=<?php echo $id ?> style="width: 57px; height: 57px;margin: 0;border-radius: 0 5px 0 0" type="submit" class="btn delete-review" name="action" value="???????">
                            <?php } ?>
                        </div>

                    </div>
                    <div style="padding: 10px;background-color: rgb(64,64,64, 0.6);text-align: justify;">
                        <?php foreach ($review["review"]->text as $line) { ?>
                            <p>
                                <?php echo $line ?>
                            </p>
                        <?php } ?>
                    </div>
                    <div class="flex">
                        <form style="flex: 1;" action=" <?= site_url("user/awardUser/$id/") ?>" method="POST">
                            <input style="border-radius: 0 0 0 5px; margin: 0" type="submit" class="btn" name="action" value="????">
                        </form>
                        <input id=<?php echo $id ?> style="flex: 1; border-radius: 0; margin: 0" type="submit" class="btn like" name="action" value="???? <?php echo $review["positive"] ?>">
                        <input id=<?php echo $id ?> style="flex: 1; border-radius: 0 0 5px 0; margin: 0" type="submit" class="btn dislike" name="action" value="???? <?php echo $review["negative"] ?>">
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

        if (n > slides.length) slideIndex = 1;

        if (n < 1) slideIndex = slides.length;

        for (i = 0; i < slides.length; i++) slides[i].style.display = "none";

        for (i = 0; i < dots.length; i++) dots[i].className = dots[i].className.replace(" activet", "");

        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " activet";
    }
</script>

<script>
    $(function() {
        $('.search').select2({
            placeholder: '???? Search for a product',
            ajax: {
                url: '<?php echo base_url($controller . "/ajaxProductSearch"); ?>',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        $('.search').on('change', function() {
            //nakon odabira
            var proizvod = $(".search option:selected").text();

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url($controller . "/ajaxProductLoad/" . $controller); ?>',
                data: {
                    ime: proizvod
                },
                dataType: 'html',
                success: function(response) {
                    window.location.href = response;
                }
            });
        })

        function update(response) {
            $("#" + id + ".like").attr("value", "???? " + response['pos'])
            $("#" + id + ".dislike").attr("value", "???? " + response['neg'])
        }

        $(document).on("click", ".like", function() {
            id = $(this).attr('id')

            $.ajax({
                url: "<?= site_url("user/likeajax") ?>",
                type: 'POST',
                data: {
                    like: 1,
                    product: <?php echo $product->id ?>,
                    user: id
                },
                dataType: "JSON",
                success: update,
            })
        })

        $(document).on("click", ".dislike", function() {
            id = $(this).attr('id')

            $.ajax({
                url: "<?= site_url("user/likeajax") ?>",
                type: 'POST',
                data: {
                    like: 0,
                    product: <?php echo $product->id ?>,
                    user: $(this).attr('id')
                },
                dataType: "JSON",
                success: update,
            })
        })

        $(document).on("click", ".delete-review", function() {
            id = $(this).attr('id')

            $.ajax({
                url: "<?= site_url("admin/deleteReviewAjax") ?>",
                type: 'POST',
                data: {
                    idProduct: <?php echo $product->id ?>,
                    idUser: id
                },
                dataType: "JSON",
                success: function(response) {
                    $("#" + id).hide(500);
                }
            })
        })


    });
</script>