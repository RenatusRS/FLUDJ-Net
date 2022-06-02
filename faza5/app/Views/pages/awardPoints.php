<form name='awardUser' action="<?= site_url("User/awardUserSubmit/{$awardee->id}") ?>" method="POST">
    <br><br>

    <div>Awarder: </div>
    <div><?php print_r($currentUser) ?></div>

    <br>

    <div>Awardee: </div>
    <div><?php print_r($awardee) ?></div>

    <input type="range" min="0" max="<?php echo $currentUser->points; ?>" value='0' step="1" name="points" />

    <input type="submit" class="btn" name="action" value="Award user">

</form>