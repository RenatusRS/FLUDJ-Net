<!DOCTYPE html>
<html lang="en">


<link rel="stylesheet" href="styles.css">

<head>
	<meta charset="utf-8">
	<title>Not Found</title>
</head>

<body>
	<div class="wrap">
		<h1>Not Found</h1>

		<p>
			<?php if (!empty($message) && $message !== '(null)') : ?>
				<?= nl2br(esc($message)) ?>
			<?php else : ?>
				Sorry! Cannot seem to find the page you were looking for.
			<?php endif ?>
		</p>

		<a href=http://localhost:8080 />Return Home</a>
	</div>
</body>

</html>