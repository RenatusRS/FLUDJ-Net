<title>Add Discount</title>

<div id=short-main>
  <form name='addDiscountForm' action="<?= site_url("Admin/addDiscountSubmit/{$productId}") ?>" method="POST">
    <span class="input-label">Add Discount</span>
    <input type="text" name="discount" class="full" required />
    <?php
    if (!empty($errors['discount'])) echo $errors['discount'];
    ?>
    <input type="date" id="start" name="expDate" value="<?php echo date('Y-m-d'); ?>">
    <?php if (isset($message)) echo "$message" ?>
    <input type="submit" class="btn" value="CONTINUE">
  </form>
</div>