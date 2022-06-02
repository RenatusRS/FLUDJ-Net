<header>
    <title><?php echo $title; ?></title>

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
            <?php } ?>
            <?php if (isset($bundle)) { ?>
                <a href="http://localhost:8080/admin/managebundle/<?php echo $bundle->id ?>">
                    <div><i class="bi bi-pencil"></i><br />Edit Bundle</div>
                </a>
            <?php } ?>
            <?php if (isset($user_profile) && $user != $user_profile) { ?>
                <?php if (!$user_profile->admin_rights) { ?>
                    <a href="http://localhost:8080/admin/promote/<?php echo $user_profile->id ?>">
                        <div><i class="bi bi-chevron-double-up"></i><br />Promote Admin</div>
                    </a>
                <?php } else { ?>
                    <a href="http://localhost:8080/admin/demote/<?php echo $user_profile->id ?>">
                        <div><i class="bi bi-chevron-double-down"></i><br />Demote Admin</div>
                    </a>
                <?php } ?>
                <?php if (!$user_profile->review_ban) { ?>
                    <a href="http://localhost:8080/admin/ban/<?php echo $user_profile->id ?>">
                        <div><i class="bi bi-slash-circle"></i><br />Review Ban</div>
                    </a>
                <?php } else { ?>
                    <a href="http://localhost:8080/admin/unban/<?php echo $user_profile->id ?>">
                        <div><i class="bi bi-circle"></i><br />Review Unban</div>
                    </a>
                <?php } ?>
            <?php } ?>
        </div>
    <?php } ?>


</header>