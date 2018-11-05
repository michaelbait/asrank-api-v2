<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 07.09.18
 * Time: 14:10
 */

namespace App\Api2\Service;

use App\Api2\Helper\ReqUtils;
use DateTime;
use Exception;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Database\RetentionPolicy;
use Psr\Log\LoggerInterface;

class RelationService {

  const MEASUREMENT = 'asn_rel_v4';
  const MEASUREMENT_NAME = 'links';
  const RETENTION_POLICY = 'autogen';

  private $client;
  private $db_name;
  private $db_user;
  private $db_pass;
  private $db_policy;
  private $reqUtils;
  private $asnService;
  private $logger;

  /**
   * RelationService constructor.
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
                              AsnService $asnService,
                              LoggerInterface $logger){

    $this->client = new Client($influxdb_host, $influxdb_port);
    $this->db_name = $influxdb_name;
    $this->db_user = $influxdb_user;
    $this->db_pass = $influxdb_pass;
    $this->db_policy = $influxdb_policy;
    $this->reqUtils = $reqUtils;
    $this->asnService = $asnService;
    $this->logger = $logger;
  }

  public function get_all($params){
    $data = $this->retrieve_links($params);
    if(is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_by_asn($asn, $params){
    $data = $this->retrieve_links_by_asn($asn, $params);
    if(is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_ranged($asn1, $asn2, $params){
    $data = $this->retrieve_ranged_links($asn1, $asn2, $params);
    if(is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  /**
   *  Return all relations rows.
   *
   * @param $params
   * @return array
   */
  public function retrieve_links($params){
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'All links (relations).',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data'  => [],
      'status' => 'success'
    ];

    list($start, $end) = $this->get_date_range($params);
    $start = $this->reqUtils->datefstr($start);
    $end = $this->reqUtils->datefstr($end);
    $result['start_date'] = $start;
    $result['end_date'] = $end;

    try {
      $qcount= sprintf('SELECT COUNT(DISTINCT("asnf")) FROM "%s" LIMIT 1', self::MEASUREMENT);
      $qdata = sprintf('SELECT * FROM "%s" WHERE ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s ',
        self::MEASUREMENT,  $start, $end, $params['limit'], $params['offset'] );

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
            $r = $this->pb_data($rows);
            $result['data'] = $this->sortLinks($r, $params['sort']);
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

  public function retrieve_links_by_asn($asn, $params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'All links for specified Asn.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data'  => [],
      'status' => 'success'
    ];

    if(!is_null($asn)){
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);
      $result['start_date'] = $start;
      $result['end_date'] = $end;

      try {
        $qcount= sprintf('SELECT COUNT("asnf") FROM "%s" WHERE asnf = %s LIMIT 1', self::MEASUREMENT, $asn);
        $qdata = sprintf('SELECT * FROM "%s" WHERE asn = \'%s\' AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
          self::MEASUREMENT,  $asn, $start, $end, $params['limit'], $params['offset']);

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
              $result['data'] = $this->pb_data_asn1_info($rows, $params);
            }
          }
        }
      } catch (Exception $e) {
        $result['status'] = "failure";
        $result['error'] = [
          "type" => $e->getCode(),
          "description" => $e->getMessage()
        ];
        $this->logger->error($e->getMessage());
      }
    }
    return $result;
  }

  public function retrieve_neighbors_by_asn($asn, $params, $flat=false) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'All links for specified Asn.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data'  => [],
      'status' => 'success'
    ];

    if(!is_null($asn)){
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);
      $result['start_date'] = $start;
      $result['end_date'] = $end;

      try {
        $qdata = sprintf('SELECT * FROM "%s" WHERE asn = \'%s\' AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
          self::MEASUREMENT,  $asn, $start, $end, $params['limit'], $params['offset']);

        $database = $this->getDatabase();
        $dp = $database->query($qdata);
        $rows = $dp->getPoints();
        if(count($rows) > 0) {
          $r = $this->deduplicate($rows, 'neighbor');
          if($flat){
            $result['data'] = $r;
          }else{
            $result['data'] = $this->pb_data_n($r);
          }
        }
      } catch (Exception $e) {
        $result['status'] = "failure";
        $result['error'] = [
          "type" => $e->getCode(),
          "description" => $e->getMessage()
        ];
        $this->logger->error($e->getMessage());
      }
    }
    return $result;
  }

  public function retrive_links_by_asn_and_neighbor($asn, $neighbor, $params){
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'All links for specified Asn.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data'  => [],
      'status' => 'success'
    ];

    if(!is_null($asn)){
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);
      $result['start_date'] = $start;
      $result['end_date'] = $end;

      try {
        $qdata = sprintf('SELECT * FROM "%s" WHERE ("asn" = \'%s\' AND "neighbor" = \'%s\') AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
          self::MEASUREMENT, trim($asn), trim($neighbor), $start, $end, $params['limit'], $params['offset']);

        $database = $this->getDatabase();
        $dp = $database->query($qdata);
        $rows = $dp->getPoints();
        if(count($rows) > 0) {
          $result['data'] = $this->pb_data_n($rows);
        }
      } catch (Exception $e) {
        $result['status'] = "failure";
        $result['error'] = [
          "type" => $e->getCode(),
          "description" => $e->getMessage()
        ];
        $this->logger->error($e->getMessage());
      }
    }
    return $result;
  }

  public function get_links_by_org_name($name, $params){
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'All links for specified Asn.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data'  => [],
      'status' => 'success'
    ];

    try{
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);
      $result['start_date'] = $start;
      $result['end_date'] = $end;

      $qdata= sprintf('SELECT "asnf", "neighbor" FROM "%s" WHERE "org_id" = \'%s\' AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC',
        self::MEASUREMENT, $name, $start, $end);
      $database = $this->getDatabase();
      $dp = $database->query($qdata);
      $rows = $dp->getPoints();
      if(count($rows) > 0){
        $result['data'] = $rows;
      }
    }catch(Exception $e){
      $this->logger->error($e->getMessage());
    }
    return $result;
  }

  public function get_links_by_org_id($name, $params){
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'All links for specified Asn.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data'  => [],
      'status' => 'success'
    ];

    if(!is_null($name)){
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);
      $result['start_date'] = $start;
      $result['end_date'] = $end;

      try {
        $qcount= sprintf('SELECT COUNT("asnf") FROM "%s" WHERE asnf = %s LIMIT 1', self::MEASUREMENT, $name);
        $qdata = sprintf('SELECT * FROM "%s" WHERE org_id = \'%s\' AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
          self::MEASUREMENT,  $name, $start, $end, $params['limit'], $params['offset']);

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
              $result['data'] = $this->pb_data($rows);
            }
          }
        }
      } catch (Exception $e) {
        $result['status'] = "failure";
        $result['error'] = [
          "type" => $e->getCode(),
          "description" => $e->getMessage()
        ];
        $this->logger->error($e->getMessage());
      }
    }
    return $result;
  }

  public function get_neighbors_by_org_id($name, $params){
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'All links for specified Asn.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data'  => [],
      'status' => 'success'
    ];

    try{
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);
      $result['start_date'] = $start;
      $result['end_date'] = $end;

      $qdata= sprintf('SELECT * FROM "%s" WHERE "org_id" = \'%s\' AND ("time" >= \'%s\' AND "time" <= \'%s\')',
        self::MEASUREMENT, $name, $start, $end);
      $database = $this->getDatabase();
      $dp = $database->query($qdata);
      $rows = $dp->getPoints();
      if(count($rows) > 0){
        $result = $this->pb_data_n($rows);
      }
    }catch(Exception $e){
      $this->logger->error($e->getMessage());
    }
    return $result;
  }

  /**
   * Get links between two asns.
   *
   * @param $asn1
   * @param $asn2
   * @param $params
   * @return array
   */
  public function retrieve_ranged_links($asn1, $asn2, $params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Links between two specified Asns.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data'  => [],
      'status' => 'success'
    ];

    if(!is_null($asn1 && !is_null($asn2))){
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);
      $result['start_date'] = $start;
      $result['end_date'] = $end;

      try {
        $qdata = sprintf('SELECT * FROM "%s" WHERE "asn" = \'%s\' AND "neighbor" = \'%s\' AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
          self::MEASUREMENT, $asn1, $asn2, $start, $end, $params['limit'], $params['offset']);

        $database = $this->getDatabase();
        $data_point = $database->query($qdata);
        $rows = $data_point->getPoints();
        if(count($rows) > 0) {
          $r = $this->deduplicate($rows, "neighbor");
          $result['total'] = count($r);
          $result['data'] = $this->pb_data($r);
        }
      } catch (Exception $e) {
        $result['status'] = "failure";
        $result['error'] = [
          "type" => "UNKNOWN",
          "description" => $e->getMessage()
        ];
        $this->logger->error($e->getMessage());
      }
    }
    return $result;
  }

  public function pb_data_asn1_info($rows, $params){
    $result = [];
    if(!is_null($rows) && is_array($rows)) {
      $cnt = 0;
      foreach ($rows as $row) {
        //if($cnt > 5){break;}
        $asnid = array_key_exists('neighbor', $row) ? $row['neighbor'] : "";
        $asn = $this->asnService->retrieve_asn_by_id_raw($asnid, $params)['data'];
        $el = [];
        $el['relationship'] = array_key_exists('relationship', $row) ? $row['relationship'] : "";
        $el['locations'] = (array_key_exists('locations', $row) && !is_null($row['locations'])) ? json_decode($row['locations']) : [];
        $el['paths'] = array_key_exists('number_paths', $row) ? $row['number_paths'] : "";
        $el['asn0'] = array_key_exists('asn', $row) ? $row['asn'] : "";
        $el['asn1'] = ["asn" =>$asn['asn'], "name" => $asn['asn_name'], "org" => $asn['org_id'], "rank"  => $asn['rank']];
        $el['date'] = array_key_exists('time', $row) ? $row['time'] : "";
        $result[] = $el;
        $cnt++;
      }
    }
    return $result;
  }

  /**
   * Parse and restructure realtions fields.
   *
   * @param $rows   - Array of dataset records
   * @return array  - Array of restructured dataset rows
   */
  private function pb_data($rows): array{
    $result = [];
    if(!is_null($rows) && is_array($rows)) {
      foreach($rows as $row){
        $el = [];
        $el['relationship'] = array_key_exists('relationship', $row) ? $row['relationship'] : "";
        $el['asn0'] = array_key_exists('asn', $row) ? $row['asn'] : "";
        $el['asn1'] = array_key_exists('neighbor', $row) ? $row['neighbor'] : "";
        $el['rank'] = array_key_exists('neighbor_rank', $row) ? $row['neighbor_rank'] : "";
        $el['paths'] = array_key_exists('number_paths', $row) ? $row['number_paths'] : "";
        $el['locations'] = (array_key_exists('locations', $row) && !is_null($row['locations'])) ? json_decode($row['locations']) : [];
        $el['date'] = array_key_exists('time', $row) ? $row['time'] : "";
        $result[] = $el;
      }
    }
    return $result;
  }

  /**
   * Parse and restructure realtions fields.
   *
   * @param $rows   - Array of dataset records
   * @return array  - Array of restructured dataset rows
   */
  private function pb_data_n($rows): array{
    $result = [];
    if(!is_null($rows) && is_array($rows)) {
      foreach($rows as $row){
        $el = [];
        $el['relationship'] = array_key_exists('relationship', $row) ? $row['relationship'] : "";
        $el['locations'] = (array_key_exists('locations', $row) && !is_null($row['locations'])) ? json_decode($row['locations']) : [];
        $el['paths'] = array_key_exists('number_paths', $row) ? $row['number_paths'] : "";
        $el['asn1'] = array_key_exists('asn', $row) ? $row['asn'] : "";
        $el['asn0'] = array_key_exists('neighbor', $row) ? $row['neighbor'] : "";
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
    $qdata = sprintf('SELECT "time", "asnf" FROM "%s" ORDER BY time DESC LIMIT 1',self::MEASUREMENT);
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

  private function sortLinks($data, $column){
    $result = $data;
    if(!is_null($data) && is_array($data) && count($data) > 0){
      $k = trim($column);
      switch ($k){
        case "rank": {
          usort($result, function ($a, $b){
            if($a['rank'] == $b['rank']) {return 0;}
            return ($a['rank'] > $b['rank']) ? 1 : -1;
          });
          break;
        }
        case "-rank": {
          usort($result, function ($a, $b){
            if($a['rank'] == $b['rank']) {return 0;}
            return ($a['rank'] < $b['rank']) ? 1 : -1;
          });
          break;
        }
        default:{
          break;
        }
      }
    }
    return $result;
  }

  // Nonverbose data presentation
  private function nonverbose($data){
    $result = [];
    if(!is_null($data) && is_array($data) && array_key_exists("data", $data)){
      foreach ($data['data'] as $el){
        $r = [];
        $r['relationship'] = array_key_exists('relationship', $el) ? $el['relationship'] : "";
        $r['asn'] = array_key_exists('asn1', $el) ? $el['asn1'] : "";
        $r['locations'] = array_key_exists('locations', $el) ? json_decode($el['locations'], true) : [];
        $r['paths'] = array_key_exists('number_paths', $el) ? $el['number_paths'] : "";
        $r['date'] = array_key_exists('time',$el) ? $el['time'] : "";
        $result[] = $r;
      }
      sort($result);
    }
    return $result;
  }

  // Remove duplicates for asns
  private function deduplicate($array, $field){
    $result = [];
    $temp = [];
    if(!is_null($array) && !empty($field)) {
      foreach ($array as $val) {
        if(array_key_exists($val[$field], $temp)){
          $time1 = strtotime($temp[$val[$field]]['time']);
          $time2 = strtotime($val['time']);
          if($time1 < $time2){
            $temp[$val[$field]] = $val;
          }
        }else{
          $temp[$val[$field]] = $val;
        }
      }
      foreach ($temp as $tmp){
        $result[] = $tmp;
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