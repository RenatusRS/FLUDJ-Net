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

if ($user != null && $user->id != $user_profile->id) {
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
    <div id=profile-head style="display:flex;background-color:rgb(0,0,0,0.6);border-radius: 5px; max-height: 250px; min-width: 770px; margin: 0 0 15px 0">
        <div style="flex: 3;min-width: 620px;">
            <img class=smooth-border style="padding-right: 13px; float:left" src="<?php echo $avatar ?>">
            <h3 style="margin: 7px 0px"><?php echo $user_profile->nickname ?></h3>
            <i><?php echo $user_profile->real_name ?></i>
            <br><br>
            <?php echo $user_profile->description ?>
        </div>
        <div style="flex: 1; text-align: right;">
            <?php if ($user != null && $user_profile == $user) { ?>
                <a href="http://localhost:8080/user/editprofile/"><input type="button" class="btn" value="Edit Profile" style="margin:0px;border-radius: 0 5px 5px 0;height: 100%"></a>
            <?php } else if ($user != null) { ?>
                <form name='friend_button' action="<?= site_url("user/profile/" . $user_profile->id); ?>" method="POST" style="margin: 0">
                    <input type="submit" name="frnd_btn" class="btn" value="<?= $buttonName ?>" style="margin:0px;border-radius: 0 5px 0 0; height: 50%;">
                </form>
                <a href="http://localhost:8080/user/reward/<?php echo $user_profile->id ?>"><input type="button" class="btn" value="Reward User" style="margin:0px;border-radius: 0 0 5px 0; height: 50%;"></a>
            <?php } ?>
        </div>
    </div>
    <div style="display: flex;">
        <div style="background-color: rgb(0,0,0,0.6);border-radius: 5px;flex:3; padding: 0 10px 10px 10px; min-width: 630px;">
            <h2>Products</h2>
            <div style=" overflow-x: auto; white-space: nowrap;">
                <?php if (count($products) > 0) {
                    foreach ($products as $product) { ?>
                        <a style="padding: 2px; width: 175px;margin:5px;display: inline-block;border-radius: 5px;border: 1px solid <?php
                                                                                                                                    echo ($user_profile->featured_review != null && $product['product'] == $products[$user_profile->featured_review]['product']) ? "rgb(255, 196, 0, 0.8)" : "rgb(0, 0, 0, 0)" ?>" href="<?php echo site_url($controller . "/product/" . $product['product']->id) ?>">
                            <img style="width: 100%;vertical-align: middle" src="<?php echo base_url('uploads/product/' . $product['product']->id . '/banner.jpg')  ?>">
                            <br>
                            <span style="vertical-align: middle; font-size: 15px;"><?php echo $product['product']->name ?></span>
                        </a>
                    <?php }
                } else { ?>
                    <p>This user doesn't own any products.</p>
                <?php } ?>
            </div>
            <h2>
                Featured Review <?php if ($user_profile->featured_review != null) {
                                    echo " - " . $products[$user_profile->featured_review]['product']->name . " ";
                                    for ($i = 1; $i <= 5; $i++) echo $i <= $products[$user_profile->featured_review]['rating'] ? "★" : "☆";
                                } ?>
            </h2>
            <?php if ($user_profile->featured_review != null) { ?>
                <div style="text-align:justify">
                    <?php echo $products[$user_profile->featured_review]['review'] ?>
                </div>
            <?php } else { ?>
                <p>This user didn't set his featured review.</p>
            <?php } ?>
        </div>
        <div style="flex:1;background-color:rgb(0,0,0,0.6);border-radius: 5px;padding: 0 10px 10px 10px; margin-left: 15px">
            <h2>Friends</h2>
            <div style="max-height: 700px; overflow-y: auto">

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
</div>