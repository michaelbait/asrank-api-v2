<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 05.09.18
 * Time: 13:11
 */

namespace App\Api2\Service;

use App\Api2\Helper\ReqUtils;
use Exception;
use DateTime;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Database\RetentionPolicy;

use Psr\Log\LoggerInterface;

class AsnService {

  const MEASUREMENT = 'asn_info_v4';
  const MEASUREMENT_NAME = 'asn';
  const RETENTION_POLICY = 'autogen';

  private $client;
  private $db_name;
  private $db_user;
  private $db_pass;
  private $db_policy;
  private $reqUtils;
  private $logger;

  /**
   * AsnsService constructor.
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
                              LoggerInterface $logger) {

    $this->client = new Client($influxdb_host, $influxdb_port);
    $this->db_name = $influxdb_name;
    $this->db_user = $influxdb_user;
    $this->db_pass = $influxdb_pass;
    $this->db_policy = $influxdb_policy;
    $this->reqUtils = $reqUtils;
    $this->logger = $logger;
  }

  /** Retrieve Asn by its id.
   *
   * @param null $id - Asn ID.
   * @param $params - Request query params
   * @return array  - Asn.
   */
  public function get_by_id($id, $params) {
    $data = $this->retrieve_asn_by_id($id, $params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_by_ids($ids, $params){
    $data = $this->retrieve_asns_by_ids($ids, $params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  /** Retrieve Asn(s) by its name.
   *
   * @param $params  - Request query params
   * @return array   - Asns by name.
   */
  public function get_by_name($params) {
    if (!is_null($params['ranked'])) {
      $data['description'] = 'List od Asns. Ranked.';
    }
    $data = $this->retrieve_asn_by_name($params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }

    return $data;
  }

  public function get_by_org_id($id, $params) {
    $data =  $this->retrieve_asn_by_org_id($id, $params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_all($params) {

    if (!is_null($params['ranked'])) {
      $data = $this->retrieve_asns($params);
      $data['description'] = 'List od Asns. Ranked.';
    } else {
      $data = $this->retrieve_asns($params);
    }

    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }

    return $data;
  }

  /**
   * Return all asns rows (expanded data) from InfluxDB.
   *
   * @param $params
   * @return array
   */
  private function retrieve_asns($params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'List of asns.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data' => [],
      'status' => 'success'
    ];

    list($start, $end) = $this->get_date_range($params);
    $start = $this->reqUtils->datefstr($start);
    $end = $this->reqUtils->datefstr($end);

    $result['start_date'] = $start;
    $result['end_date'] = $end;

    try {
      $qcount = sprintf('SELECT COUNT(DISTINCT("asn_name")) FROM "%s"  LIMIT 1', self::MEASUREMENT);
      $qdata = sprintf('SELECT * FROM "%s" WHERE ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
        self::MEASUREMENT, $start, $end, $params['limit'], $params['offset']);
      if (!is_null($params['ranked'])) {
        $result['description'] = 'List of asns. Ranked.';
        $qcount = sprintf('SELECT COUNT(DISTINCT("asn_name")) FROM "%s" WHERE rank >= 0 AND customer_cone_asnes >= 0 LIMIT 1', self::MEASUREMENT);
        $qdata = sprintf('SELECT * FROM "%s" WHERE ("rank" >= 0 AND "customer_cone_asnes" >= 0) AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
          self::MEASUREMENT, $start, $end, $params['limit'], $params['offset']);
      }

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
            $r = $this->pb_data($rows);
            $result['data'] = $this->sortAsns($r, $params['sort']);
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
    return $result;
  }

  // Internal Helpers
  // --------------------------------------------------------------

  /**
   * Return one asn from InfluxDB.
   *
   * @param $id
   * @param $params
   * @return array
   */
  private function retrieve_asn_by_id($id, $params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'One Asn by id.',
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

      $database = $this->getDatabase();
      if (!is_null($id)) {
        $qdata = sprintf('SELECT * FROM "%s" WHERE "asn"=\'%s\'', self::MEASUREMENT, $id);
      } else {
        $qdata = sprintf('SELECT * FROM "%s" WHERE ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT 1',
          self::MEASUREMENT, $start, $end);
      }

      $data_point = $database->query($qdata);
      if ($data_point) {
        $rows = $data_point->getPoints();
        if (count($rows) > 0) {
          $result['data'] = [$this->pb_data($rows)[0]];
          $result['total'] = 1;
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

  public function retrieve_asn_by_id_raw($id, $params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'One Asn by id.',
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

      $database = $this->getDatabase();
      if (!is_null($id)) {
        $qdata = sprintf('SELECT * FROM "%s" WHERE "asn"=\'%s\'', self::MEASUREMENT, $id);
      } else {
        $qdata = sprintf('SELECT * FROM "%s" WHERE ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT 1',
          self::MEASUREMENT, $start, $end);
      }

      $data_point = $database->query($qdata);
      if ($data_point) {
        $rows = $data_point->getPoints();
        if (count($rows) > 0) {
          $result['data'] = $rows[0];
          $result['total'] = 1;
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
   * Return asns (one or more) filtered by some name.
   *
   * @param $params -  Request query params
   * @return array
   */
  private function retrieve_asn_by_name($params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Asn(s) by name.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data' => [],
      'status' => 'success'
    ];

    try {
      if (!is_null($params['name'])) {
        list($start, $end) = $this->get_date_range($params);
        $start = $this->reqUtils->datefstr($start);
        $end = $this->reqUtils->datefstr($end);
        $result['start_date'] = $start;
        $result['end_date'] = $end;

        $fq = $this->build_query_by_name($params['name']);
        if (!is_null($params['ranked'])) {
          $result['description'] = 'List of asns. Ranked.';
          $qcount = sprintf('SELECT COUNT(DISTINCT("asn_name")) FROM "%s" WHERE %s AND (rank >= 0 AND customer_cone_asnes >= 0) LIMIT 1',
            self::MEASUREMENT, $fq);
          $qdata = sprintf('SELECT * FROM "%s" WHERE %s AND (rank >= 0 AND customer_cone_asnes >= 0) AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC ',
            self::MEASUREMENT, $fq, $start, $end);
        } else {
          $qcount = sprintf('SELECT COUNT(DISTINCT("asn_name")) FROM "%s" WHERE %s LIMIT 1',
            self::MEASUREMENT, $fq);
          $qdata = sprintf('SELECT * FROM "%s" WHERE %s AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC',
            self::MEASUREMENT, $fq, $start, $end);
        }

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
              $r = array_slice($this->deduplicate($rows, "asn_name"), $params['offset'], $params['limit']);
              $result['data'] = $this->pb_data($r);
            }
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
   *  Retrieve Ans by multiple specified Ids. Like SQL <SELECT IN> clause.
   *
   * @param $ids      - Asns ids
   * @param $params   - Request query params
   * @return array
   */
  private function retrieve_asns_by_ids($ids, $params) {
    $result = [];
    if (!is_null($ids) && is_array($ids) && count($ids) > 0) {
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);

      try{
        $qdata = sprintf('SELECT * FROM %s WHERE %s AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC',
          $this::MEASUREMENT, $this->build_query_by_ids($ids), $start, $end );
        $database = $this->getDatabase();
        $dp =$database->query($qdata);
        $rows = $dp->getPoints();
        if(count($rows) > 0){
          $r = $this->deduplicate($rows, "org_name");
          $result = $this->pb_data($r);
        }
      }catch (Exception $e){
        $this->logger->error($e->getMessage());
      }
    }
    return $result;
  }

  /** Retrieve Ans by org id.
   *
   * @param $oid
   * @param $params
   * @return array
   */
  private function retrieve_asn_by_org_id($oid, $params){
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'One Asn by id.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data' => [],
      'status' => 'success'
    ];

    if (!is_null($oid)) {
      list($start, $end) = $this->get_date_range($params);
      $start = $this->reqUtils->datefstr($start);
      $end = $this->reqUtils->datefstr($end);
      $result['start_date'] = $start;
      $result['end_date'] = $end;

      try{
        $qdata = sprintf('SELECT * FROM %s WHERE "org_id" =~ /(?i)^%s/ AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC' ,
          $this::MEASUREMENT, $oid, $start, $end);
        $database = $this->getDatabase();
        $dp =$database->query($qdata);
        $rows = $dp->getPoints();
        if(count($rows) > 0){
          $result['data'] = $this->pb_data($rows);
        }
      }catch (Exception $e){
        $this->logger->error($e->getMessage());
      }
    }
    return $result;
  }

  /**
   * Parse and restructure dasns fields (verbose mode).
   *
   * @param $rows - Array of dataset records
   * @return array  - Array of restructured dataset rows
   */
  private function pb_data($rows): array {
    $result = [];
    if ($rows != null && is_array($rows)) {
      foreach ($rows as $row) {
        $el = [];
        $el['id'] = array_key_exists('asn', $row) ? $row['asn'] : "";
        $el['rank'] = array_key_exists('rank', $row) ? $row['rank'] : "";
        $el['name'] = array_key_exists('asn_name', $row) ? $row['asn_name'] : "";
        $el['source'] = array_key_exists('source', $row) ? $row['source'] : "";
        $el['country'] = array_key_exists('country', $row) ? $row['country'] : "";
        $el['org']['id'] = array_key_exists('org_id', $row) ? $row['org_id'] : "";
        $el['org']['name'] = array_key_exists('org_name', $row) ? $row['org_name'] : "";
        $el['latitude'] = array_key_exists('latitude', $row) ? $row['latitude'] : "";
        $el['longitude'] = array_key_exists('longitude', $row) ? $row['longitude'] : "";

        $cone = [];
        $cone['prefixes'] = array_key_exists('customer_cone_prefixes', $row) ? $row['customer_cone_prefixes'] : "";
        $cone['addresses'] = array_key_exists('customer_cone_addresses', $row) ? $row['customer_cone_addresses'] : "";
        $cone['asns'] = array_key_exists('customer_cone_asnes', $row) ? $row['customer_cone_asnes'] : "";
        $el['cone'] = $cone;

        $degree = [];
        $degree['globals'] = array_key_exists('degree_global', $row) ? $row['degree_global'] : "";
        $degree['peers'] = array_key_exists('degree_peer', $row) ? $row['degree_peer'] : "";
        $degree['siblings'] = array_key_exists('degree_sibling', $row) ? $row['degree_sibling'] : "";
        $degree['customers'] = array_key_exists('degree_customer', $row) ? $row['degree_customer'] : "";
        $degree['transits'] = array_key_exists('degree_transit', $row) ? $row['degree_transit'] : "";
        $el['degree'] = $degree;
        $el['date'] = array_key_exists('time', $row) ? $row['time'] : "";

        $result[] = $el;
      }
    }
    return $result;
  }

  /**
   * Buiild asn query string based on name param/s.
   *
   * @param $name - String of names backspace separated.
   * @return array
   */
  private function build_query_by_name($name) {
    $result = sprintf('"asn_name" =~ /(?i)%s/', $name);
    $words = explode(" ", $name);
    if (count($words) > 1) {
      $wl = [];
      foreach ($words as $word) {
        $wl[] = sprintf('"asn_name" =~ /(?i)%s/', $word);
      }
      $result = sprintf('%s', join(' AND ', $wl));
    }
    return $result;
  }

  /**
   *  Building query from multiple Asn ids.
   *
   * @param $ids
   * @return string
   */
  private function build_query_by_ids($ids) {
    $result = "asn =~ /%s/";
    if (count($ids) > 0) {
      $wl = [];
      foreach ($ids as $id) {
        $wl[] = sprintf('^%s$', $id);
      }
      $result = sprintf($result, join('|', $wl));
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

  /** Get date of last record in db
   * @return DateTime
   */
  private function get_last_date() {
    $result = new DateTime();
    $qdata = sprintf('SELECT "time", "org_name" FROM "%s" ORDER BY time DESC LIMIT 1',self::MEASUREMENT);
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

  private function sortAsns($data, $column){
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
        case "asns": {
          usort($result, function ($a, $b){
            if($a['cone']['asns'] == $b['cone']['asns']) {return 0;}
            return ($a['cone']['asns'] > $b['cone']['asns']) ? 1 : -1;
          });
          break;
        }
        case "-asns": {
          usort($result, function ($a, $b){
            if($a['cone']['asns'] == $b['cone']['asns']) {return 0;}
            return ($a['cone']['asns'] < $b['cone']['asns']) ? 1 : -1;
          });
          break;
        }
        case "prefixes": {
          usort($result, function ($a, $b){
            if($a['cone']['prefixes'] == $b['cone']['prefixes']) {return 0;}
            return ($a['cone']['prefixes'] > $b['cone']['prefixes']) ? 1 : -1;
          });
          break;
        }
        case "-prefixes": {
          usort($result, function ($a, $b){
            if($a['cone']['prefixes'] == $b['cone']['prefixes']) {return 0;}
            return ($a['cone']['prefixes'] < $b['cone']['prefixes']) ? 1 : -1;
          });
          break;
        }
        case "addresses": {
          usort($result, function ($a, $b){
            if($a['cone']['addresses'] == $b['cone']['addresses']) {return 0;}
            return ($a['cone']['addresses'] > $b['cone']['addresses']) ? 1 : -1;
          });
          break;
        }
        case "-addresses": {
          usort($result, function ($a, $b){
            if($a['cone']['addresses'] == $b['cone']['addresses']) {return 0;}
            return ($a['cone']['addresses'] < $b['cone']['addresses']) ? 1 : -1;
          });
          break;
        }
        case "transit": {
          usort($result, function ($a, $b){
            if($a['degree']['transits'] == $b['degree']['transits']) {return 0;}
            return ($a['degree']['transits'] > $b['degree']['transits']) ? 1 : -1;
          });
          break;
        }
        case "-transit": {
          usort($result, function ($a, $b){
            if($a['degree']['transits'] == $b['degree']['transits']) {return 0;}
            return ($a['degree']['transits'] < $b['degree']['transits']) ? 1 : -1;
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

  private function nonverbose($data) {
    $result = [];
    if (!is_null($data) && is_array($data) && array_key_exists("data", $data)) {
      foreach ($data['data'] as $el) {
        if (array_key_exists("asn", $el)) {
          $result[] = $el['asn'];
        } elseif (array_key_exists("id", $el)) {
          $result[] = $el['id'];
        }
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