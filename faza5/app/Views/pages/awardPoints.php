<!--
Autori:
	Uros Loncar 2019/0691
	Djordje Stanojevic 2019/0288
	Fedja Mladenovic 2019/0613
	
Opis: Stranica za nagradjivanje korisnika

@version 1.1

-->

<?php

use App\Models\UserM;
?>

<title>Award Points</title>

<div id="short-main">
    <form style="text-align: center" name='awardUser' action="<?= site_url("User/awardUserSubmit/{$awardee->id}") ?>" method="POST">
        <span style="font-size: 25px;">Award points to <br> <img style="width: 30px;" src="<?php echo (new UserM())->getAvatar($awardee->id) ?>" /> <?php echo $awardee->nickname ?></span>
        <input type="range" id='coa' min="1" max="<?php echo $currentUser->points; ?>" value='1' step="1" name="points" oninput='document.getElementById("mybar").value = document.getElementById("coa").value + "P";' />
        <input type="text" name="mybar" id="mybar" value="1P" disabled style="font-size: 20px;width: 100%; text-align: center;color:rgb(255, 196, 0); background-color:rgb(0,0,0,0); border:0" />
        <input type="submit" class="btn" name="action" value="Award User">
    </form>
</div>