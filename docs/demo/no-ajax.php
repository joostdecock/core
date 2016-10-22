<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Joost De Cock">
    <title>Try the API</title>
    <link href="/themes/siobhan/css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style>
      table.mmp-form td.key { width: inherit; vertical-align: middle;}
    </style>
  </head>
  <body class="margin-top-l" style="background: #fff;">
    <div class="container">
      <div class="row margin-top-30">
        <div class="col-xs-12">
          <h1>Freesewing.org API</h1>
          <h2>Try it yourself</h2>
          <p>Or <a href="../">return to the API documentation</a></p>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <h2>Request</h2>
          <?php echo file_get_contents('form.html'); ?>
        </div>
      </div>
    </div><!-- /.container -->
  </body>
</html>
