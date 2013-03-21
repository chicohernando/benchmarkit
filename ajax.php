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

  include('partials/results.php');
?>