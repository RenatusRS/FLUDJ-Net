<div id=short-main>
    <h2>My Coupons</h2>

    <?php foreach ($coupons as $coupon) { ?>
        <a href="<?php product_url('user', $coupon->id) ?>">
            <div style="color: black;background-color: rgb(255, 196, 0, 0.6);display:flex; align-items: center;margin: 10px 0; border: 2px solid rgb(255, 196, 0); border-radius: 5px">

                <div style="flex: 2">
                    <img style="width: 200px;" src=<?php product_banner($coupon->id) ?>>
                </div>

                <div style="flex: 1; text-align: center;">
                    <span style="font-size: 30px;">
                        <?php echo $coupon->coupon ?>%
                    </span>
                </div>

            </div>
        </a>
    <?php } ?>
</div>