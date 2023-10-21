<!--
Opis: Stranica za postavljanje popusta

@version 1.1

-->

<title>Set Discount</title>

<div id=short-main>
	<form name='setDiscountForm' action="<?= site_url("Admin/setDiscountSubmit/{$productId}") ?>" method="POST">
		<span class="input-label">Set Discount</span>
		<input type="text" name="discount" class="full" required />
		<?php
		if (!empty($errors['discount'])) echo $errors['discount'];
		?>
		<input type="date" id="start" name="expDate" value="<?php echo date('Y-m-d'); ?>">
		<?php if (isset($message)) echo "$message" ?>
		<input type="submit" class="btn" value="CONTINUE">
	</form>
</div>