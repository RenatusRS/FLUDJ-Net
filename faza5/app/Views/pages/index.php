<!--
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691
	
Opis: Indeksna stranica
-->

<?= link_tag('search.css') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<title>FLUDJ Net</title>

<div id=main>
	<div style="margin-bottom: 10px;">
		<select class="search" name="search" style="width: 300px; color: black;"></select>
	</div>

	<div id=hero style="display:flex">
		<div class="video-container" style="flex: 4">
			<video autoplay muted loop>
				<source src="<?php echo base_url('uploads/product/' . $heroP->id . '/video.webm')  ?>">
			</video>
			<a href="<?php product_url($controller, $heroP->id) ?>">
				<div class="caption">

					<img src="<?php echo base_url('uploads/product/' . $heroP->id . '/banner.jpg')  ?>">
					<h2><?php echo $heroP->name ?></h2>
					<p><?php echo $heroP->description ?></p>
					<h3>BUY AT $<?php echo number_format($heroP->price, 2); ?></h3>
				</div>
			</a>
		</div>
		<?php if (count($topSellerP) >= 4) { ?>
			<div class="popular-products" style="flex: 1">
				<h3>Popular Products</h3>
				<?php for ($i = 0; $i < 4; $i++) { ?>
					<a href="<?php echo site_url($controller . "/product/" . $topSellerP[$i]->id) ?>">
						<div>
							<img src=" <?php echo base_url('uploads/product/' . $topSellerP[$i]->id . '/capsule.jpg') ?>">
							<span><?php echo $topSellerP[$i]->name ?></span>
						</div>
					</a>
				<?php } ?>
			</div>
		<?php } ?>
	</div>

	<?php if (count($discountedP) >= 4) { ?>
		<h2>Currently On Sale</h2>
		<div style="display:flex; margin: 0 -8px">
			<?php for ($i = 0; $i < 4; $i++) { ?>
				<a style="flex: 1; margin: 0 8px;" href="<?php product_url($controller, $discountedP[$i]->id) ?>">
					<img style="width: 100%;border-radius: 5px" src="<?php product_banner($discountedP[$i]->id) ?>">
					<span><?php echo $discountedP[$i]->name ?></span>
				</a>
			<?php } ?>
		</div>
	<?php } ?>

	<div style="background-color:rgb(0,0,0,0.6); border-radius: 5px; padding: 10px; margin: 10px 0">
		<div style="display:flex;align-items: center;">
			<div style="flex: 3">
				<h2>Use Your Coupons!</h2>
				<p style="text-align:justify">
					Get coupons by leaving quality reviews! Users reward other users with points to earn coupons, gather points by purchasing products!
				</p>
				<p style="text-align:justify">
					Currently you have coupons for:
				</p>
			</div>
			<?php for ($i = 0; $i < 3; $i++) {
				if ($i < count($couponP)) { ?>
					<a style="flex: 2; margin: 0 8px;" href="<?php product_url($controller, $couponP[$i]->id) ?>">
						<img style="width: 100%;border-radius: 5px" src="<?php product_banner($couponP[$i]->id) ?>">
						<span><?php echo $couponP[$i]->name ?></span>
					</a>
				<?php } else { ?>
					<div style="flex: 2; margin: 0 8px;">
					</div>
			<?php }
			} ?>
			<a style="flex: 2; margin: 0 8px;background-color:rgb(0,0,0,0.6);border-radius: 5px" href="http://localhost:8080/user/coupons/">
				<p style="text-align: center;">SEE ALL COUPONS</p>
			</a>
		</div>
	</div>

	<h2>Highest Rated</h2>
	<div style="display:flex;align-items: center;">
		<?php for ($i = 0; $i < 5; $i++) { ?>
			<a style=" flex: 1" href="<?php product_url($controller, $highRatingP[$i]->id) ?>">
				<img class="front-capsule" src="<?php product_capsule($highRatingP[$i]->id) ?>">
				<span><?php echo $highRatingP[$i]->name ?></span>
			</a>
		<?php } ?>
	</div>

	<?php if (count($friendsLikeP) >= 4) {
		$max = min(5, count($friendsLikeP));
	?>
		<h2>Your Friends Liked</h2>
		<div style="display:flex;align-items: center;; margin: 10px -8px">
			<?php for ($i = 0; $i < $max; $i++) { ?>
				<a style=" flex: 1; margin: 0 8px" href="<?php product_url($controller, $friendsLikeP[$i]->id) ?>">
					<img class="front-capsule" src="<?php product_banner($friendsLikeP[$i]->id) ?>">
					<span><?php echo $friendsLikeP[$i]->name ?></span>
				</a>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if (count($discoveryP) >= 5) { ?>
		<div style="background-color:rgb(0,0,0,0.6); border-radius: 5px; padding: 10px; margin: 10px 0">
			<h2>Discovery Queue</h2>
			<p>Our complex behind the scenes algorithm has picked these products specifically for you...</p>
			<div style="display:flex;">
				<?php for ($i = 0; $i < 5; $i++) { ?>
					<a style=" flex: 1; width: 100%;" href="<?php product_url($controller, $discoveryP[$i]->id) ?>">
						<img style="width: 100%;" src="<?php product_banner($discoveryP[$i]->id) ?>">
						<span><?php echo $discoveryP[$i]->name ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if (count($userLikeP) >= 4) {
		$max = min(6, count($userLikeP));
	?>
		<h2>Similar To Products You Own</h2>
		<div style="display:flex;align-items: center; margin: 10px -8px">
			<?php for ($i = 0; $i < $max; $i++) { ?>
				<a style=" flex: 1; margin: 0 8px" href="<?php product_url($controller, $userLikeP[$i]->id) ?>">
					<img class="front-capsule" src="<?php product_banner($userLikeP[$i]->id) ?>">
					<span><?php echo $userLikeP[$i]->name ?></span>
				</a>
			<?php } ?>
		</div>
	<?php } ?>

</div>
</div>

<script>
	$(function() {
		$('.search').select2({
			placeholder: '🔍 Search for a product',
			ajax: {
				url: '<?php echo base_url($controller . "/ajaxProductSearch"); ?>',
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
			var proizvod = $(".search option:selected").text();

			$.ajax({
				type: 'GET',
				url: '<?php echo base_url($controller . "/ajaxProductLoad/" . $controller); ?>',
				data: {
					ime: proizvod
				},
				dataType: 'html',
				success: function(response) {
					window.location.href = response;
				}
			});
		})
	});
</script>