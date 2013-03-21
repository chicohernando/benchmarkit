<?php
  require_once 'classes/domains/Benchmark.class.php';
  require_once 'classes/utilities/CurlUtility.class.php';

  if (isset($_POST['url']) && !empty($_POST['url'])) {
    try {
      $curlUtility = new CurlUtility();
      $benchmark = $curlUtility->getBenchmarkForUrl($_POST['url']);
    } catch (InvalidUrlException $exception) {
      //could put more specific error handling here in future
    } catch (InvalidBenchmarkException $exception) {
      //could put more specific error handling here in future
    } catch (Exception $exception) {
      //generic error handling
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="description" content="Simple benchmarking service" />
    <meta name="title" content="Benchmark It!" />
    <!-- using a css reset to make LAF/UX more consistent across browsers, linking directly to the source to prove I didn't write it / come up with it -->
    <link rel="stylesheet" href="http://meyerweb.com/eric/tools/css/reset/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/css/index.css" type="text/css" media="screen" />
  </head>
  <body>
    <div class="container">
      <form method="POST" action="index.php">
        <label for="url">URL</label>
        <input type="text" name="url" />
        <input type="submit" value="Benchmark this!" />
      </form>

      <div class="ajax_container">
        <?php include('partials/results.php'); ?>
      </div>
    </div>

    <!-- Put JS at bottom for faster page rendering -->
    <script src="http://code.jquery.com/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="js/index.js" type="text/javascript"></script>
  </body>
</html>