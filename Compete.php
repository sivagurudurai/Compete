<?php
// Check for dependencies
if (!function_exists('curl_init'))
  throw new Exception('Compete needs the CURL PHP extension.');

if (!function_exists('json_decode'))
  throw new Exception('Compete needs the JSON PHP extension.');

class Compete
{
  const API_BASE_URL = 'http://apps.compete.com/sites/:domain/trended/:metric/?apikey=:key';
  const USER_AGENT = 'Compete API wrapper for PHP';

  private $_urlKeys = array(':domain', ':metric', ':key');
  private $_apiKey;

  // For url cleaning
  private $_toSearch   = array('http://', 'www.');
  private $_toReplace  = array('', '');

  public function __construct($apiKey) {
    $this->_apiKey = $apiKey;
  }

  public function get($site, $metric) {
    $values = array(
      $this->_prepareUrl($site),
      $metric,
      $this->_apiKey
    );

    $url = str_replace($this->_urlKeys, $values, self::API_BASE_URL);
    return json_decode($this->_get($url));
  }

  /**
   * Cut unnecessary parts of url.
   *
   * @param string $url some url.
   * @return string trimmed url.
   */
  private function _prepareUrl($url) {
    return str_replace($this->_toSearch, $this->_toReplace, $url);
  }

  /**
   * Execute http get method.
   *
   * @param string $url request url.
   * @return string response.
   */
  private function _get($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,            $url);
    curl_setopt($ch, CURLOPT_USERAGENT,      self::USER_AGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    return curl_exec($ch);
  }
}
