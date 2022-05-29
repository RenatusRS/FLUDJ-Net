<!--
Autori:
	Djordje Stanojevic 2019/0288
	Uros Loncar 2019/0691
	
Opis: Indeksna stranica
-->

<title>FLUDJ Net</title>

<?= link_tag('search.css') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<?php

use App\Models\ProductM;

?>

<div id=main>
	<div style="margin-bottom: 10px;">
		<select class="search" name="search" style="width: 300px; color: black;"></select>
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