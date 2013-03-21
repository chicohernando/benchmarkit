<?php
  require_once dirname(dirname(__FILE__)) . '/exceptions/InvalidUrlException.class.php';
  require_once dirname(dirname(__FILE__)) . '/domains/Benchmark.class.php';
  class CurlUtility {
    protected $_ch = null;
    
    public function __construct() {
      //make sure that curl is available to use, some systems don't have it
      if (function_exists('curl_init')) {
        $this->_ch = curl_init();
      }
    }

    /**
     * Returns a Benchmark for the given url
     * 
     * @param string $url
     * @return Benchmark
     * @throws InvalidUrlException
     * @throws InvalidBenchmarkException
     */
    public function getBenchmarkForUrl($url) {
      return $this->_ch ? $this->_getBenchmarkForUrlWithCurl($url) : $this->_getBenchmarkForUrlWithoutCurl($url);
    }

    /**
     * Internal function that will generate a Benchmark based on results using cURL
     * 
     * @param string $url
     * @return Benchmark
     * @throws InvalidUrlException
     * @throws InvalidBenchmarkException
     */
    protected function _getBenchmarkForUrlWithCurl($url) {
      //set the options for what we want cURL to do
      $curlOptions = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true, //this makes it return the data as a string instead of echo'ing it
        CURLOPT_FOLLOWLOCATION => true, //this tells cURL to follow redirects, otherwise invalid results will arise
        CURLOPT_MAXREDIRS => 5, //set a maximum number of redirects, otherwise you could end up with an infinite loop,
        CURLOPT_CONNECTTIMEOUT => 5, //don't wait indefinitely for a connection to happen,
        CURLOPT_SSL_VERIFYPEER => false, //this allows unverified SSL sites to still get benchmarked,
        CURLOPT_TIMEOUT => 10 //for now, setting this to timeout at 10 seconds, don't want this just spinning forever
      );

      //assign the options to our handle
      curl_setopt_array($this->_ch, $curlOptions);
      
      $data = curl_exec($this->_ch);

      //get the data back about this url request
      $curlInfo = curl_getinfo($this->_ch);
      
      return new Benchmark(array(
        Benchmark::URL => $curlInfo['url'],
        Benchmark::CONTENT_TYPE => $curlInfo['content_type'],
        Benchmark::NUMBER_OF_BYTES => $curlInfo['size_download'],
        Benchmark::TOTAL_DOWNLOAD_TIME => $curlInfo['total_time']
      ));
    }

    /**
     * Internal function that will generate a Benchmark based on results using file_get_contents.
     * I believe the results from here are going to be less accurate and is only implemented
     * as a last resort sort of solution.
     * 
     * @param string $url
     * @return Benchmark
     * @throws InvalidUrlException
     * @throws InvalidBenchmarkException
     */
    protected function _getBenchmarkForUrlWithoutCurl($url) {
      //since cURL is not available, gotta figure out a way to get the information

      //get the current time before making the request
      $startTime = microtime(true);
      //request to get the URL
      $rawResponse = file_get_contents($url);
      //get the current time right NOW!
      $endTime = microtime(true);

      //get an associative array of the response headers
      $headers = $this->_parseHeaders(implode("\r\n", $http_response_header));

      return new Benchmark(array(
        Benchmark::URL => $url,
        Benchmark::CONTENT_TYPE => isset($headers['Content-Type']) ? $headers['Content-Type'][count($headers['Content-Type']) - 1] : null, //if redirects happened there will be multiple Content-Type entries, use the last one
        Benchmark::NUMBER_OF_BYTES => strlen($rawResponse), //use strlen to calculate bytes as opposed to the headers Content-Length, some sites don't return that info in the headers
        Benchmark::TOTAL_DOWNLOAD_TIME => ($endTime - $startTime) //by substracting the times, we get a rough estimate of the total download time
      ));
    }

    /**
     * Helper method to turn a string representing HTTP headers into
     * an associative array.  Full disclosure here, I yanked this from
     * http://www.bhootnath.in/blog/2010/10/parse-http-headers-in-php/ as
     * I can't guarantee that the system this is going to be running on has
     * pecl_http available. This function definitely gets the job done though.
     *
     * @param string $header
     * @return array
     */
    protected function _parseHeaders($header) {
      $retVal = array();
      $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
      foreach ($fields as $field) {
        if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
          $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
          if (isset($retVal[$match[1]])) {
            $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
          } else {
            $retVal[$match[1]] = trim($match[2]);
          }
        }
      }
      return $retVal;
    }
  }
?>