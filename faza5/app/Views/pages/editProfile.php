<!--
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691

Opis: Stranica za podesavanje podataka na svom profilu

-->

<title>Edit profile</title>

<h3>Edit profile</h3>

<?php
    use App\Models\UserM;
    $us = (new UserM())->where('id', $user->id)->first();
?>

<div id="main" style="margin: 100px auto; width: 325px; padding: 15px; border-radius: 9px;">
        <form name='friend_button' action="<?= site_url("User/Profile/")?>" method="POST" enctype="multipart/form-data">
            
            <h3>Avatar</h3>
            <input type="file" name="profile_pic" required accept="image/x-jpg">
            <?php if (!empty($errors['profile_pic'])) echo $errors['profile_pic'] ?>
            
            <h3>Nickname</h3>
            <input type="text" name="nickname" class="full" value="<?=
                
                set_value('nickname', $us->nickname);
            ?>" />
            <?php if (!empty($errors['nickname'])) echo $errors['nickname'] ?>
            
            <h3>Real Name</h3>
            <input type="text" name="real_name" class="full" value="<?=
                set_value('real_name', $us->real_name);
            ?>" />
            <?php if (!empty($errors['real_name'])) echo $errors['real_name'] ?>

            <h3>Location</h3>
            <!--
            <label for="country">Location:</label>
            <select name="country" id="country">
            <option value="srb" selected>Serbia</option>
            <option value="us">United States</option>
            <option value="eng">England</option>
            </select>
            -->
            <input type="text" name="location" class="full" value="<?=
                set_value('location', $us->country);
            ?>" />
            <?php if (!empty($errors['location'])) echo $errors['location'] ?>

            <h3>Description</h3>
            <input type="text" name="description" class="full" value="<?=
                set_value('description', $us->description);
            ?>" />
            <?php if (!empty($errors['description'])) echo $errors['description'] ?>
            
            <!-- <h3>Featured review</h3>
            <input type="text" name="review" class="full" value="<?php
                /*
                set_value('review', $us->featured_review);
                */
            ?>" /> -->
            
            <input type="submit" name="editbtn" class="btn" value=<?= "Edit" ?>>
        </form>
</div>