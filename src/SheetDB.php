<?php
/**
 * This is library to simplify SheetDB API requests without any dependencies.
 * If you want to contribute feel free to reach me at support@sheetdb.io
 */

namespace SheetDB;

use SheetDB\Connection;

class SheetDB
{
    const BASE_URL = 'https://sheetdb.io/api/v1/';

    private $connection;
    protected $api_id;

    /**
     * Instantiates Connection with default url
     * Sets SheetDB api_id
     * @param $api_id ID of API within SheetDB
     */
    public function __construct($api_id, $sheet = null) {
      $this->api_id = $api_id;
      $this->connection = new Connection($this->handlerUrl(), $sheet);
    }

    /**
     * Get all of the spreadsheet data
     * @return array|bool Results or false
     */
    public function get() {
      $this->connection->resetQueryParams();
      $this->connection->setUrl($this->handlerUrl());
      return $this->connection->makeRequest();
    }

    /**
     * Get all keys of the spreadsheet
     * @return array|bool Array of keys or false
     */
    public function keys() {
      $this->connection->resetQueryParams();
      $this->connection->setUrl($this->handlerUrl('/keys'));
      return $this->connection->makeRequest('get');
    }

    /**
     * Get document name of the spreadsheet
     * @return string|bool Name of document or false
     */
    public function name() {
      $this->connection->resetQueryParams();
      $this->connection->setUrl($this->handlerUrl('/name'));
      $response = $this->connection->makeRequest('get');
      if ($response && isset($response->name)){
        return $response->name;
      }
      return false;
    }

    /**
     * Get count of rows in spreadsheet (without first row)
     * @return int Number of rows
     */
    public function count() {
      $this->connection->resetQueryParams();
      $this->connection->setUrl($this->handlerUrl('/count'));
      $response = $this->connection->makeRequest('get');
      if ($response && isset($response->rows)){
        return $response->rows;
      }
      return false;
    }

    /**
     * Search within spreadsheet
     * @param array $query
     * @param bool $caseSensitive
     * @return array|bool Results of search or false
     */
    public function search(array $query, $caseSensitive = false) {
      $this->connection->resetQueryParams();
      $this->connection->addToQueryParams($query);
      $this->connection->addToQueryParams(['casesensitive' => $caseSensitive]);
      $this->connection->setUrl($this->handlerUrl('/search'));
      return $this->connection->makeRequest('get');
    }

    /**
     * Search within spreadsheet, if any parameter is true, matches will
     * be in a response
     * @param array $query
     * @param bool $caseSensitive
     * @return array|bool Results of search or false
     */
    public function searchOr(array $query, $caseSensitive = false) {
      $this->connection->resetQueryParams();
      $this->connection->addToQueryParams($query);
      $this->connection->addToQueryParams(['casesensitive' => $caseSensitive]);
      $this->connection->setUrl($this->handlerUrl('/search_or'));
      return $this->connection->makeRequest('get');
    }

    /**
     * Create a row(s) in spreadsheet
     * @param array $data
     * @return int|bool Count of rows created or false
     */
    public function create(array $data) {
      $this->connection->resetQueryParams();
      $this->connection->setUrl($this->handlerUrl());
      $response = $this->connection->makeRequest('post', $data);
      if ($response && isset($response->created)){
        return $response->created;
      }
      return false;
    }

    /**
     * Update a row(s) in spreadsheet
     * @param string $columnName
     * @param string $value
     * @param array $data
     * @param bool $putMethod If it's true, make PUT request, otherwise PATCH
     * @return int|bool Count of rows updated or false
     */
    public function update($columnName, $value, array $data, $putMethod = false) {
      $this->connection->resetQueryParams();
      $this->connection->setUrl($this->handlerUrl('/' . $columnName . '/' . $value));
      $response = $this->connection->makeRequest($putMethod === true ? 'put' : 'patch', [$data]);
      if ($response && isset($response->updated)){
        return $response->updated;
      }
      return false;
    }

    /**
     * Update the batch of rows in the spreadsheet
     * @param array $data
     * @param bool $putMethod If it's true, make PUT request, otherwise PATCH
     * @return int|bool Count of rows updated or false
     */
    public function batchUpdate(array $data, $putMethod = false) {
      $this->connection->resetQueryParams();
      $this->connection->setUrl($this->handlerUrl('/batch_update'));
      $response = $this->connection->makeRequest($putMethod === true ? 'put' : 'patch', $data);
      if ($response && isset($response->updated)){
        return $response->updated;
      }
      return false;
    }

    /**
     * Delete a row(s) in spreadsheet
     * @param string $columnName
     * @param string $value
     * @return int|bool Count of rows deleted or false
     */
    public function delete($columnName, $value) {
      $this->connection->resetQueryParams();
      $this->connection->setUrl($this->handlerUrl('/' . $columnName . '/' . $value));
      $response = $this->connection->makeRequest('delete');
      if ($response && isset($response->deleted)){
        return $response->deleted;
      }
      return false;
    }

    /**
     * Helper method that returns url with appended content
     * @return string
     */
    private function handlerUrl($append = '') {
      return self::BASE_URL . $this->api_id . $append;
    }

}
