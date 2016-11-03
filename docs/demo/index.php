<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="Joost De Cock">
        <title>Freesewing API Demo</title>
        <link href="../css/style.css" rel="stylesheet">
        <link href="../css/docs.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="demo.js"></script>
        <style>
            body {margin-bottom: 30px; }
            #logo { padding-bottom: 0; padding-top: 20px; margin: auto; text-align: center;}
            h1 { text-align: center; }
            #servicelist {margin-top: 20px; text-align: center;}
            blockquote h6 { text-align: center; }
            .clickable:hover { cursor: pointer; }
            .gapabove { margin-top: 40px; }
            .form-control { color: #333; }
            .uppercasefirst:first-letter { text-transform: uppercase; }
            svg { box-shadow: -3px -3px 3px 0px rgba(0, 0, 0, 0.19), 3px 0px 3px 0px rgba(0, 0, 0, 0.19), 0px 3px 3px 0px rgba(0, 0, 0, 0.23);}
        </style>
        <script type='text/javascript'>
            $( document ).ready(function() {
                loadServices();
                $(document).on('click', 'a#info', function() { loadApiInfo(); });
                $(document).on('click', 'a.pattern-info', function() { loadPatternInfo($(this).attr('data-pattern')); });
                $(document).on('click', 'a#draft', function() { sampleDraftPatternList('draft'); });
                $(document).on('click', 'a#sample', function() { sampleDraftPatternList('sample'); });
                $(document).on('click', 'a.pattern-draft', function() { loadDraft($(this).attr('data-pattern')); });
                $(document).on('click', 'a#draft-submit', function() { draftSubmit($(this).attr('data-pattern')); });
                $(document).on('click', 'a.pattern-sample', function() { loadSample($(this).attr('data-pattern')); });
            });   
        </script>
    </head>
    <body class="margin-top-l" style="background: #fff;">
        <div class="container">
            <div id='logo'>
                <a href='/docs/'><img id='logo' src='../media/logo.svg'></a>
            </div>
            <h1>Freesewing API Demo</h1>
            <div class="row"><div class="col-xs-12"><h2>Before we start: Services</h2></div></div>
            <div class="row">
                <div class="col-md-9">
                    <p>Out of the box, the Freesewing API provides 3 different services. They are:</p>
                    <h6>The info service</h6>
                    <p>Provides information about the services, patterns, channels, and themes available on the API.</p>
                    <h6>The draft service</h6>
                    <p>Drafts patterns according to your wishes.</p>
                    <h6>The sample service</h6>
                    <p>Samples patterns; Multiple drafts on top of each other, with changing measurements or options. (This will make sense when you try it, you'll see).</p>
                </div>
                <div id='services' class="col-md-3 col-xs-12">
                    <h3>Try them all</h3>
                </div>
            </div>
            <div class="row">
                <div id='content' class="col-xs-12">
                </div>
            </div>
            <div class="row gapabove">
                <div class="col-xs-12 col-md-8 col-md-offset-2">
                    <h2>What just happened?</h2>
                    <div id='details'>
                        <img src="spinner.gif">
                    </div>
                </div>
            </div>
        </div><!-- /.container -->
    </body>
</html>
