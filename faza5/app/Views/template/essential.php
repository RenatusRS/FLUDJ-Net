<!-- Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css">

<?= link_tag('styles.css') ?>

<style>
    body::after {
        content: "";
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 20%,
                rgba(18, 18, 18, 1)), url(<?php echo $background ?>);
        background-size: cover;
        background-repeat: no-repeat;
        opacity: 0.4;

        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        position: absolute;
        z-index: -1;
    }
</style>