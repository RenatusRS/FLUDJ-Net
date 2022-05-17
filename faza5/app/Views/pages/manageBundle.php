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
            <?php if (!empty($errors['bundle'])) echo $errors['bundle'] ?>
            <h3>Bundle Details <?php if (isset($fuck)) echo $fuck ?></h3>
            Bundle Name
            <input type="text" name="name" placeholder="Bundle name" value="<?php echo $bundle->name ?>" class=full>
            <?php if (!empty($errors['name'])) echo $errors['name'] ?>

            Discount %
            <input type="text" name="discount" placeholder="15" value="<?php echo $bundle->discount ?>" class=full>
            <?php if (!empty($errors['discount'])) echo $errors['discount'] ?>

        </div>

        <div style="flex:1%; padding: 0 16px">
            <h3>Assets</h3>
            Banner
            <input type="file" name="banner">
            <?php if (!empty($errors['banner'])) echo $errors['banner'] ?>
</br>
            Background
            <input type="file" name="background">
            <?php if (!empty($errors['background'])) echo $errors['background'] ?>
        </div>

        <div style="flex:60%; padding: 0 16px">
            <h3>Description</h3>
            <textarea name="description" style="width: 100%; height: 200px;" placeholder="Describe your bundle..."><?php echo $bundle->description ?></textarea>
            <?php if (!empty($errors['description'])) echo $errors['description'] ?>

        </div>

    </div>
    <div id="main" style="margin: -100px 50px;">


        <input type="submit" value="SUBMIT" class="btn">
    </div>


</form>

</body>
</html>