<title>
    <?php
    echo isset($product) ? "Edit: " . $product->name : "Add New Product";
    ?>
</title>

<?php
if (!isset($product)) {
    $product = (object)[
        'id' => -1,
        'name' => '',
        'price' => '',
        'discount' => '',
        'discount_expire' => '',
        'base_game' => '',
        'description' => '',
        'developer' => '',
        'publisher' => '',
        'release_date' => '',
        'os_min' => '',
        'ram_min' => '',
        'gpu_min' => '',
        'cpu_min' => '',
        'mem_min' => '',
        'os_rec' => '',
        'ram_rec' => '',
        'gpu_rec' => '',
        'cpu_rec' => '',
        'mem_rec' => ''
    ];
}
?>

<form name='manageProductForm' action="<?= site_url("Admin/manageProductSubmit") ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $product->id ?>" />

    <div id=main style="display:flex;flex-wrap: wrap;">
        <div style="flex:100%; padding: 0 16px">
            <?php if (!empty($errors)) : ?>
                <div style='color:red;'>
                    <?php foreach ($errors as $field => $error) : ?>
                        <p><?= $error ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>

        <div style="flex:50%; padding: 0 16px">
            <h3>Product Details <?php if (isset($fuck)) echo $fuck ?></h3>
            Product Name
            <input type="text" name="name" placeholder="Red Dead Redemption 2" value="<?php echo $product->name ?>" class=full>

            Genres (space separated)
            <input type="text" name="genres" placeholder="Action Puzzle Rythm" value="<?php if (isset($genres)) echo implode(' ', $genres) ?>" class=full>

            Price €
            <input type="text" name="price" placeholder="29.99" value="<?php echo $product->price ?>" class=full>

        </div>

        <div style="flex:50%; padding: 0 16px">
            <h3>Owner Details</h3>
            Developer
            <input type="text" name="developer" placeholder="Valve" value="<?php echo $product->developer ?>" class=full>

            Publisher
            <input type="text" name="publisher" placeholder="Nintendo" value="<?php echo $product->publisher ?>" class=full>

            Release Date
            <input type="text" name="release_date" placeholder="15/07/2015" value="<?php echo $product->release_date ?>" class=full>

        </div>

        <div style="flex:50%; padding: 0 16px">
            <h3>Minimum Specifications</h3>
            OS
            <input type="text" name="os_min" placeholder="Windows 7" value="<?php echo $product->os_min ?>" class=full>

            Processor
            <input type="text" name="cpu_min" placeholder="INTEL CORE I7-8700K" value="<?php echo $product->cpu_min ?>" class=full>

            Graphics
            <input type="text" name="gpu_min" placeholder="NVIDIA GEFORCE GTX 1070" value="<?php echo $product->gpu_min ?>" class=full>

            Memory
            <input type="text" name="ram_min" placeholder="8GB" value="<?php echo $product->ram_min ?>" class=full>

            Disk Space
            <input type="text" name="mem_min" placeholder="30GB" value="<?php echo $product->mem_min ?>" class=full>

        </div>

        <div style="flex:50%; padding: 0 16px">
            <h3>Recommended Specifications</h3>
            OS
            <input type="text" name="os_rec" placeholder="Windows 10" value="<?php echo $product->os_rec ?>" class=full>

            Processor
            <input type="text" name="cpu_rec" placeholder="INTEL CORE I7-8700K" value="<?php echo $product->cpu_rec ?>" class=full>

            Graphics
            <input type="text" name="gpu_rec" placeholder="NVIDIA GEFORCE GTX 1070" value="<?php echo $product->gpu_rec ?>" class=full>

            Memory
            <input type="text" name="ram_rec" placeholder="16GB" value="<?php echo $product->ram_rec ?>" class=full>

            Disk Space
            <input type="text" name="mem_rec" placeholder="30GB" value="<?php echo $product->mem_rec ?>" class=full>

        </div>

        <div style="flex:60%; padding: 0 16px; justify-content: center;">
            <h3>DLC Details</h3>
            <p>Base Game ID</p>
            <input type="text" name="base_game" placeholder="440" value="<?php echo $product->base_game ?>" class=full style="width:90%">

        </div>

        <div style="flex:1%; padding: 0 16px">
            <h3>Assets</h3>
            Banner
            <input type="file" name="banner" accept="image/x-jpg">

            Capsule
            <input type="file" name="capsule" accept="image/x-jpg">

            Background
            <input type="file" name="background" accept="image/x-png">

            Video
            <input type="file" name="video" accept="video/webm">

            Screenshot 1
            <input type="file" name="ss1" accept="image/x-jpg">

            Screenshot 2
            <input type="file" name="ss2" accept="image/x-jpg">

            Screenshot 3
            <input type="file" name="ss3" accept="image/x-jpg">

        </div>

        <div style="flex:60%; padding: 0 16px">
            <h3>Description</h3>
            <textarea name="description" style="width: 100%; height: 200px;" placeholder="Describe your product..."><?php echo $product->description ?></textarea>

        </div>

    </div>
    <div id="main" style="margin: -100px 50px;">


        <input type="submit" value="SUBMIT" class="btn">
    </div>


</form>