<!--
Autori:
	Djordje Stanojevic 2019/0288

Opis: Stranica za pregled zahteva za prijateljstvo, uz mogucnost prihvatanja i odbijanja dolazecih
i ponistenja od poslatog zahteva

-->

<title>Friend Requests</title>

<h3>Friend Requests</h3>


<h4>Incoming:</h4>
<?php

use App\Models\RelationshipM;

$relationshipM = new RelationshipM();

foreach ($requesters as $requester) {

    echo $requester->username;

    if (isset($_POST[$requester->id . "ACCEPT"])) {
        $relationshipM->set('status', 1)->where('id_user2', $user->id)->where('id_user1', $requester->id)->update();
        header("Refresh:0");
    }

    if (isset($_POST[$requester->id . "REJECT"])) {
        $relationshipM->where('id_user1', $requester->id)->where('id_user2', $user->id)->delete();
        header("Refresh:0");
    }

?>

    <div id="<?php echo $requester->id . "requester" ?>>" style="margin: 100px auto; width: 325px; padding: 15px; border-radius: 9px;">
        <form name='fr_accept_btn' action="<?= site_url("User/FriendRequests"); ?>" method="POST">
            <input type="submit" name=<?= $requester->id . "ACCEPT" ?> class="btn" value=<?= "ACCEPT" ?>>
        </form>
        <form name='fr_reject_btn' action="<?= site_url("User/FriendRequests"); ?>" method="POST">
            <input type="submit" name=<?= $requester->id . "REJECT" ?> class="btn" value=<?= "REJECT" ?>>
        </form>
    </div>

<?php
}
?>

<h4>Outgoing:</h4>
<?php
foreach ($requestedTo as $requestedToUser) {

    echo $requestedToUser->username;

    if (isset($_POST[$requestedToUser->id . "CANCEL"])) {
        $relationshipM->where('id_user1', $user->id)->where('id_user2', $requestedToUser->id)->delete();
        header("Refresh:0");
    }

?>
    <div id="<?php echo $requestedToUser->id ?>>" style="margin: 100px auto; width: 325px; padding: 15px; border-radius: 9px;">
        <form name='fr_cancel_btn' action="<?= site_url("User/FriendRequests"); ?>" method="POST">
            <input type="submit" name=<?= $requestedToUser->id . "CANCEL" ?> class="btn" value=<?= "CANCEL" ?>>
        </form>
    </div>

<?php
}
?>