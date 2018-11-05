<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 25.10.18
 * Time: 13:08
 */

namespace App\Service;

use App\Helper\ReqUtils;
use DateTime;
use Exception;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Database\RetentionPolicy;

use Psr\Log\LoggerInterface;

class DatasetService{
  const MEASUREMENT = 'datasets';
  const MEASUREMENT_NAME = 'dataset';
  const RETENTION_POLICY = 'autogen';

  private $client;
  private $db_name;
  private $db_user;
  private $db_pass;
  private $db_policy;
  private $reqUtils;
  private $logger;

  /**
   * DatasetService constructor.
   * Automatically injects params
   * @param $influxdb_host - InfluxDB host name
   * @param $influxdb_port - InfluxDB port number
   * @param $influxdb_name2 - InfluxDB database name
   * @param $influxdb_user - InfluxDb user name
   * @param $influxdb_pass - InfluxDb user password
   * @param $influxdb_policy - InfluxDb retention policy
   * @param ReqUtils $reqUtils
   * @param LoggerInterface $logger
   */
  public function __construct($influxdb_host,
                              $influxdb_port,
                              $influxdb_name2,
                              $influxdb_user,
                              $influxdb_pass,
                              $influxdb_policy,
                              ReqUtils $reqUtils,
                              LoggerInterface $logger){

    $this->client = new Client($influxdb_host, $influxdb_port);
    $this->db_name = $influxdb_name2;
    $this->db_user = $influxdb_user;
    $this->db_pass = $influxdb_pass;
    $this->db_policy = $influxdb_policy;
    $this->reqUtils = $reqUtils;
    $this->logger = $logger;
  }

  public function get_all($params) {
    $data = $this->retrieve_ds($params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_by_id($id, $params) {
    $data = $this->retrieve_ds_by_id($id, $params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_by_name($params){
    $data = $this->retrieve_ds_by_name($params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  /**
   * Return all datasets.
   *
   * @param $params
   * @return array
   */
  public function retrieve_ds($params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'List of datasets.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data' => [],
      'status' => 'success'
    ];

    try {
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);
      $result['start_date'] = $start;
      $result['end_date'] = $end;

      $qcount = sprintf('SELECT COUNT(DISTINCT("ds_id_f")) FROM "%s"  LIMIT 1', self::MEASUREMENT);
      $qdata = sprintf('SELECT * FROM "%s" ORDER BY time DESC LIMIT %s OFFSET %s ',
        self::MEASUREMENT, $params['limit'], $params['offset']);

      $database = $this->getDatabase();
      $total_point = $database->query($qcount);
      $tp = $total_point->getPoints();
      if (count($tp) > 0 && array_key_exists("count", $tp[0])) {
        $total = $total_point->getPoints()[0]['count'];
        $result['total'] = $total;
        if ($total > 0) {
          $data_point = $database->query($qdata);
          $rows = $data_point->getPoints();
          if (count($rows) > 0) {
            $result['data'] = $this->pb_data($rows);
          }
        }
      }
    }catch (Exception $e) {
      $result['status'] = "failure";
      $result['error'] = [
        "type" => "UNKNOWN",
        "description" => $e->getMessage()
      ];
      $this->logger->error($e->getMessage());
    }
    return $result;
  }

  /**
   * Return one dataset by id.
   *
   * @param $id
   * @param $params
   * @return array
   */
  public function retrieve_ds_by_id($id, $params){
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Dataset by Id.',
      'total' => 0,
      'data' => [],
      'status' => 'success'
    ];

    try {
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);

      $qdata = sprintf('SELECT * FROM "%s" WHERE "dataset_id"=\'%s\'', self::MEASUREMENT, $id);
      $qdef = sprintf('SELECT * FROM "%s"  WHERE ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT 1',
        self::MEASUREMENT, $start, $end);

      $database = $this->getDatabase();
      if (!is_null($id)) {
        $dp = $database->query($qdata);
      }else{
        $dp = $database->query($qdef);
      }

      $rows = $dp->getPoints();
      if (count($rows) > 0) {
        $result['data'] = [$this->pb_data($rows)[0]];
        $result['total'] = 1;
      }

    } catch (Exception $e) {
      $result['status'] = "failure";
      $result['error'] = [
        "type" => "UNKNOWN",
        "description" => $e->getMessage()
      ];
      $this->logger->error($e->getMessage());
    }
    return $result;
  }


  /**
   * Return one dataset by name.
   *
   * @param $params
   * @return array
   */
  public function retrieve_ds_by_name($params){
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Dataset by name.',
      'total' => 0,
      'data' => [],
      'status' => 'success'
    ];

    try {
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);

      $database = $this->getDatabase();

      if (!is_null($params['name'])) {
        $qdata = sprintf('SELECT * FROM "%s" WHERE "dataset_id"=\'%s\' AND ("time" >= \'%s\' AND "time" <= \'%s\')',
          self::MEASUREMENT, $params['name'], $start, $end);
        $dp = $database->query($qdata);
      }else{
        $qdef = sprintf('SELECT * FROM "%s" WHERE ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT 1',
          self::MEASUREMENT, $start, $end);
        $dp = $database->query($qdef);
      }

      $rows = $dp->getPoints();
      if (count($rows) > 0) {
        $result['data'] = [$this->pb_data($rows)[0]];
        $result['total'] = 1;
      }

    } catch (Exception $e) {
      $result['status'] = "failure";
      $result['error'] = [
        "type" => "UNKNOWN",
        "description" => $e->getMessage()
      ];
      $this->logger->error($e->getMessage());
    }
    return $result;
  }

  // PRIVATE SECTIONS

  /**
   * Parse and restructure dataset fields (verbose mode).
   *
   * @param $rows   - Array of dataset records
   * @return array  - Array of restructured dataset rows
   */
  private function pb_data($rows): array{
    $result = array();

    if($rows != null && is_array($rows)) {
      foreach($rows as $row){
        $el = array();
        $el['id'] = array_key_exists('dataset_id', $row) ? $row['dataset_id'] : "";
        $el['address_family'] = array_key_exists('address_family', $row) ? $row['address_family'] : "";
        $el['asn_assigned_ranges'] = array_key_exists('asn_assigned_ranges', $row) ? json_decode($row['asn_assigned_ranges']) : [];
        $el['asn_reserved_ranges'] = array_key_exists('asn_reserved_ranges', $row) ? json_decode($row['asn_reserved_ranges']) : [];
        $el['prefixes'] = array_key_exists('number_prefixes', $row) ? json_decode($row['number_prefixes']) : "";
        $el['addresses'] = array_key_exists('number_addresses', $row) ? json_decode($row['number_addresses']) : "";
        $el['asns'] = array_key_exists('number_asnes', $row) ? json_decode($row['number_asnes']) : "";
        $el['orgs'] = array_key_exists('number_organizes', $row) ? json_decode($row['number_organizes']) : "";
        $el['date'] = array_key_exists('date', $row) ? json_decode($row['date']) : "";
        $el['clique'] = array_key_exists('clique', $row) ? json_decode($row['clique']) : [];
        $el['sources'] = array_key_exists('sources', $row) ? json_decode($row['sources'], true) : [];
        $el['date'] = array_key_exists('time', $row) ? $row['time'] : "";
        $result[] = $el;
      }
    }
    return $result;
  }

  /** Get date of last record in db
   * @return DateTime
   */
  private function get_last_date() {
    $result = new DateTime();
    $qdata = sprintf('SELECT "time", "dataset_id" FROM "%s" ORDER BY time DESC LIMIT 1',self::MEASUREMENT);
    try {
      $database = $this->getDatabase();
      $data_point = $database->query($qdata);
      $rows = $data_point->getPoints();
      if (count($rows) > 0) {
        $result = (array_key_exists('time', $rows[0]) && strtotime($rows[0]['time'])) ? new DateTime($rows[0]['time']) : new DateTime();
      }
    }catch (Exception $e){
      $this->logger->error($e->getMessage());
    }
    return $result;
  }

  private function get_date_range($params){
    $date_start = array_key_exists('start_date', $params) ? $params['start_date'] : null;
    $date_end   = array_key_exists('end_date', $params) ? $params['end_date'] : null;

    if(!is_null($date_start) && !is_null($date_end)){
      $result = $this->reqUtils->normalize_date_range($date_start, $date_end);
    }elseif (!is_null($date_start) && is_null($date_end)){
      $result = $this->reqUtils->one_month_add($date_start);
    }else{
      // get last data date
      $last = $this->get_last_date();
      $result = $this->reqUtils->one_month_sub($last);
    }
    return $result;
  }

  private function nonverbose($data) {
    $result = [];
    if (!is_null($data) && is_array($data) && array_key_exists("data", $data)) {
      foreach ($data['data'] as $el) {
        if (array_key_exists("id", $el)) {
          $result[] = $el['id'];
        }
      }
      sort($result);
    }
    return $result;
  }

  /**
   * Return InfluxDB Client Object
   *
   * @return Database - InfluxDb Clien Object
   * @throws Database\Exception
   */
  private function getDatabase(): Database {
    $database = $this->client->selectDB($this->db_name);
    if (!$database->exists()) {
      $database->create(new RetentionPolicy($this->db_name, $this->db_policy, 1, true));
    }
    return $database;
  }

}