<?php
  require_once dirname(dirname(__FILE__)) . '/exceptions/InvalidBenchmarkException.class.php';

  class Benchmark {
    //define some constants for consistency
    const CONTENT_TYPE = 'CONTENT_TYPE';
    const NUMBER_OF_BYTES = 'NUMBER_OF_BYTES';
    const TOTAL_DOWNLOAD_TIME = 'TOTAL_DOWNLOAD_TIME';
    const URL = 'URL';

    //member variables
    protected $_contentType;
    protected $_numberOfBytes;
    protected $_totalDownloadTime;
    protected $_url;

    /**
     * The constructor takes an associative array of options. They are keyed off of the constants
     * that are defined in this class.  If valid keys are passed in with invalid data then an
     * InvalidBenchmarkException will be thrown.
     * 
     * @param array $options
     * @throws InvalidBenchmarkException
     */
    public function __construct (array $options = array()) {
      //sanity check, make sure we are working with an array
      if (!is_array($options)) {
        $options = array();
      }

      if ($this->_isPresent(self::CONTENT_TYPE, $options)) {
        $this->setContentType($options[self::CONTENT_TYPE]);
      }

      if ($this->_isPresent(self::NUMBER_OF_BYTES, $options)) {
        $this->setNumberOfBytes($options[self::NUMBER_OF_BYTES]);
      }

      if ($this->_isPresent(self::TOTAL_DOWNLOAD_TIME, $options)) {
        $this->setTotalDownloadTime($options[self::TOTAL_DOWNLOAD_TIME]);
      }

      if ($this->_isPresent(self::URL, $options)) {
        $this->setUrl($options[self::URL]);
      }
    }

    /**
     * Helper method to determine whether a key is set in an array and is not empty.
     * 
     * @param string $key
     * @param array $array
     * @return bool
     */
    protected function _isPresent($key, $array) {
      return is_array($array) && isset($array[$key]) && !empty($array[$key]);
    }

    /**
     * Checks to see that the variable passed in is a float or not. Returns true if it is
     * and throws an InvalidBenchmarkException otherwise.
     * 
     * @param float $float
     * @param string $errorMessagePrefix
     * @return boolean
     * @throws InvalidBenchmarkException
     * 
     */
    protected function _validateFloat($float, $errorMessagePrefix = 'Variable') {
      if (!is_numeric($float)) {
        throw new InvalidBenchmarkException($errorMessagePrefix . ' must be a float');
      }

      return true;
    }

    /**
     * Checks to see that the variable passed in is an integer or not. Returns true if it is
     * and throws an InvalidBenchmarkException otherwise.
     * 
     * @param int $integer
     * @param string $errorMessagePrefix
     * @return boolean
     * @throws InvalidBenchmarkException
     * 
     */
    protected function _validateInteger($integer, $errorMessagePrefix = 'Variable') {
      /**
       * is_numeric only validates that it is something like 1.23 or "1.23" adding a 0 to the 
       * variable will make sure that it isn't a string, the resulting value with be either an int or a float
       * still have to check for numericality though because 'hello world' + 0 ends up being zero, which is no good
       */
      if (!is_numeric($integer) || !is_int($integer + 0)) {
        throw new InvalidBenchmarkException($errorMessagePrefix . ' must be an integer');
      }

      return true;
    }

    /**
     * Checks to see that the variable passed in is a string or not. Returns true if it is
     * and throws an InvalidBenchmarkException otherwise.
     * 
     * @param string $string
     * @param string $errorMessagePrefix
     * @return boolean
     * @throws InvalidBenchmarkException
     * 
     */
    protected function _validateString($string, $errorMessagePrefix = 'Variable') {
      if (!is_string($string)) {
        throw new InvalidBenchmarkException($errorMessagePrefix . ' must be a string');
      }

      return true;
    }

    /**
     * This will set the content type.  Returns $this to allow for chaining.
     * 
     * @param string $contentType
     * @return Benchmark
     */
    public function setContentType($contentType) {
      $this->_validateString($contentType, 'Content type');
      $this->_contentType = $contentType;
      return $this;
    }

    /**
     * This will set the number of bytes this Benchmark downloaded.  Returns $this to allow for chaining.
     * 
     * @param int $numberOfBytes
     * @return Benchmark
     */
    public function setNumberOfBytes($numberOfBytes) {
      $this->_validateFloat($numberOfBytes, 'Number of bytes');
      $this->_numberOfBytes = (float)$numberOfBytes;
      return $this;
    }

    /**
     * This will set the number of seconds this Benchmark took to complete. Returns $this to allow for chaining.
     * 
     * @param int $totalDownloadTime
     * @return Benchmark
     */
    public function setTotalDownloadTime($totalDownloadTime) {
      $this->_validateFloat($totalDownloadTime, 'Total download time');
      $this->_totalDownloadTime = (float)$totalDownloadTime;
      return $this;
    }

    /**
     * This will set the URL that this Benchmark downloaded.  Returns $this to allow for chaining.
     * 
     * @param string $url
     * @return Benchmark
     */
    public function setUrl($url) {
      $this->_validateString($url, 'URL');
      $this->_url = $url;
      return $this;
    }

    /**
     * Return a JSON representation of this object
     * 
     * @return string
     */
    public function toJson() {
      $objectVariables = get_object_vars($this);
      
      /**
       * I am running into an issue where when converting to json I get keys that I don't like
       * since I chose to make proteced/private variables use the _ prefixed notation. Instead
       * I am going to iterate through the keys and if it starts with _ I will remove the _ from
       * the json representation.  I couldn't use json_encode from outside of this class because
       * the variables aren't accessible outside of this object.  Also, looked into implementing
       * the JsonSerializable Interface, however, my PHP version doesn't support it.
       *
       */
      $jsonArray = array();
      foreach ($objectVariables as $key => $value) {
        if (substr($key, 0, 1) === '_') {
          $key = substr($key, 1);
        }

        $jsonArray[$key] = $value;
      }

      return json_encode($jsonArray);
    }
  }
?>