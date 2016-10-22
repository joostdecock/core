<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Joost De Cock">
    <title>Try the API</title>
    <link href="../css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style>
      svg { box-shadow: -3px -3px 3px 0px rgba(0, 0, 0, 0.19), 3px 0px 3px 0px rgba(0, 0, 0, 0.19), 0px 3px 3px 0px rgba(0, 0, 0, 0.23);}
      table.mmp-form td.key { width: inherit; vertical-align: middle;}
    </style>
    <script type='text/javascript'>
      $( document ).ready(function() {
        $("#form").submit(function(e) {
          var url = "/api/";
          $.ajax({
            type: "POST",
            url: url,
            data: $("#form").serialize(), // serializes the form's elements.
            success: function(data)
            {
                data = jQuery.parseJSON(data);
                $('#status').html(data.status);
                $('#debug_data').html(data.debug);
                $('#svg_data').html(data.svg);
                $('#responsetab').tab('show');
            }
          });
          e.preventDefault(); // avoid to execute the actual submit of the form.
        });
      });   
    </script>
  </head>
  <body class="margin-top-l" style="background: #fff;">
    <div class="container">
      <div class="jumbotron scroll-top margin-top-50">
        <h1>Try the freeSewing API</h1>
        <p>Or <a href="../">return to the API documentation</a></p>
      </div>
      <div class="row margin-top-30">
        <div class="col-xs-12">
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#request" aria-controls="request" role="tab" data-toggle="tab">Request</a></li>
            <li role="presentation"><a id="responsetab" href="#response" aria-controls="profile" role="tab" data-toggle="tab">Response</a></li>
            <li role="presentation"><a href="#svg" aria-controls="messages" role="tab" data-toggle="tab">SVG</a></li>
          </ul>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="request">
              <h2>Request</h2>
              <?php echo file_get_contents('form.html'); ?>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="response">
              <h2>Response</h2>
              <p>Status: <span id="status"></span><br>Full apiHandler object:</p>
              <div id='debug_data'></div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="svg">
              <h2>SVG</h2>
              <div id='svg_data'></div>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.container -->
  </body>
</html>
