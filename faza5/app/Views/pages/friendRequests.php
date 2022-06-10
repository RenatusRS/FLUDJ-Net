<!--
Autori:
    Uros Loncar 2019/0691
	Djordje Stanojevic 2019/0288

Opis: Stranica za pregled zahteva za prijateljstvo, uz mogucnost prihvatanja i odbijanja dolazecih
i ponistenja od poslatog zahteva

@version 1.1

-->

<?= link_tag('search.css') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<?php

use App\Models\RelationshipM;
use App\Models\UserM;

$relationshipM = new RelationshipM();
$userM = new UserM();

?>

<title>Friend Requests</title>

<div id=main>
    <div>
        <select class="search" name="search" style="width: 300px; color: black;"></select>
    </div>
    <div style="display:flex">
        <div style="flex: 1;">
            <h2>INCOMING REQUESTS</h2>
            <?php
            if (count($requesters) == 0) { ?>
                <p>
                    You don't have any incoming friend requests.
                </p>
            <?php }
            foreach ($requesters as $requester) {
                if (isset($_POST[$requester->id . "ACCEPT"])) {
                    $relationshipM->set('status', 1)->where('id_user2', $user->id)->where('id_user1', $requester->id)->update();
                    header("Refresh:0");
                }

                if (isset($_POST[$requester->id . "REJECT"])) {
                    $relationshipM->where('id_user1', $requester->id)->where('id_user2', $user->id)->delete();
                    header("Refresh:0");
                } ?>

                <div id=<?= $requester->id ?> style="margin: 15px 15px 0 0; min-width: 330px;">
                    <a href="http://localhost:8080/user/profile/<?php echo $requester->id ?>">
                        <div style="width:75%; float:left;background-color: black;border-radius: 5px 0 0 5px">
                            <img src=" <?php echo $userM->getAvatar($requester->id) ?>" style="width:70px; vertical-align: middle;border-radius: 5px 0 0 5px" /> <span style=" vertical-align: middle; font-size: 22px;"><?php echo $requester->nickname ?></span>
                        </div>
                    </a>
                    <div style="width:25%;float:left;">
                        <div style="float:left">
                            <input type="button" id=<?= $requester->id ?> class="btn accept-button" value="✔" style="border-radius:0; height: 70px; margin: 0">
                        </div>
                        <div style="float:left">
                            <input type="button" id=<?= $requester->id ?> class="btn reject-button" value="✘" style="border-radius:0; height: 70px; margin: 0">
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div style="flex: 1;">
            <h2>OUTGOING REQUESTS</h2>
            <?php
            if (count($requestedTo) == 0) { ?>
                <p>
                    You don't have any outgoing friend requests.
                </p>
            <?php }
            foreach ($requestedTo as $requestedToUser) {
                if (isset($_POST[$requestedToUser->id . "CANCEL"])) {
                    $relationshipM->where('id_user1', $user->id)->where('id_user2', $requestedToUser->id)->delete();
                    header("Refresh:0");
                } ?>

                <div id=<?= $requestedToUser->id ?> style="margin: 15px 15px 0 0; min-width: 330px;">
                    <a href="http://localhost:8080/user/profile/<?php echo $requestedToUser->id ?>">
                        <div style="width:87%; float:left;background-color: black;border-radius: 5px 0 0 5px">
                            <img src="<?php echo $userM->getAvatar($requestedToUser->id) ?>" style="width:70px; vertical-align: middle;border-radius: 5px 0 0 5px" /> <span style="vertical-align: middle; font-size: 22px;"><?php echo $requestedToUser->nickname ?></span>
                        </div>
                    </a>
                    <div style="width:13%;float:left;">
                        <div style="float:left;">
                            <input type="button" id=<?= $requestedToUser->id ?> class="btn cancel-button" value="✘" style="border-radius:0; height: 70px; margin: 0">
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('.search').select2({
            placeholder: '🔍 Search for a user',
            ajax: {
                url: '<?php echo base_url('user/ajaxUserSearch'); ?>',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        $('.search').on('change', function() {
            //nakon odabira
            var korisnik = $(".search option:selected").text();

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url('user/ajaxUserLoad'); ?>',
                data: {
                    nadimak: korisnik
                },
                dataType: 'html',
                success: function(response) {
                    window.location.href = response;
                }
            });
        })

        $(document).on("click", ".cancel-button", function() {
            id = $(this).attr("id");

            $.ajax({
                url: "<?= site_url("user/friendajax") ?>",
                type: 'POST',
                data: {
                    user: id,
                    relationship: 0,
                },
                dataType: "JSON",
                success: function(response) {
                    $("#" + id).hide(900);
                },
            })
        })

        $(document).on("click", ".accept-button", function() {
            id = $(this).attr("id");

            $.ajax({
                url: "<?= site_url("user/friendajax") ?>",
                type: 'POST',
                data: {
                    user: id,
                    relationship: 2,
                },
                dataType: "JSON",
                success: function(response) {
                    $("#" + id).hide(900);
                },
            })
        })

        $(document).on("click", ".reject-button", function() {
            id = $(this).attr("id");

            $.ajax({
                url: "<?= site_url("user/friendajax") ?>",
                type: 'POST',
                data: {
                    user: id,
                    relationship: 3,
                },
                dataType: "JSON",
                success: function(response) {
                    $("#" + id).hide(900);
                },
            })
        })
    });
</script>