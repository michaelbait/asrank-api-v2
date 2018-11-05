<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 24.10.18
 * Time: 19:48
 */

namespace App\Service;

use Exception;
use DateTime;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Database\RetentionPolicy;

use App\Helper\ReqUtils;
use Psr\Log\LoggerInterface;

class AsnService {

  const MEASUREMENT = 'asns.rank.asc';
  const MEASUREMENT_NAME = 'asn';
  const RETENTION_POLICY = 'autogen';

  private $client;
  private $db;
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
   * @param $influxdb_name2 - InfluxDB database name
   * @param $influxdb_user - InfluxDb user name
   * @param $influxdb_pass - InfluxDb user password
   * @param $influxdb_policy - InfluxDb retention policy
   * @param ReqUtils $reqUtils
   * @param LoggerInterface $logger
   * @throws Database\Exception
   */
  public function __construct($influxdb_host,
                              $influxdb_port,
                              $influxdb_name2,
                              $influxdb_user,
                              $influxdb_pass,
                              $influxdb_policy,
                              ReqUtils $reqUtils,
                              LoggerInterface $logger) {

    $this->db_name = $influxdb_name2;
    $this->db_user = $influxdb_user;
    $this->db_pass = $influxdb_pass;
    $this->db_policy = $influxdb_policy;
    $this->reqUtils = $reqUtils;
    $this->logger = $logger;
    $this->client = new Client($influxdb_host, $influxdb_port);
    $this->db = $this->getDatabase();
  }

  public function get_all($params) {
    $data = $this->retrieve_asns($params);
    if (!is_null($params['ranked'])) {
      $data['description'] = 'List od Asns. Ranked.';
    }
    return $data;
  }

  public function get_by_id($id, $params) {
    return $this->retrieve_asn_by_id($id, $params);
  }

  public function get_by_org($id, $params) {
    return $this->retrieve_by_org_id($id, $params);
  }

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

      $sort = !is_null($params['sort']) ? $params['sort'] : 'rank';
      $measurement = $this->getMeasure('asns', $sort);

      if (!is_null($id)) {
        $qdata = sprintf('SELECT * FROM "%s" WHERE "asn"=\'%s\'', $measurement, $id);
      } else {
        $qdata = sprintf('SELECT * FROM "%s" LIMIT 1', $measurement, $start, $end);
      }

      $database = $this->getDatabase();
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

  /** Retrieve Ans by org id.
   *
   * @param $oid
   * @param $params
   * @return array
   */
  private function retrieve_by_org_id($oid, $params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Asns by org id.',
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

    $sort = !is_null($params['sort']) ? $params['sort'] : 'rank';
    $measurement = $this->getMeasure('asns', $sort);

    try {
      $qcount = sprintf('SELECT COUNT("asn_f") FROM "%s" WHERE "org_id" = \'%s\'', $measurement, $oid);
      $qdata = $qdata = sprintf('SELECT * FROM "%s" WHERE "org_id" = \'%s\' AND "rank" != \'0\' LIMIT %s OFFSET %s',
        $measurement, $oid, $params['limit'], $params['offset']);

      $total_point = $this->db->query($qcount);
      $total = $total_point->getPoints()[0]['count'];

      $data_point = $this->db->query($qdata);
      $rows = $data_point->getPoints();
      $result['data'] = $this->pb_data($rows);
      $result['total'] = $total;

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

    $sort = !is_null($params['sort']) ? $params['sort'] : 'rank';
    $measurement = $this->getMeasure('asns', $sort);

    try {
      $result['description'] = 'List of asns. Ranked.';
      $qcount = sprintf('SELECT asns_rank FROM counts');
      $qdata = sprintf('SELECT * FROM "%s" WHERE rank != \'0\' LIMIT %s OFFSET %s',
        $measurement, $params['limit'], $params['offset']);
      $total_point = $this->db->query($qcount);
      $total = $total_point->getPoints()[0]['asns_rank'];
      $result['total'] = $total;
      if ($total > 0) {
        $data_point = $this->db->query($qdata);
        $rows = $data_point->getPoints();
        if (count($rows) > 0) {
          $result['data'] = $this->pb_data($rows);
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

  private function sortAsns($data, $column) {
    $result = $data;
    if (!is_null($data) && is_array($data) && count($data) > 0) {
      $k = trim($column);
      switch ($k) {
        case "rank":
          {
            usort($result, function ($a, $b) {
              if ($a['rank'] == $b['rank']) {
                return 0;
              }
              return ($a['rank'] > $b['rank']) ? 1 : -1;
            });
            break;
          }
        case "-rank":
          {
            usort($result, function ($a, $b) {
              if ($a['rank'] == $b['rank']) {
                return 0;
              }
              return ($a['rank'] < $b['rank']) ? 1 : -1;
            });
            break;
          }
        case "asns":
          {
            usort($result, function ($a, $b) {
              if ($a['cone']['asns'] == $b['cone']['asns']) {
                return 0;
              }
              return ($a['cone']['asns'] > $b['cone']['asns']) ? 1 : -1;
            });
            break;
          }
        case "-asns":
          {
            usort($result, function ($a, $b) {
              if ($a['cone']['asns'] == $b['cone']['asns']) {
                return 0;
              }
              return ($a['cone']['asns'] < $b['cone']['asns']) ? 1 : -1;
            });
            break;
          }
        case "prefixes":
          {
            usort($result, function ($a, $b) {
              if ($a['cone']['prefixes'] == $b['cone']['prefixes']) {
                return 0;
              }
              return ($a['cone']['prefixes'] > $b['cone']['prefixes']) ? 1 : -1;
            });
            break;
          }
        case "-prefixes":
          {
            usort($result, function ($a, $b) {
              if ($a['cone']['prefixes'] == $b['cone']['prefixes']) {
                return 0;
              }
              return ($a['cone']['prefixes'] < $b['cone']['prefixes']) ? 1 : -1;
            });
            break;
          }
        case "addresses":
          {
            usort($result, function ($a, $b) {
              if ($a['cone']['addresses'] == $b['cone']['addresses']) {
                return 0;
              }
              return ($a['cone']['addresses'] > $b['cone']['addresses']) ? 1 : -1;
            });
            break;
          }
        case "-addresses":
          {
            usort($result, function ($a, $b) {
              if ($a['cone']['addresses'] == $b['cone']['addresses']) {
                return 0;
              }
              return ($a['cone']['addresses'] < $b['cone']['addresses']) ? 1 : -1;
            });
            break;
          }
        case "transit":
          {
            usort($result, function ($a, $b) {
              if ($a['degree']['transits'] == $b['degree']['transits']) {
                return 0;
              }
              return ($a['degree']['transits'] > $b['degree']['transits']) ? 1 : -1;
            });
            break;
          }
        case "-transit":
          {
            usort($result, function ($a, $b) {
              if ($a['degree']['transits'] == $b['degree']['transits']) {
                return 0;
              }
              return ($a['degree']['transits'] < $b['degree']['transits']) ? 1 : -1;
            });
            break;
          }
        default:
          {
            break;
          }
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
        $el['country_name'] = array_key_exists('country_name', $row) ? $row['country_name'] : "";
        $el['org']['id'] = array_key_exists('org_id', $row) ? $row['org_id'] : "";
        $el['org']['name'] = array_key_exists('org_name', $row) ? $row['org_name'] : "";
        $el['latitude'] = array_key_exists('latitude', $row) ? $row['latitude'] : "";
        $el['longitude'] = array_key_exists('longitude', $row) ? $row['longitude'] : "";

        $cone = [];
        $cone['prefixes'] = array_key_exists('customer_cone_prefixes', $row) ? $row['customer_cone_prefixes'] : "";
        $cone['addresses'] = array_key_exists('customer_cone_addresses', $row) ? $row['customer_cone_addresses'] : "";
        $cone['asns'] = array_key_exists('customer_cone_asns', $row) ? $row['customer_cone_asns'] : "";
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

  private function get_date_range($params) {
    $date_start = array_key_exists('start_date', $params) ? $params['start_date'] : null;
    $date_end = array_key_exists('end_date', $params) ? $params['end_date'] : null;

    if (!is_null($date_start) && !is_null($date_end)) {
      $result = $this->reqUtils->normalize_date_range($date_start, $date_end);
    } elseif (!is_null($date_start) && is_null($date_end)) {
      $result = $this->reqUtils->one_month_add($date_start);
    } else {
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
    $qdata = sprintf('SELECT "time", "org_name" FROM "%s" ORDER BY time DESC LIMIT 1', self::MEASUREMENT);
    try {
      $database = $this->getDatabase();
      $data_point = $database->query($qdata);
      $rows = $data_point->getPoints();
      if (count($rows) > 0) {
        $result = (array_key_exists('time', $rows[0]) && strtotime($rows[0]['time'])) ? new DateTime($rows[0]['time']) : new DateTime();
      }
    } catch (Exception $e) {
      $this->logger->error($e->getMessage());
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
    } catch (Database\Exception $e) {
      $this->logger->error($e->getMessage());
    }
    return $database;
  }

  private function getMeasure($prefix = null, $sort = null) {
    $result = self::MEASUREMENT;
    if (!is_null($prefix) && !is_null($sort)) {
      if (substr($sort, 0, 1) === '-') {
        $suffix = 'desc';
        $tag = substr($sort, 1, strlen($sort));
      } else {
        $suffix = 'asc';
        $tag = $sort;
      }
      $result = $prefix . '.' . $tag . '.' . $suffix;
    }
    return $result;
  }
}