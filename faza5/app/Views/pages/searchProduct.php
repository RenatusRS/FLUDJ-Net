<!--
Autori:
	Djordje Stanojevic 2019/0288

Opis: Stranica za pretragu proizvoda

-->

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Search</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

  <style>
    .container {
      max-width: 500px;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <select class="search form-control" name="search"></select>
  </div>
</body>

<script>
  $(function() {
    $('.search').select2({
      placeholder: 'Search for a product: ',
      ajax: {
        url: '<?php echo base_url('User/ajaxProductSearch'); ?>',
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
      var nst = $(".search option:selected").text();

      $.ajax({
        type: 'GET',
        url: '<?php echo base_url('User/ajaxProductLoad'); ?>',
        data: {
          tst: nst
        },
        dataType: 'html',
        success: function(response) {
          window.location.href = response;
        }
      });

    })

  });
</script>

</html>