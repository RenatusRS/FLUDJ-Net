<!--
Autori:
	Djordje Stanojevic 2019/0288

Opis: Stranica za pregled zahteva za prijateljstvo, uz mogucnost prihvatanja i odbijanja dolazecih
i ponistenja od poslatog zahteva

-->

<title>Friend requests</title>

<h3>Friend requests</h3>


<h4>Incoming:</h4>
<?php
    foreach ($requesters as $requester) {
    ?>
        <option value="<?php echo $requester->id ?>"> <?php echo $requester->username ?> </option>
        <?php
            if (isset($_POST[$requester->id."ACCEPT"])) {
                $builder = \Config\Database::connect()->table('relationship');
                $builder->set('status', 1)->where('id_user2', $user->id)->where('id_user1', $requester->id)->update();
                unset($_POST[$requester->id."ACCEPT"]);
                header("Refresh:0");
            }
            if (isset($_POST[$requester->id."REJECT"])) {
                $builder = \Config\Database::connect()->table('relationship');
                $builder->where('id_user1', $requester->id)->where('id_user2', $user->id)->delete();
                unset($_POST[$requester->id."REJECT"]);
                header("Refresh:0");
            }
        ?>
        <div id="<?php echo $requester->id."requester" ?>>" style="margin: 100px auto; width: 325px; padding: 15px; border-radius: 9px;">
        <form name='fr_accept_btn' action="<?= site_url("User/FriendRequests"); ?>" method="POST">
            <input type="submit" name=<?= $requester->id."ACCEPT" ?> class="btn" value=<?= "ACCEPT" ?>>
        </form>
        <form name='fr_reject_btn' action="<?= site_url("User/FriendRequests"); ?>" method="POST">
            <input type="submit" name=<?= $requester->id."REJECT" ?> class="btn" value=<?= "REJECT" ?>>
        </form>
        </div>

    <?php
    }
?>

<h4>Outgoing:</h4>
<?php
    foreach ($requestedTo as $requestedToUser) {
    ?>
        <option value="<?= $requestedToUser->id ?>"> <?php echo $requestedToUser->username ?> </option>
        <?php
            if (isset($_POST[$requestedToUser->id."CANCEL"])) {
                $builder = \Config\Database::connect()->table('relationship');
                $builder->where('id_user1', $user->id)->where('id_user2', $requestedToUser->id)->delete();
                unset($_POST[$requestedToUser->id."CANCEL"]);
                header("Refresh:0");
            }
        ?>
        <div id="<?php echo $requestedToUser->id ?>>" style="margin: 100px auto; width: 325px; padding: 15px; border-radius: 9px;">
        <form name='fr_cancel_btn' action="<?= site_url("User/FriendRequests"); ?>" method="POST">
            <input type="submit" name=<?= $requestedToUser->id."CANCEL" ?> class="btn" value=<?= "CANCEL" ?>>
        </form>
        </div>

        
    <?php
    }
?>