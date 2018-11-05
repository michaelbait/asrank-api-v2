<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 05.09.18
 * Time: 12:49
 */

namespace App\Api2\Service;

use App\Api2\Helper\LocaleHelper;
use App\Api2\Helper\ReqUtils;
use DateTime;
use Exception;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Database\RetentionPolicy;
use Psr\Log\LoggerInterface;
use Symfony\Component\Intl\Intl;


class OrgService {
  const MEASUREMENT = 'org_info_v4';
  const MEASUREMENT_NAME = 'organizations';
  const RETENTION_POLICY = 'autogen';

  private $asnService;
  private $linkService;
  private $client;
  private $db_name;
  private $db_user;
  private $db_pass;
  private $db_policy;
  private $reqUtils;
  private $logger;
  private $lh;

  /**
   * OrgService constructor.
   * Automatically injected params
   * @param $influxdb_host - InfluxDB host name
   * @param $influxdb_port - InfluxDB port number
   * @param $influxdb_name - InfluxDB database name
   * @param $influxdb_user - InfluxDb user name
   * @param $influxdb_pass - InfluxDb user password
   * @param $influxdb_policy - InfluxDb retention policy
   * @param ReqUtils $reqUtils
   * @param AsnService $asnService - ASN Servcie
   * @param RelationService $relationService
   * @param LoggerInterface $logger -  Logger
   */
  public function __construct($influxdb_host,
                              $influxdb_port,
                              $influxdb_name,
                              $influxdb_user,
                              $influxdb_pass,
                              $influxdb_policy,
                              ReqUtils $reqUtils,
                              AsnService $asnService,
                              RelationService $relationService,
                              LoggerInterface $logger) {

    $this->client = new Client($influxdb_host, $influxdb_port);
    $this->db_name = $influxdb_name;
    $this->db_user = $influxdb_user;
    $this->db_pass = $influxdb_pass;
    $this->db_policy = $influxdb_policy;
    $this->asnService = $asnService;
    $this->linkService = $relationService;
    $this->reqUtils = $reqUtils;
    $this->logger = $logger;
    $this->lh = new LocaleHelper();
  }

  /** Retrieve rganization by its id.
   *
   * @param null $id - Asn ID.
   * @param $params
   * @return array          - Rows.
   */
  public function get_by_id($id, $params) {
    $data = $this->retrieve_org_by_id($id, $params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_by_name($params) {
    $data = $this->retrieve_org_by_name($params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  /**
   * Get all orgs.
   *
   * @param $params
   * @return array
   */
  public function get_all($params) {
    $data = $this->retrieve_orgs($params);

    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_members_by_name($params) {
    $data = $this->retrieve_members_by_name($params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  public function get_members_by_id($id, $params) {
    $data = $this->retrieve_members_by_id($id, $params);
    if (is_null($params['verbose'])) {
      $data['data'] = $this->nonverbose($data);
    }
    return $data;
  }

  /**
   * Retrieve all orgs.
   *
   * @param $params
   * @return array
   */
  private function retrieve_orgs($params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'List of organizations.',
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

      $qcount = sprintf('SELECT COUNT(DISTINCT("org_name")) FROM "%s" LIMIT 1', self::MEASUREMENT);
      $qdata = sprintf('SELECT * FROM "%s" WHERE ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC',
        self::MEASUREMENT, $start, $end);
      if (!is_null($params['ranked'])) {
        $result['description'] = 'List of organizations. Ranked.';
        $qcount = sprintf('SELECT COUNT(DISTINCT("org_name")) FROM "%s" WHERE rank >= 0 AND customer_cone_asnes >= 0 LIMIT 1', self::MEASUREMENT);
        $qdata = sprintf('SELECT * FROM "%s" WHERE rank >= 0 AND customer_cone_asnes >= 0 AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC',
          self::MEASUREMENT, $start, $end);
      }

      $database = $this->getDatabase();
      $total_point = $database->query($qcount);
      $tp = $total_point->getPoints();
      if (count($tp) > 0 && array_key_exists("count", $tp[0])) {
        $total = $total_point->getPoints()[0]['count'];
        if ($total > 0) {
          $result['total'] = $total;
          $data_point = $database->query($qdata);
          $rows = $data_point->getPoints();
          if (count($rows) > 0) {
            $r = array_slice($this->deduplicate($rows, "org_id"), $params['offset'], $params['limit']);
            $r = $this->pb_data($r);
            $result['data'] = $this->sortOrgs($r, $params['sort']);
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
   * Return at last one org.
   *
   * @param $id
   * @param $params
   * @return array
   */
  private function retrieve_org_by_id($id, $params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'One Organiztion by id.',
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
      $data_point = $database->query(sprintf(
        'SELECT * FROM "%s" WHERE "org_id" =~ /(?i)^%s/  AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC',
        self::MEASUREMENT, trim($id), $start, $end));
      $rows = $data_point->getPoints();
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
   * Return orgs (one or more) filtered by some name.
   *
   * @param $params
   * @return array
   */
  private function retrieve_org_by_name($params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Organization by name.',
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
          $result['description'] = 'List of organizations. Ranked.';
          $qcount = sprintf('SELECT COUNT(DISTINCT("org_name")) FROM "%s" WHERE %s AND (rank >= 0 AND customer_cone_asnes >= 0) LIMIT 1', self::MEASUREMENT, $fq);
          $qdata = sprintf('SELECT * FROM "%s" WHERE %s AND (rank >= 0 AND customer_cone_asnes >= 0) AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
            self::MEASUREMENT, $fq, $start, $end, $params['limit'], $params['offset']);
        } else {
          $qcount = sprintf('SELECT COUNT(DISTINCT("org_name")) FROM "%s" WHERE %s LIMIT 1', self::MEASUREMENT, $fq);
          $qdata = sprintf('SELECT * FROM "%s" WHERE %s AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
            self::MEASUREMENT, $fq, $start, $end, $params['limit'], $params['offset']);
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
              $r = array_slice($this->deduplicate($rows, "org_id"), $params['offset'], $params['limit']);
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
   * Retrieve all members for one organization.
   *
   * @param $id - Organization Id.
   * @param $params
   * @param bool $flat
   * @return array
   */
  public function retrieve_members_by_id($id, $params, $flat=false) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Organization members by org id.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data' => [],
      'status' => 'success'
    ];

    try {
      if (!is_null($id)) {
        list($start, $end) = $this->get_date_range($params);
        $start = $this->reqUtils->datefstr($start);
        $end = $this->reqUtils->datefstr($end);
        $result['start_date'] = $start;
        $result['end_date'] = $end;

        $qdata = sprintf('SELECT * FROM "%s" WHERE "org_id" = \'%s\' AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
          self::MEASUREMENT, trim($id), $start, $end, $params['limit'], $params['offset']);
        $database = $this->getDatabase();
        $data_point = $database->query($qdata);
        $rows = $data_point->getPoints();
        if (count($rows) > 0) {
          if($flat){
            $result['data'] = json_decode($rows[0]['members']);
          }else{
            $result['data'] = $this->asnService->get_by_ids(json_decode($rows[0]['members']), $params);
          }
          $result['total'] = count($result['data']);
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

  public function retrieve_members_by_name($params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Organization members by org name.',
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

        $qcount = sprintf('SELECT COUNT(DISTINCT("org_name")) FROM "%s" WHERE "org_name" =~ /(?i)^%s/ LIMIT 1', self::MEASUREMENT, trim($params['name']));
        $qdata = sprintf('SELECT * FROM "%s" WHERE "org_name" =~ /(?i)^%s/ AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
          self::MEASUREMENT, trim($params['name']), $start, $end, $params['limit'], $params['offset']);

        $database = $this->getDatabase();
        $total_point = $database->query($qcount);
        $tp = $total_point->getPoints();

        if (count($tp) > 0 && array_key_exists("count", $tp[0])) {
          $total = $total_point->getPoints()[0]['count'];
          if ($total > 0) {
            $result['total'] = $total;
            $data_point = $database->query($qdata);
            $rows = $data_point->getPoints();
            $r = $this->deduplicate($rows, "org_id");
            if (count($r) > 0) {
              $membs = [];
              foreach ($r as $rr) {
                $membs = array_merge($membs, json_decode($rr['members']));
              }
              $result['data'] = $this->asnService->get_by_ids($membs, $params);
            }
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

  public function retrieve_neighbors($params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Organiztion neighbors.',
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

        $qcount = sprintf('SELECT COUNT("oid") FROM "%s" WHERE "org_id" =~ /(?i)^%s/ LIMIT 1', self::MEASUREMENT, trim($params['name']));
        $qdata = sprintf('SELECT * FROM "%s" WHERE "org_id" =~ /(?i)^%s/ AND ("time" >= \'%s\' AND "time" <= \'%s\') ORDER BY time DESC LIMIT %s OFFSET %s',
          self::MEASUREMENT, trim($params['name']), $start, $end, $params['limit'], $params['offset']);

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
              $result['data'] = $this->asnService->get_by_ids(json_decode($rows[0]['members']), $params);
            }
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

  /**
   *  Return all links for org filtered by an name.
   * @param $name     - Org Id name
   * @param $params   - Request query params
   * @return array
   */
  public function get_neighbors($name, $params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Organiztion neighbors.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data' => [],
      'status' => 'success'
    ];

    try {
      if (!is_null($name)) {
        list($start, $end) = $this->get_date_range($params);
        $start = $this->reqUtils->datefstr($start);
        $end = $this->reqUtils->datefstr($end);
        $result['start_date'] = $start;
        $result['end_date'] = $end;

        $name = trim($name);
        $data = $this->asnService->get_by_org_id($name, $params);
        $asns = $data['data'];
        if (count($asns) > 0) {
          $result['total'] = count($asns);
          foreach ($asns as $asn) {
            $relation = $this->linkService->get_by_asn($asn['id'], $params);
            $el = ["org" => $asn['org']];
            $result['start_date'] = $relation['start_date'];
            $result['end_date'] = $relation['end_date'];
            $el['links'] = $relation["data"];
            $result['data'][] = $el;
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

  public function get_neighbors_between_old($name1, $name2, $params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Links between two organizations.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data' => [],
      'status' => 'success'
    ];

    try {
      if (!is_null($name1) && !is_null($name2)) {
        list($start, $end) = $this->get_date_range($params);
        $start = $this->reqUtils->datefstr($start);
        $end = $this->reqUtils->datefstr($end);
        $result['start_date'] = $start;
        $result['end_date'] = $end;

        $org1 = $this->retrieve_org_by_id($name1, $params);
        $org2 = $this->retrieve_org_by_id($name2, $params);
        if(!is_null($org1) && !is_null($org2)){
          $m1 = $this->retrieve_members_by_id($name1, $params, true);
          $m2 = $this->retrieve_members_by_id($name2, $params, true);
          if(array_key_exists("data", $org1) && array_key_exists("data", $org2)){
            $org = [
              "org" => $org2['data'][0]['id'],
              "org-name" => $org2['data'][0]['name'],
              "rank" => $org2['data'][0]['rank'],
              "links" => [],
            ];

            $rels = array_uintersect($m1['data'], $m2['data'], function ($e1, $e2) {
              if ($e1['id'] == $e2['id']) {
                return 0;
              }
              return ($e1 > $e2) ? 1 : -1;
            });

            if (count($rels) > 0) {
              foreach ($rels as $link) {
                $links = $this->linkService->get_links_by_org_id($link['org'], $params);
                if (count($links['data']) <= 0) {
                  continue;
                }
                $org['links'] = array_merge($org['links'], $links);
              }
            }
            $result['data'] = $org;
          }
        }
        return $result;
      }
    } catch (Exception $e) {
      $result['status'] = "failure";
      $result['error'] = [
        "type" => $e->getCode(),
        "description" => $e->getMessage()
      ];
      $this->logger->error($e->getMessage());
    }
    return array_slice($result, $params['offset'], $params['limit']);
  }

  public function get_neighbors_between($name1, $name2, $params) {
    $result = [
      'type' => self::MEASUREMENT_NAME,
      'description' => 'Links between two organizations.',
      'total' => 0,
      'start_date' => '',
      'end_date' => '',
      'data' => [],
      'status' => 'success'
    ];

    try {
      if (!is_null($name1) && !is_null($name2)) {
        $org1 = $this->retrieve_org_by_id($name1, $params);
        $org2 = $this->retrieve_org_by_id($name2, $params);
        if(!is_null($org1) && !is_null($org2)) {
          $m1 = $this->retrieve_members_by_id($name1, $params, true);
          $m2 = $this->retrieve_members_by_id($name2, $params, true);

          $result['data']['org'] =$org2['data'][0]['id'];
          $result['data']['rank'] =$org2['data'][0]['rank'];

          $rels = [];
          foreach ($m1['data'] as $m){
            $neighbors = $this->linkService->retrieve_neighbors_by_asn($m, $params,true);
            foreach($neighbors['data'] as $neighbor){
              if(in_array($neighbor['neighbor'], $m2['data'])){
                $links = $this->linkService->retrive_links_by_asn_and_neighbor($m, $neighbor['neighbor'], $params);
                if(count($links['data']) > 0){
                  $rels = array_merge($links['data'], $rels);
                }
              }
            }
          }
          $r = $this->deduplicate($rels, 'asn1');
          $result['total'] = count($r);
          $result['data']['links'] = $r;
        }
      }
    }catch (Exception $e) {
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
   * Parse and restructure orgs fields (verbose mode).
   *
   * @param $rows - Array of orgs records
   * @return array  - Array of restructured orgs rows
   */
  private function pb_data($rows): array {
    $result = [];
    if ($rows != null && is_array($rows)) {
      foreach ($rows as $row) {
        $el = [];
        $el["id"] = array_key_exists('org_id', $row) ? $row['org_id'] : "";
        $el["name"] = array_key_exists('org_name', $row) ? $row['org_name'] : "";
        $el["country"] = array_key_exists('country', $row) ? $row['country'] : "";
        $country = $this->lh->get_country($el["country"]);
        $el["country_name"] = $country;
        $el["rank"] = array_key_exists('rank', $row) ? $row['rank'] : "";
        $el["number_members"] = array_key_exists('number_members', $row) ? $row['number_members'] : "";
        $el["members"] = array_key_exists('members', $row) ? json_decode($row['members']) : [];

        $cone = [];
        $cone['prefixes'] = array_key_exists('customer_cone_prefixes', $row) ? $row['customer_cone_prefixes'] : "";
        $cone['addresses'] = array_key_exists('customer_cone_addresses', $row) ? $row['customer_cone_addresses'] : "";
        $cone['asns'] = array_key_exists('customer_cone_asnes', $row) ? $row['customer_cone_asnes'] : "";
        $cone['orgs'] = array_key_exists('customer_cone_orgs', $row) ? $row['customer_cone_orgs'] : "";
        $el['cone'] = $cone;

        $degree = [];
        $degree['asn'] = [
          "transit" => array_key_exists('asn_degree_transit', $row) ? $row['asn_degree_transit'] : "",
          "global" => array_key_exists('asn_degree_global', $row) ? $row['asn_degree_global'] : ""
        ];
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
    return sprintf('"org_name" =~ /(?i)^%s/', trim($name));
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

  private function sortOrgs($data, $column){
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
        case "asnes": {
          usort($result, function ($a, $b){
            if($a['cone']['asnes'] == $b['cone']['asnes']) {return 0;}
            return ($a['cone']['asnes'] > $b['cone']['asnes']) ? 1 : -1;
          });
          break;
        }
        case "-asnes": {
          usort($result, function ($a, $b){
            if($a['cone']['asnes'] == $b['cone']['asnes']) {return 0;}
            return ($a['cone']['asnes'] < $b['cone']['asnes']) ? 1 : -1;
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
        if (array_key_exists("name", $el)) {
          $result[] = $el['name'];
        }
      }
      sort($result);
    }
    return $result;
  }

  // Remove duplicates for asns
  private function deduplicate($array, $field) {
    $result = [];
    $temp = [];
    if (!is_null($array) && !empty($field)) {
      foreach ($array as $val) {
        if (array_key_exists($val[$field], $temp)) {
          $time1 = strtotime($temp[$val[$field]]['time']);
          $time2 = strtotime($val['time']);
          if ($time1 < $time2) {
            $temp[$val[$field]] = $val;
          }
        } else {
          $temp[$val[$field]] = $val;
        }
      }
      foreach ($temp as $tmp) {
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