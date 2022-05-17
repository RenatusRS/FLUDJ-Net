<!--
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691
	
Opis: Stranica profila korisnika, sa ispisanom listom prijatelja (sopstveni profil ili tudji)
Ako je tudji profil - mogucnost dodavanja, uklanjanja, odbijanja i prihvatanja zahteva za prijatelja

-->

<title><?php echo $user_profile->nickname ?></title>
<h3>Profile</h3>

<?php
ob_start();

use App\Models\UserM;
use App\Models\RelationshipM;
?>

<h2>Friends</h2>
<?php
$id = $user_profile->id;
$r = (new RelationshipM())->asArray()->where('id_user1', $id)->where('status', true)->orWhere('id_user2', $id)->where('status', true)->findAll();
foreach ($r as $row) {
    $friend = $row['id_user1'] == $id ? $row['id_user2'] :  $row['id_user1'];
    $usM = (new UserM())->where('id', $friend)->first();
    echo '<h5>' . $usM->nickname . '</h5>';
}

if ($user->id == $user_profile->id)
    echo "Your profile.";
else {
    echo "Profile of $user_profile->nickname";
    $buttonName = "ADD_FRIEND";
    $cory = (new RelationshipM())->asArray()->where('id_user1', $user->id)->where('id_user2', $user_profile->id)->where('status', 1)->orWhere('id_user1', $user_profile->id)->where('id_user2', $user->id)->where('status', 1)->findAll();
    //"SELECT * FROM `relationships` WHERE (id_user1=$user->id AND id_user2=$user_profile->id AND status=true) OR (id_user1=$user_profile->id AND id_user2=$user->id AND status=true)"
    if (sizeof($cory) > 0) $buttonName = "REMOVE_FRIEND";
    $cory = (new RelationshipM())->asArray()->where('id_user1', $user->id)->where('id_user2', $user_profile->id)->where('status', 0)->Orwhere('id_user1', $user_profile->id)->where('id_user2', $user->id)->where('status', 0)->findAll();
    //"SELECT `id_user1` FROM `relationships` WHERE (id_user1=$user->id AND id_user2=$user_profile->id AND status=false) OR (id_user1=$user_profile->id AND id_user2=$user->id AND status=false)"
    if (sizeof($cory) > 0) {
        $value = $cory[0];
        if ($value['id_user1'] == $user->id) {
            $buttonName = "CANCEL_REQUEST";
        } else {
            $buttonName = "ACCEPT_REQUEST";
        }
    }
?>
    <?php
    if (isset($_POST['frnd_btn'])) {
        $builder = \Config\Database::connect()->table('relationship');
        if ($buttonName == "ADD_FRIEND") {
            $data = ['id_user1' => $user->id, 'id_user2' => $user_profile->id, 'status' => 0,];
            $builder->insert($data);
        } else if ($buttonName == "CANCEL_REQUEST") {
            $builder->where('id_user1', $user->id)->where('id_user2', $user_profile->id)->delete();
        } else if ($buttonName == "ACCEPT_REQUEST") {
            $builder->set('status', 1)->where('id_user1', $user_profile->id)->where('id_user2', $user->id)->update();
        } else if ($buttonName == "REMOVE_FRIEND") {
            $builder->where('id_user1', $user->id)->where('id_user2', $user_profile->id)->OrWhere('id_user1', $user_profile->id)->where('id_user2', $user->id)->delete();
        }
        unset($_POST['frnd_btn']);
        header("Refresh:0");
    }
    ?>

    <div id="main" style="margin: 100px auto; width: 325px; padding: 15px; border-radius: 9px;">
        <form name='friend_button' action="<?= site_url("User/Profile/" . $user_profile->id); ?>" method="POST">
            <input type="submit" name="frnd_btn" class="btn" value=<?= $buttonName ?>>
        </form>
    </div>

<?php
} //end else - drugi profil
?>