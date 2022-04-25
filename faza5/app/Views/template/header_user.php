<?= link_tag('styles.css') ?>
<script src="https://kit.fontawesome.com/7e034826bc.js" crossorigin="anonymous"></script>
<header>
    <ul>
        <li><a href=http://localhost:8080/ id="logo"><img src=assets/logo.png height=40px></a></li>
        <li><a href=http://localhost:8080 />STORE</a></li>
        <li style="float:right"><a href=http://localhost:8080/User/Logout>LOG OUT</a></li>
        <li style="float:right"><a href=http://localhost:8080/User/AddFunds><?php echo number_format($user->balance, 2); ?>$</a></li>
        <li style="float:right"><a href=http://localhost:8080/User/Profile><?php echo $user->username; ?></a></li>
    </ul>
</header>
<i class="fa-solid fa-flask-vial"></i>