<!--
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691
	
Opis: Stranica profila korisnika, sa ispisanom listom prijatelja (sopstveni profil ili tudji)
Ako je tudji profil - mogucnost dodavanja, uklanjanja, odbijanja i prihvatanja zahteva za prijatelja

-->

<title><?php echo $user_profile->nickname ?></title>

<?php
ob_start();

use App\Models\OwnershipM;
use App\Models\ProductM;
use App\Models\UserM;
use App\Models\RelationshipM;

$userM = new UserM();
$relationshipM = new RelationshipM();

if ($user->id != $user_profile->id) {
    $buttonName = "Add Friend";
    $cory = $relationshipM->asArray()->where('id_user1', $user->id)->where('id_user2', $user_profile->id)->where('status', 1)->orWhere('id_user1', $user_profile->id)->where('id_user2', $user->id)->where('status', 1)->findAll();

    if (sizeof($cory) > 0) $buttonName = "Remove Friend";
    $cory = $relationshipM->asArray()->where('id_user1', $user->id)->where('id_user2', $user_profile->id)->where('status', 0)->Orwhere('id_user1', $user_profile->id)->where('id_user2', $user->id)->where('status', 0)->findAll();

    if (sizeof($cory) > 0) {
        $value = $cory[0];
        if ($value['id_user1'] == $user->id) $buttonName = "Cancel Request";
        else $buttonName = "Accept Request";
    }

    if (isset($_POST['frnd_btn'])) {
        switch ($buttonName) {
            case "Add Friend":
                $relationshipM->insert([
                    'id_user1' => $user->id,
                    'id_user2' => $user_profile->id,
                    'status' => 0
                ]);
                break;

            case "Remove Friend":
                $relationshipM->where('id_user1', $user->id)->where('id_user2', $user_profile->id)->OrWhere('id_user1', $user_profile->id)->where('id_user2', $user->id)->delete();
                break;

            case "Accept Request":
                $relationshipM->set('status', 1)->where('id_user1', $user_profile->id)->where('id_user2', $user->id)->update();
                break;

            case "Cancel Request":
                $relationshipM->where('id_user1', $user->id)->where('id_user2', $user_profile->id)->delete();
                break;
        }

        unset($_POST['frnd_btn']);
        header("Refresh:0");
    }
} ?>

<div id=main>
    <div id=profile-head style="display:flex;background-color: black;border-radius: 5px; max-height: 250px;   min-width: 770px; margin: 0 0 15px 0">
        <div style="width:25%;float:left; min-width: 150px; position: relative;">
            <img width=100% class=smooth-border style="padding-right: 13px" src="<?php echo $avatar ?>">
        </div>
        <div style="width:120%;float:left;align-content: left; justify-content: left;  min-width: 620px">
            <h3 style="margin: 7px 0px"><?php echo $user_profile->nickname ?></h3>
            <i><?php echo $user_profile->real_name ?></i>
            <br><br>
            <?php echo $user_profile->description ?>
        </div>
        <div style="width:25%;float:right">
            <?php if ($user_profile == $user) { ?>
                <a href="http://localhost:8080/user/editprofile/"><input type="button" class="btn" value="Edit Profile" style="margin:0px;border-radius: 0 5px 0 0;max-height: 100%;height: 100%;max-width: 250px;"></a>
            <?php } else { ?>
                <form name='friend_button' action="<?= site_url("user/profile/" . $user_profile->id); ?>" method="POST" style="margin: 0">
                    <input type="submit" name="frnd_btn" class="btn" value="<?= $buttonName ?>" style="margin:0px;border-radius: 0 5px 0 0;max-height: 50%;height: 50%;max-width: 250px;">
                </form>
                <a href="http://localhost:8080/user/reward/<?php echo $user_profile->id ?>"><input type="button" class="btn" value="Reward User" style="margin:0px;border-radius: 0 0 5px 0;max-height: 50%;height: 50%;max-width: 250px;"></a>
            <?php } ?>
        </div>
    </div>
    <div style="min-width: 885px;">
        <div style="background-color: black;border-radius: 5px;float:left; padding: 0 10px 10px 10px">
            <h2>Products</h2>
            <div style="display:flex; overflow-y: scroll;">

                <?php if (count($products) > 0) {
                    foreach ($products as $product) { ?>
                        <a href="<?php echo site_url($controller . "/product/" . $product['product']->id) ?>">
                            <div style="margin:5px; flex: 1">
                                <img style=" width:25%;vertical-align: middle" src="<?php echo base_url('uploads/product/' . $product['product']->id . '/banner.jpg')  ?>">
                                <br>
                                <span style="vertical-align: middle"><?php echo $product['product']->name ?></span>
                            </div>
                        </a>
                    <?php }
                } else { ?>
                    <p>This user doesn't own any products.</p>
                <?php } ?>
            </div>
            <?php if ($user_profile->featured_review != null) { ?>
                <div>
                    <h2>
                        Featured Review
                    </h2>
                    <img src="<?php echo base_url("uploads/product/" . $user_profile->featured_review . "/banner.jpg") ?>">
                </div>
            <?php } ?>
        </div>
        <div style="background-color: black;border-radius: 5px;float:right; width: 15%; padding: 0 10px 10px 10px">
            <h2>Friends</h2>
            <?php if (count($friends) > 0) {
                foreach ($friends as $friend) { ?>
                    <a href="<?php echo site_url($controller . "/profile/" . $friend->id) ?>">
                        <div style="margin:5px">
                            <img style=" width:25%;vertical-align: middle" src="<?php echo $userM->getAvatar($friend->id) ?>">
                            <span style="vertical-align: middle"><?php echo $friend->nickname ?></span>
                        </div>
                    </a>
                <?php }
            } else { ?>
                <p>This user has no friends.</p>
            <?php } ?>
        </div>
    </div>
</div>