<div id="short-main">
    <!-- <h2>Award Points</h2> -->
    <form name='awardUser' action="<?= site_url("User/awardUserSubmit/{$awardee->id}") ?>" method="POST">
        <span style="font-size: 25px; vertical-align: middle;">Award points to <br> <img style="width: 30px; vertical-align: middle;" src="<?php user_avatar($awardee->id) ?>" /> <?php echo $awardee->nickname ?>.</span>

        <input type="range" id='coa' min="1" max="<?php echo $currentUser->points; ?>" value='0' step="1" name="points" onchange='document.getElementById("mybar").value = "" + document.getElementById("coa").value;'/>
        <input type="text" name="mybar" id="mybar" value="" disabled style="color:rgb(255, 196, 0); background-color:rgb(0,0,0,0); border:0" />
        <input type="submit" class="btn" name="action" value="Award User">
    </form>


    <?php
    if (isset($_POST["points"])) {
        echo "" . $_POST["points"];
        // Your Slider value is here, do what you want with it. Mail/Print anything..
    } else {
        echo "Please slide the bar and press award user.";
    }
    ?>

</div>