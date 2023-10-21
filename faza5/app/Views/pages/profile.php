<!--
Opis: Stranica profila korisnika, sa ispisanom listom prijatelja (sopstveni profil ili tudji)
Ako je tudji profil - mogucnost dodavanja, uklanjanja, odbijanja i prihvatanja zahteva za prijatelja

@version 1.1

-->

<?php

use App\Models\UserM;

$userM = new UserM();

if ($user != null && $user->id != $user_profile->id) {
    if ($relationship == -1) $button = "Add Friend";
    else if ($relationship == 0) $button = "Cancel Request";
    else if ($relationship == 1) $button = "Remove Friend";
    else if ($relationship == 2) $button = "Accept Request";
} ?>

<title><?php echo $user_profile->nickname ?></title>

<div id=main>
    <div id=profile-head style="display:flex;background-color:rgb(0,0,0,0.6);border-radius: 5px; max-height: 250px; min-width: 770px; margin: 0 0 15px 0">
        <div style="flex: 3;min-width: 620px;">
            <img class=smooth-border style="padding-right: 13px; float:left" src="<?php echo $avatar ?>">
            <h3 style="margin: 7px 0px"><?php echo $user_profile->nickname ?></h3>
            <i><?php echo $user_profile->real_name ?></i>
            <br><br>
            <span id="desc"><?php echo $user_profile->description ?></span>
        </div>
        <div style="flex: 1; text-align: right;">
            <?php if ($user != null && $user_profile == $user) { ?>
                <a href="http://localhost:8080/user/editprofile/"><input type="button" class="btn" value="Edit Profile" style="margin:0px;border-radius: 0 5px 5px 0;height: 100%"></a>
            <?php } else if ($user != null) { ?>
                <input type="submit" id="friend-button" name="<?= $relationship ?>" class=" btn" value="<?= $button ?>" style="margin:0;border-radius: 0 5px 0 0; height: 50%;">
                <a href="http://localhost:8080/user/awardUser/<?php echo $user_profile->id ?>"><input type="button" class="btn" value="Reward User" style="margin:0px;border-radius: 0 0 5px 0; height: 50%;"></a>
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
                    <?php foreach ($products[$user_profile->featured_review]['review'] as $line) { ?>
                        <p>
                            <?php echo $line ?>
                        </p>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p>This user didn't set his featured review.</p>
            <?php } ?>
        </div>
        <div style="flex:1;background-color:rgb(0,0,0,0.6);border-radius: 5px;padding: 0 10px 10px 10px; margin-left: 15px">
            <h2>Friends</h2>
            <div style="max-height: 550px; overflow-y: auto">

                <?php if (count($friends) > 0) {
                    foreach ($friends as $friend) { ?>
                        <a id="<?= $friend->id ?>" href="<?php echo site_url($controller . "/profile/" . $friend->id) ?>">
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

<?php if ($user != null && $user->id != $user_profile->id) { ?>
    <script>
        $(function() {
            $(document).on("click", "#friend-button", function() {
                $.ajax({
                    url: "<?= site_url("user/friendajax") ?>",
                    type: 'POST',
                    data: {
                        user: <?php echo $user_profile->id ?>,
                        relationship: $(this).attr("name"),
                    },
                    dataType: "JSON",
                    success: function(response) {
                        switch (response['state']) {
                            case -1:
                                $("#friend-button").attr("value", "Add Friend").attr("name", "-1");
                                break;
                            case 0:
                                $("#friend-button").attr("value", "Cancel Request").attr("name", "0");;
                                break;
                            case 1:
                                $("#friend-button").attr("value", "Remove Friend").attr("name", "1");;
                                break;
                            default:
                                console.log("No case match for: " + response['state']);
                        }

                        if (response['remove'] == true) $("#" + <?= $user->id ?>).hide(900);
                    },
                })
            })
        });
    </script>
<?php } ?>