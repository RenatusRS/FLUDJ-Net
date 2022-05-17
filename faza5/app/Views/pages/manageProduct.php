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
        <div style="flex:50%; padding: 0 16px">

            <?php if (!empty($errors['product'])) echo $errors['product'] ?>
            <h3>Product Details <?php if (isset($fuck)) echo $fuck ?></h3>
            Product Name
            <input type="text" name="name" placeholder="Red Dead Redemption 2" value="<?php echo $product->name ?>"  class=full>
            <?php if (!empty($errors['name'])) echo $errors['name'] ?>

            Genres (space separated)
            <input type="text" name="genres" placeholder="Action Puzzle Rythm" value="<?php if (isset($genres)) echo implode(' ', $genres) ?>"  class=full>
            <?php if (!empty($errors['genres'])) echo $errors['genres'] ?>

            Price €
            <input type="text" name="price" placeholder="29.99" value="<?php echo $product->price ?>"  class=full>
            <?php if (!empty($errors['price'])) echo $errors['price'] ?>

        </div>

        <div style="flex:50%; padding: 0 16px">
            <h3>Owner Details</h3>
            Developer
            <input type="text" name="developer" placeholder="Valve" value="<?php echo $product->developer ?>"  class=full>
            <?php if (!empty($errors['developer'])) echo $errors['developer'] ?>

            Publisher
            <input type="text" name="publisher" placeholder="Nintendo" value="<?php echo $product->publisher ?>"  class=full>
            <?php if (!empty($errors['publisher'])) echo $errors['publisher'] ?>

            Release Date
            <input type="text" name="release_date" placeholder="15/07/2015" value="<?php echo $product->release_date ?>"  class=full>
            <?php if (!empty($errors['release_date'])) echo $errors['release_date'] ?>

        </div>

        <div style="flex:50%; padding: 0 16px">
            <h3>Minimum Specifications</h3>
            OS
            <input type="text" name="os_min" placeholder="Windows 7" value="<?php echo $product->os_min ?>"  class=full>
            <?php if (!empty($errors['os_min'])) echo $errors['os_min'] ?>

            Processor
            <input type="text" name="cpu_min" placeholder="INTEL CORE I7-8700K" value="<?php echo $product->cpu_min ?>"  class=full>
            <?php if (!empty($errors['cpu_min'])) echo $errors['cpu_min'] ?>

            Graphics
            <input type="text" name="gpu_min" placeholder="NVIDIA GEFORCE GTX 1070" value="<?php echo $product->gpu_min ?>"  class=full>
            <?php if (!empty($errors['gpu_min'])) echo $errors['gpu_min'] ?>

            Memory
            <input type="text" name="ram_min" placeholder="8GB" value="<?php echo $product->ram_min ?>"  class=full>
            <?php if (!empty($errors['ram_min'])) echo $errors['ram_min'] ?>

            Disk Space
            <input type="text" name="mem_min" placeholder="30GB" value="<?php echo $product->mem_min ?>"  class=full>
            <?php if (!empty($errors['mem_min'])) echo $errors['mem_min'] ?>

        </div>

        <div style="flex:50%; padding: 0 16px">
            <h3>Recommended Specifications</h3>
            OS
            <input type="text" name="os_rec" placeholder="Windows 10" value="<?php echo $product->os_rec ?>"  class=full>
            <?php if (!empty($errors['os_rec'])) echo $errors['os_rec'] ?>

            Processor
            <input type="text" name="cpu_rec" placeholder="INTEL CORE I7-8700K" value="<?php echo $product->cpu_rec ?>"  class=full>
            <?php if (!empty($errors['cpu_rec'])) echo $errors['cpu_rec'] ?>

            Graphics
            <input type="text" name="gpu_rec" placeholder="NVIDIA GEFORCE GTX 1070" value="<?php echo $product->gpu_rec ?>"  class=full>
            <?php if (!empty($errors['gpu_rec'])) echo $errors['gpu_rec'] ?>

            Memory
            <input type="text" name="ram_rec" placeholder="16GB" value="<?php echo $product->ram_rec ?>"  class=full>
            <?php if (!empty($errors['ram_rec'])) echo $errors['ram_rec'] ?>

            Disk Space
            <input type="text" name="mem_rec" placeholder="30GB" value="<?php echo $product->mem_rec ?>"  class=full>
            <?php if (!empty($errors['mem_rec'])) echo $errors['mem_rec'] ?>

        </div>

        <div style="flex:60%; padding: 0 16px; justify-content: center;">
            <h3>DLC Details</h3>
            <p>Base Game ID</p>
            <input type="text" name="base_game" placeholder="440" value="<?php echo $product->base_game ?>" class=full style="width:90%">
            <?php if (!empty($errors['base_game'])) echo $errors['base_game'] ?>

        </div>

        <div style="flex:1%; padding: 0 16px">
            <h3>Assets</h3>
            Banner
            <input type="file" name="banner"  accept="image/x-jpg">
            <?php if (!empty($errors['banner'])) echo $errors['banner'] ?>

            Background
            <input type="file" name="background" accept="image/x-png">
            <?php if (!empty($errors['background'])) echo $errors['background'] ?>


            Video
            <input type="file" name="video" accept="video/webm">
            <?php if (!empty($errors['video'])) echo $errors['video'] ?>

            Screenshot 1
            <input type="file" name="ss1"  accept="image/x-jpg">
            <?php if (!empty($errors['ss1'])) echo $errors['ss1'] ?>

            Screenshot 2
            <input type="file" name="ss2"  accept="image/x-jpg">
            <?php if (!empty($errors['ss2'])) echo $errors['ss2'] ?>

            Screenshot 3
            <input type="file" name="ss3"  accept="image/x-jpg">
            <?php if (!empty($errors['ss3'])) echo $errors['ss3'] ?>

        </div>

        <div style="flex:60%; padding: 0 16px">
            <h3>Description</h3>
            <textarea name="description" style="width: 100%; height: 200px;" placeholder="Describe your product..." ><?php echo $product->description ?></textarea>
            <?php if (!empty($errors['description'])) echo $errors['description'] ?>

        </div>

    </div>
    <div id="main" style="margin: -100px 50px;">


        <input type="submit" value="SUBMIT" class="btn">
    </div>


</form>