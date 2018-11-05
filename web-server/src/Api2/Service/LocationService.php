<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 28.08.18
 * Time: 11:19
 */

namespace App\Api2\Service;

use App\Api2\Helper\ReqUtils;
use DateTime;
use Exception;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Database\RetentionPolicy;

use Psr\Log\LoggerInterface;


class LocationService{
  const MEASUREMENT = 'locations';
  const RETENTION_POLICY = 'autogen';

  private $client;
  private $db_name;
  private $db_user;
  private $db_pass;
  private $db_policy;
  private $reqUtils;
  private $logger;

  /**
   * LocationService constructor.
   * Automatically injects params
   * @param $influxdb_host - InfluxDB host name
   * @param $influxdb_port - InfluxDB port number
   * @param $influxdb_name - InfluxDB database name
   * @param $influxdb_user - InfluxDb user name
   * @param $influxdb_pass - InfluxDb user password
   * @param $influxdb_policy - InfluxDb retention policy
   * @param ReqUtils $reqUtils
   * @param LoggerInterface $logger
   */
  public function __construct($influxdb_host,
                              $influxdb_port,
                              $influxdb_name,
                              $influxdb_user,
                              $influxdb_pass,
                              $influxdb_policy,
                              ReqUtils $reqUtils,
                              LoggerInterface $logger){

    $this->client = new Client($influxdb_host, $influxdb_port);
    $this->db_name = $influxdb_name;
    $this->db_user = $influxdb_user;
    $this->db_pass = $influxdb_pass;
    $this->db_policy = $influxdb_policy;
    $this->reqUtils = $reqUtils;
    $this->logger = $logger;
  }

  /**
   * Return all locations.
   *
   *
   * @param int $offset - Offset for ISQL SELECT query.
   * @param int $limit - Limit for ISQL SELECT query.
   * @param null $verbose
   * @return array
   */
  public function get_all($params){
    $data = $this->retrieve_locations($params);
    if(is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_location($loc, $verbose=null){
    $data = $this->retrieve_location($loc);
    if(is_null($verbose)) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  /**
   * Retrieve all locations.
   *
   * @param $offset - Offset for ISQL SELECT query.
   * @param $limit - Limit for ISQL SELECT query.
   * @return array
   */
  private function retrieve_locations($params) {
    $result = [
      'type' => self::MEASUREMENT,
      'description' => 'All locations.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data'  => [],
      'status' => 'success'
    ];

    try {
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);

      $result['start_date'] = $start;
      $result['end_date'] = $end;

      $qcount = sprintf('SELECT COUNT(DISTINCT("city")) FROM "%s"  LIMIT 1', self::MEASUREMENT);
      $qdata = sprintf('SELECT * FROM "%s" WHERE ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
        self::MEASUREMENT,  $start, $end, $params['limit'], $params['offset']);

      $database = $this->getDatabase();
      $total_point = $database->query($qcount);
      $tp = $total_point->getPoints();
      if(count($tp) > 0 && array_key_exists("count", $tp[0])){
        $total = $total_point->getPoints()[0]['count'];
        $result['total'] = $total;
        if($total > 0){
          $data_point = $database->query($qdata);
          $rows = $data_point->getPoints();
          if(count($rows) > 0) {
            //$r = array_slice($this->deduplicate($rows, "asn_name"), $offset, $limit);
            $result['data'] = $this->pb_data($rows);
          }
        }
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
   * Retrieve one location.
   *
   * @param $lid
   * @return array
   * @throws Database\Exception
   */
  public function retrieve_location($loc){

    $result = [
      'type' => self::MEASUREMENT,
      'description' => 'One specified location.',
      'total' => 0,
      'data'  => [],
      'status' => 'success'
    ];

    try {
      $database = $this->getDatabase();
      if(!is_null($loc)) {
        $data_point = $database->query(sprintf(
          'SELECT * FROM "%s" WHERE "lid"=\'%s\'', self::MEASUREMENT, $loc
        ));
      }else{
        $data_point = $database->query(sprintf(
          'SELECT * FROM "%s" ORDER BY time DESC LIMIT 1', self::MEASUREMENT
        ));
      }
      if($data_point){
        $rows = $data_point->getPoints();
        if(count($rows) > 0){
          $result['data'] = [$this->pb_data($rows)[0]];
          $result['total'] = 1;
        }
      }

    }catch (Exception $e){
      $result['status'] = "failure";
      $result['error'] = [
        "type" => "UNKNOWN",
        "description" => $e->getMessage()
      ];
      $this->logger->error($e->getMessage());
    }
    return $result;
  }


  /** Get date of last record in db
   * @return DateTime
   */
  private function get_last_date() {
    $result = new DateTime();
    $qdata = sprintf('SELECT "time", "lid" FROM "%s" ORDER BY time DESC LIMIT 1',self::MEASUREMENT);
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

  private function nonverbose($data){
    $result = [];
    if(!is_null($data) && is_array($data) && array_key_exists("data", $data)){
      foreach ($data['data'] as $el){
        if(array_key_exists("id", $el)){
          $result[] = $el['id'];
        }
      }
      sort($result);
    }
    return $result;
  }

  /**
   * Parse and restructure locations fields.
   *
   * @param $rows   - Array of dataset records
   * @return array  - Array of restructured dataset rows
   */
  private function pb_data($rows): array{
    $result = [];
    if($rows != null && is_array($rows)){
      foreach ($rows as $row){
        $elem = [];
        $elem['id'] = $row['lid'];
        $elem['city'] = $row['city'];
        $elem['country'] = $row['country'];
        $elem['continent'] = $row['continent'];
        $elem['region'] = $row['region'];
        $elem['population'] = $row['population'];
        $elem['latitude'] = $row['latitude'];
        $elem['longitude'] = $row['longitude'];
        $elem['date'] = $row['time'];
        $result[] = $elem;
      }
    }
    return $result;
  }

  /**
   * Return InfluxDB Database Client
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