<!--
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691

Opis: Stranica za podesavanje podataka na svom profilu

-->

<?php

use App\Models\OwnershipM;
use App\Models\ProductM;
use App\Models\UserM;

$us = (new UserM())->where('id', $user->id)->first();
?>

<title>Edit Profile</title>

<div id="main" style="margin: 100px auto; width: 325px; padding: 15px; border-radius: 9px;">
    <?php if (!empty($errors)) : ?>
        <div style='color:red;'>
            <?php foreach ($errors as $field => $error) : ?>
                <p><?= $error ?></p>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <form name='friend_button' action="<?= site_url("user/EditProfileSubmit/") ?>" method="POST" enctype="multipart/form-data">

        <h3>Avatar</h3>
        <input type="file" name="profile_pic" accept="image/jpeg">

        <h3>Nickname</h3>
        <input type="text" name="nickname" class="full" value="<?= set_value('nickname', $us->nickname); ?>" />

        <h3>Real Name</h3>
        <input type="text" name="real_name" class="full" value="<?= set_value('real_name', $us->real_name); ?>" />

        <h3>Description</h3>
        <textarea name="description" rows=10 style="width:100%; max-width: 100%; min-width: 100%;"><?php echo $us->description; ?></textarea>

        <h3>Featured Review</h3>
        <?php $os = (new OwnershipM())->where('id_user', $us->id)->where('rating !=', null)->findAll(); ?>
        <select name="f_review" id="feat_reviews">
            <option value="none">None</option>
            <?php foreach ($os as $osr) {
                $pr = (new ProductM())->where('id', $osr->id_product)->first(); ?>
                <option value="<?php echo $pr->id ?>"><?php echo $pr->name ?></option>
            <?php } ?>
        </select>

        <input type="submit" name="editbtn" class="btn" value=<?= "Edit" ?>>
    </form>
</div>