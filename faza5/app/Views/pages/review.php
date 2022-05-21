<title>Make Review</title>


<form name='makeReviewForm' action="<?= site_url("User/makeReviewSubmit/{$product->id}") ?>" method="POST">
  <br><br>
<div>
  <label for="review">Review</label>
  <br>
  <textarea name="text" id="" cols="30" rows="10"></textarea>
  </div>
  <br>
 <div>
  <label for="rating">Rating</label>
  <br>
  <input type="range" id="rating" name="rating"
         min="1" max="5">
         </div>
  <br>
  <?php if (isset($message)) echo "$message" ?>
  <br>
  <input type="submit" class="btn" value="Confirm">
</form>