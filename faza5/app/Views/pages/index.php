<!--
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691
	
Opis: Indeksna stranica
-->

<title>FLUDJ Net</title>

<div id=main>
	<div id=hero style="display:flex">
		<div class="video-container">
			<video autoplay muted loop>
				<source src="<?php

								use App\Models\ProductM;

								echo base_url('uploads/product/' . $heroP->id . '/video.webm')  ?>">
			</video>
			<div class="caption">
				<img src="<?php echo base_url('uploads/product/' . $heroP->id . '/banner.jpg')  ?>">
				<h2><?php echo $heroP->name ?></h2>
				<p><?php echo $heroP->description ?></p>
				<h3>BUY AT $<?php echo number_format($heroP->price, 2); ?></h3>
			</div>
		</div>
		<div class="popular-products">
			<h3>Popular Products</h3>
			<?php $cnt = min(5, count($topSellerP)); for ($i = 0; $i < $cnt; $i++) { ?>
				<a href="<?php echo site_url("Product/" . $topSellerP[$i]->id) ?>">
					<div>
						<img src=" <?php echo base_url('uploads/product/' . $topSellerP[$i]->id . '/capsule.jpg') ?>">
						<span><?php echo $topSellerP[$i]->name ?></span>
					</div>
				</a>
			<?php } ?>
		</div>
	</div>
	<div class="sale-products">
		<h3>Products On Sale</h3>
		<table style="width: 100%; border-collapse: collapse; margin-left: auto; margin-right: auto;" border="0" cellpadding="5px">
			<tbody>
				<tr>
					<?php $cnt = min(5, count($discountedP)); for ($i = 0; $i < $cnt; $i++)  { ?>
						<td style="width: 16.667%;">
							<a href="<?php echo site_url("Product/" . $discountedP[$i]->id) ?>">
								<div>
									<img src=" <?php echo base_url('uploads/product/' . $discountedP[$i]->id . '/banner.jpg') ?>">
									<p class="product-name"><?php echo $discountedP[$i]->name ?></p>
									<?php
									$discount = (new ProductM())->getDiscount($discountedP[$i]->id);
									$discountedPrice = (new ProductM())->getDiscountedPrice($discountedP[$i]->id);

									if ($discount != 0) { ?>
										<span class="discount"><?php echo $discount ?>%</span> <span class="price-original"><?php echo number_format($discountedP[$i]->price, 2) ?></span>
									<?php } ?>
									<span class="price"><?php echo number_format($discountedPrice, 2) ?></span>
								</div>
							</a>
						</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>

	</div>
</div>