<html>

<head>
    <title>
        <?php
        echo isset($bundle) ? "Edit: " . $bundle->name : "Add New Bundle";
        ?>
    </title>
</head>

<?php
if (!isset($bundle)) {
    $bundle = (object)[
        'id' => -1,
        'name' => '',
        'discount' => '',
        'description' => '',
    ];
}
?>

<body>

    <form name='manageBundleForm' action="<?= site_url("Admin/manageBundleSubmit") ?>" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?php echo $bundle->id ?>" />

        <div id=main style="display:flex;flex-wrap: wrap;">
            <div style="flex:50%; padding: 0 16px">
                <?php if (!empty($errors)) : ?>
                    <div style='color:red;'>
                        <?php foreach ($errors as $field => $error) : ?>
                            <p><?= $error ?></p>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <h3>Bundle Details</h3>
                Bundle Name
                <input type="text" name="name" placeholder="Can't Stop Crying Pack" value="<?php echo $bundle->name ?>" class=full>

                Discount %
                <input type="text" name="discount" placeholder="15" value="<?php echo $bundle->discount ?>" class=full>

            </div>

            <div style="flex:1%; padding: 0 16px">
                <h3>Assets</h3>
                Banner
                <input type="file" name="banner" accept="image/jpeg">
            </div>

            <div style="flex:60%; padding: 0 16px">
                <h3>Description</h3>
                <textarea name="description" style="width: 100%; height: 200px;" placeholder="Describe your bundle..."><?php echo $bundle->description ?></textarea>

            </div>



            <input type="submit" value="SUBMIT" class="btn">
        </div>

    </form>
    <form name='updateProducts' action="<?= site_url("Admin/updateBundleProducts") ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $bundle->id ?>" />
        <div style="display:flex;flex-wrap: wrap;">
            <div style="flex:50%; padding: 0 200px">
                <h3>Remove products from bundle:</h3>
                <?php if (count($inBundle) != 0) : ?>
                    <?php foreach ($inBundle as $product) : ?>
                        <input type="checkbox" name="inBundle[]" value="<?php echo "{$product->id}" ?>"><?php echo "{$product->id}:\t{$product->name}" ?>
                        </br>
                    <?php endforeach ?>
                <?php else : ?>
                    <div style='color:red;'>
                        Bundle is empty.
                    </div>
                <?php endif ?>
            </div>
            <div style="flex:50%; padding: 0 200px">
                <h3>Add products to bundle:</h3>
                <?php if (count($notInBundle) != 0) : ?>
                    <?php foreach ($notInBundle as $product) : ?>
                        <input type="checkbox" name="notInBundle[]" value="<?php echo "{$product->id}" ?>"><?php echo "{$product->id}:\t{$product->name}" ?>
                        </br>
                    <?php endforeach ?>
                <?php else : ?>
                    <div style='color:red;'>
                        Bundle has every product??????? easter egg
                    </div>
                <?php endif ?>
            </div>
            <input type="submit" value="Apply bundle content change" class="btn">
        </div>
    </form>

</body>

</html>