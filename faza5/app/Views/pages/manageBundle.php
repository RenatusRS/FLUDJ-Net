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

<title>
    <?php echo isset($bundle) ? "Edit: " . $bundle->name : "Add New Bundle"; ?>
</title>

<div id=main style="display:flex">
    <form style="flex: 3; margin-right: 16px" name='manageBundleForm' action="<?= site_url("Admin/manageBundleSubmit") ?>" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?php echo $bundle->id ?>" />

        <div style="display:flex;">
            <div style="flex:3%;">
                <?php if (!empty($errors)) : ?>
                    <div style='color:red;'>
                        <?php foreach ($errors as $field => $error) : ?>
                            <p><?= $error ?></p>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <h3>Bundle Details</h3>
                Bundle Name
                <input type="text" name="name" placeholder="Can't Stop Laughing Pack" value="<?php echo $bundle->name ?>" class=full>

                Discount %
                <input type="text" name="discount" placeholder="15" value="<?php echo $bundle->discount ?>" class=full>

            </div>
            <div style="flex:1; padding: 0 16px">
                <h3>Assets</h3>
                Banner
                <input type="file" name="banner" accept="image/jpeg">
            </div>
        </div>
        <div>
            <h3>Description</h3>
            <textarea name="description" style="width: 100%; height: 200px;" placeholder="Describe your bundle..."><?php echo $bundle->description ?></textarea>
        </div>

        <input type="submit" value="SUBMIT" class="btn">

    </form>
    <form style="flex: 1;" name='updateProducts' action="<?= site_url("Admin/updateBundleProducts") ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $bundle->id ?>" />
        <h3 style="display: inline-block; margin: 0;">UPDATE CONTENTS</h3>

        <br>
        <?php if ($bundle->id == -1) : ?>
            <div style='color:red;'>
                Create the bundle first then change its contents.
            </div>
        <?php else : ?>
            <div style="max-height: 450px; overflow-y: auto">


                <?php foreach ($inBundle as $product) : ?>
                    <label><input type="checkbox" name="inBundle[]" value="<?php echo "{$product->id}" ?>" checked><?php echo "{$product->name}" ?></label>
                    </br>
                <?php endforeach ?>
                <?php foreach ($notInBundle as $product) : ?>
                    <label><input type="checkbox" name="inBundle[]" value="<?php echo "{$product->id}" ?>"><?php echo "{$product->name}" ?></label>
                    </br>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <input type="submit" class="btn" value="CHANGE CONTENT" <?php if ($bundle->id == -1) echo "hidden" ?>>

    </form>
</div>