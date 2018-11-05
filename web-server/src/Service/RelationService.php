<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 07.09.18
 * Time: 14:10
 */

namespace App\Service;

use App\Helper\ReqUtils;
use DateTime;
use Exception;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Database\RetentionPolicy;
use Psr\Log\LoggerInterface;

class RelationService {

  const MEASUREMENT = 'relations.rank.asc';
  const MEASUREMENT_NAME = 'links';
  const RETENTION_POLICY = 'autogen';

  private $client;
  private $db;
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
   * @param AsnService $asnService
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

    $this->db_name = $influxdb_name;
    $this->db_user = $influxdb_user;
    $this->db_pass = $influxdb_pass;
    $this->db_policy = $influxdb_policy;
    $this->reqUtils = $reqUtils;
    $this->asnService = $asnService;
    $this->logger = $logger;
    $this->client = new Client($influxdb_host, $influxdb_port);
    $this->db = $this->getDatabase();
  }

  public function get_all($params){
    return $this->retrieve_links($params);
  }

  public function get_by_asn($asn, $params){
    return $this->retrieve_links_by_asn($asn, $params);
  }

  public function get_ranged($asn1, $asn2, $params){
    return $this->retrieve_ranged_links($asn1, $asn2, $params);
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

      $sort = !is_null($params['sort']) ? $params['sort'] : 'rank';
      $measurement = $this->getMeasure('relations', $sort);

      try {
        $result['description'] = 'List of relations by asn.';

        $qcount= sprintf('SELECT COUNT("asn0_f") FROM "%s" WHERE asn0 = \'%s\' LIMIT 1', $measurement, $asn);
        $qdata = sprintf('SELECT * FROM "%s" WHERE asn0 = \'%s\' LIMIT %s OFFSET %s',
          $measurement,  $asn, $params['limit'], $params['offset']);

        $total_point = $this->db->query($qcount);
        $total = $total_point->getPoints()[0]['count'];
        $result['total'] = $total;
        $data_point = $this->db->query($qdata);
        $rows = $data_point->getPoints();
        if (count($rows) > 0) {
          $result['data'] = $this->pb_data($rows);
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
        $el['id'] = array_key_exists('asn1', $row) ? $row['asn1'] : "";
        $el['asn'] = array_key_exists('asn1', $row) ? $row['asn1'] : "";
        $el['name'] = array_key_exists('name', $row) ? $row['name'] : "";
        $el['clique'] = array_key_exists('clique', $row) ? $row['clique'] : "true";
        $el['rank'] = array_key_exists('rank', $row) ? $row['rank'] : "true";
        $el['source'] = array_key_exists('source', $row) ? $row['source'] : "true";
        $el['country'] = array_key_exists('country', $row) ? $row['country'] : "true";
        $el['country_name'] = array_key_exists('country_name', $row) ? $row['country_name'] : "true";
        $el['latitude'] = array_key_exists('latitude', $row) ? $row['latitude'] : "";
        $el['longitude'] = array_key_exists('longitude', $row) ? $row['longitude'] : "";
        $el['relationship'] = array_key_exists('relationship', $row) ? $row['relationship'] : "";
        $el['paths'] = array_key_exists('paths', $row) ? $row['paths'] : "";
        $el['org']['id'] = array_key_exists('org_id', $row) ? $row['org_id'] : "";
        $el['org']['name'] = array_key_exists('org_name', $row) ? $row['org_name'] : "";
        $el['cone']['asns'] = array_key_exists('cone_asns', $row) ? $row['cone_asns'] : "";
        $el['cone']['addresses'] = array_key_exists('cone_addresses', $row) ? $row['cone_addresses'] : "";
        $el['cone']['prefixes'] = array_key_exists('cone_prefixes', $row) ? $row['cone_prefixes'] : "";

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

  /**
   * Return InfluxDB Client Object
   *
   * @return Database - InfluxDb Client Object
   */
  private function getDatabase(): Database {
    $database = null;
    try {
      $database = $this->client->selectDB($this->db_name);
      if (!$database->exists()) {
        $database->create(new RetentionPolicy($this->db_name, $this->db_policy, 1, true));
      }
    }catch (Database\Exception $e) {
      $this->logger->error($e->getMessage());
    }
    return $database;
  }

  private function getMeasure($prefix=null, $sort=null){
    $result = self::MEASUREMENT;
    if(!is_null($prefix) && !is_null($sort)) {
      if (substr($sort, 0, 1) === '-') {
        $suffix = 'desc';
        $tag = substr($sort, 1, strlen($sort));
      }else{
        $suffix = 'asc';
        $tag = $sort;
      }
      $result = $prefix . '.' . $tag . '.' . $suffix;
    }
    return $result;
  }
}