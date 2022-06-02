<style>
    p.indent {
        margin-left: 2%;
        margin-right: 2%;
    }
</style>


<div id=main style="display:flex;flex-wrap: wrap;">
    <div style="flex:50%; padding: 0 16px">
        <h3>Bundle Name:</h3>
        <p class='indent'><?php echo $bundle->name ?></p>

        <h3>Discount:</h3>
        <p class='indent'><?php echo $bundle->discount ?></p>

        <h3>Description:</h3>
        <p class='indent'><?php echo $bundle->description ?></p>

        <h3>Games in bundle IDs:</h3>
        <p class='indent'><?php
                            print_r($bundledProducts);
                            ?></p>

        <h3>Total price:</h3>
        <p class='indent'><strike>$<?php echo $price['price'] ?></strike> $<?php echo "{$price['final']} with discount of {$price['discount']}%" ?></p>

        Banner:
        <img width=20% class=smooth-border src="<?php echo base_url('uploads/bundle/' . $bundle->id . '/banner.jpg')  ?>">

        <form action="<?= site_url("User/buyBundle/{$bundle->id}") ?>" method="POST">
            <input type="submit" class="btn" value="BUY">
        </form>

    </div>
</div>