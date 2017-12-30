<?php
/**
 * The Connection class is for calling SheetDB API with any request
 */

namespace SheetDB;

use Exception;

class Connection
{

    private $url;
    private $queryParams = [];
    private $sheet = null;

    public function __construct($url, $sheet = null) {
      $this->url = $url;
      $this->sheet = $sheet;
    }

    public function setUrl($url) {
      $this->url = $url;
    }

    public function getUrl() {
      return $this->url;
    }

    public function setQueryParams($queryParams) {
      $this->queryParams = $queryParams;
    }

    public function getQueryParams() {
      return $this->queryParams;
    }

    public function addToQueryParams($param) {
      $this->queryParams = array_merge($this->queryParams,$param);
    }

    public function resetQueryParams() {
      $this->queryParams = [];
    }

    /**
     * This method prepares the query parameters and sets the final url.
     * @return $result of request specified in sheetdb documentation
     *         OR false if something went wrong
     */
    public function makeRequest($method = 'get', $data = []) {

      $options = array(
        'http' => array(
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'method'  => strtoupper($method),
          'content' => http_build_query([
            'data' => $data
          ])
        )
      );

      $this->prepareQueryUrl();

      try {
        $raw = @file_get_contents($this->url, false, stream_context_create($options));
        $result = json_decode($raw);
      } catch (Exception $e) {
        return false;
      }

      return $result;
    }

    /**
     * If queryParams are set, updates url with http_build_query
     */
    private function prepareQueryUrl() {
        $url = $this->url;

        if ($this->sheet) {
          $this->queryParams['sheet'] = $this->sheet;
        }

        if ($this->queryParams) {
          $url .= '/?';
          $url .= http_build_query($this->queryParams);
          $this->url = $url;
        }
    }
}
