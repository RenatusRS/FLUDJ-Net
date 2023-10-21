<!--
Opis: Template koji se ucitava na svakoj stranici

@version 1.1

-->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?= link_tag('styles.css') ?>

<style>
    body::after {
        content: "";
        background: linear-gradient(to bottom,
                rgba(255, 255, 255, 0) 0%,
                rgba(18, 18, 18, 1)),
            url(<?php echo $background ?>);
        background-size: cover;
        background-repeat: no-repeat;
        opacity: 0.4;

        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        position: fixed;
        z-index: -1;
    }
</style>


<script type="text/javascript">
    $(document).ready(function() {
        // Images loaded is zero because we're going to process a new set of images.
        var imagesLoaded = 0;
        // Total images is still the total number of <img> elements on the page.
        var totalImages = $("img").length;

        // Step through each image in the DOM, clone it, attach an onload event
        // listener, then set its source to the source of the original image. When
        // that new image has loaded, fire the imageLoaded() callback.
        $("img").each(function(idx, img) {
            $("<img>").on("load", imageLoaded).attr("src", $(img).attr("src"));
        });

        // Do exactly as we had before -- increment the loaded count and if all are
        // loaded, call the allImagesLoaded() function.
        function imageLoaded() {
            imagesLoaded++;

            if (imagesLoaded == totalImages) {
                allImagesLoaded();
            }
        }

        function allImagesLoaded() {
            $("#loading").hide();
            $('#main').show();
            $('#middle-main').show();
            $('#short-main').show();
        }
    });
</script>