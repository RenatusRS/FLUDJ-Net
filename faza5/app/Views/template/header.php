<!--
Autori:
	Uros Loncar 2019/0691
	
Opis: Header template

@version 1.1

-->

<header>
    <ul>
        <li><a href="http://localhost:8080/" id="logo"><img src=http://localhost:8080/assets/logo.png height=40px></a></li>
        <li><a href="http://localhost:8080/">STORE</a></li>
        <?php if ($user == NULL) { ?>
            <li style="float:right"><a href="http://localhost:8080/guest/registration/">REGISTER</a></li>
            <li style="float:right"><a href="http://localhost:8080/guest/login/">SIGN-IN</a></li>
        <?php } else { ?>
            <li><a href="http://localhost:8080/user/profile/"> <?php echo $user->username  ?></a></li>
            <li style="float:right"><a href="http://localhost:8080/user/logout/">LOG OUT</a></li>
            <li style="float:right"><a href="http://localhost:8080/user/friendrequests/"><i class="bi bi-person-plus-fill"> </i></a></li>
            <li style="float:right"><a href="http://localhost:8080/user/coupons/"><?php echo $user->points ?>P</a></li>
            <li style="float:right"><a href="http://localhost:8080/user/addfunds/">$<?php echo number_format($user->balance, 2) ?></a></li>
        <?php } ?>
    </ul>

    <?php if ($user != NULL && $user->admin_rights) { ?>
        <div class="sidenav">
            <a href="http://localhost:8080/admin/manageproduct/">
                <div><i class="bi bi-plus-lg"></i><br />Add Product</div>
            </a>
            <a href="http://localhost:8080/admin/managebundle/">
                <div><i class="bi bi-plus-lg"></i><br />Add Bundle</div>
            </a>
            <?php if (isset($product)) { ?>
                <a href="http://localhost:8080/admin/manageproduct/<?php echo $product->id ?>">
                    <div><i class="bi bi-pencil"></i><br />Edit Product</div>
                </a>
                <a href="http://localhost:8080/admin/setdiscount/<?php echo $product->id ?>">
                    <div><i class="bi bi-cash-coin"></i><br />Set Discount</div>
                </a>
                <a href="http://localhost:8080/admin/deleteproduct/<?php echo $product->id ?>">
                    <div><i class="bi bi-trash"></i><br />Delete Product</div>
                </a>
            <?php } ?>
            <?php if (isset($bundle)) { ?>
                <a href="http://localhost:8080/admin/managebundle/<?php echo $bundle->id ?>">
                    <div><i class="bi bi-pencil"></i><br />Edit Bundle</div>
                </a>
                <a href="http://localhost:8080/admin/deletebundle/<?php echo $bundle->id ?>">
                    <div><i class="bi bi-trash"></i><br />Delete Bundle</div>
                </a>
            <?php } ?>
            <?php if (isset($user_profile) && $user != $user_profile) { ?>
                <div id="admin-promote" class="not-selectable" style="cursor: pointer;">
                    <?php if ($user_profile->admin_rights) { ?>
                        <i class="bi bi-chevron-double-down"></i><br />Demote Admin
                    <?php } else { ?>
                        <i class="bi bi-chevron-double-up"></i><br />Promote Admin
                    <?php } ?>
                </div>
                <div id="admin-ban" class="not-selectable" style="cursor: pointer;">
                    <?php if (!$user_profile->review_ban) { ?>
                        <i class=" bi bi-slash-circle"></i><br />Review Ban
                    <?php } else { ?>
                        <i class="bi bi-circle"></i><br />Review Unban
                    <?php } ?>
                </div>
                <a href="http://localhost:8080/admin/deleteuser/<?php echo $user_profile->id ?>">
                    <div><i class="bi bi-trash"></i><br />Delete User</div>
                </a>
            <?php } ?>
        </div>
    <?php } ?>


</header>

<?php if (isset($user_profile) && ($user != null) && ($user->admin_rights) && ($user != $user_profile)) { ?>
    <script>
        $(function() {
            $(document).on("click", "#admin-ban", function() {
                $.ajax({
                    url: "<?= site_url("admin/banajax") ?>",
                    type: 'POST',
                    data: {
                        user: <?php echo $user_profile->id ?>
                    },
                    dataType: "JSON",
                    success: function(response) {
                        if (response['state'] == 1) $("#admin-ban").html("<i class='bi bi-slash-circle'></i><br />Review Ban");
                        else $("#admin-ban").html("<i class='bi bi-circle'></i><br />Review Unban");
                    },
                })
            })

            $(document).on("click", "#admin-promote", function() {
                $.ajax({
                    url: "<?= site_url("admin/promoteajax") ?>",
                    type: 'POST',
                    data: {
                        user: <?php echo $user_profile->id ?>
                    },
                    dataType: "JSON",
                    success: function(response) {
                        if (response['state'] == 1) $("#admin-promote").html("<i class='bi bi-chevron-double-down'></i><br />Demote Admin");
                        else $("#admin-promote").html("<i class='bi bi-chevron-double-up'></i><br />Promote Admin");
                    },
                })
            })
        })
    </script>
<?php } ?>