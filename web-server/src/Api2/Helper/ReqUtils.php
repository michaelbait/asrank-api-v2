<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 28.08.18
 * Time: 17:34
 */

namespace App\Api2\Helper;

use DateInterval;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;

class ReqUtils {

  private $perpage;
  private $page;
  private $logger;

  public function __construct(int $perpage, int $page,
                              LoggerInterface $logger) {
    $this->perpage = $perpage;
    $this->page = $page;
    $this->logger = $logger;
  }

  /**
   * Make to be digit.
   * @param $digit - Some symbol
   * @return int   - digit
   */
  public static function digit_sanitizer($digit) {
    return (!is_null($digit) && is_numeric($digit)) ? (int)$digit : 0;
  }

  /**
   * Correct page_size and page_number params for pagination.
   *
   * @param $page_size - Records per page
   * @param $page_number - Current page number
   * @return array - Return arrau with limit and offset for Influxdb Query
   */
  public function pagination_sanitize($page_size, $page_number): array {
    $result = array();
    $page_size = (!is_null($page_size) && is_numeric($page_size) && (int)$page_size > 0) ? (int)$page_size : $this->perpage;
    $page_number = (!is_null($page_number) && is_numeric($page_number) && (int)$page_number > 0) ? (int)$page_number : $this->page;
    $result[] = ($page_size * $page_number) - $page_size;
    $result[] = $page_size;
    $result[] = $page_number;
    return $result;
  }

  /** Build response structure for current user request.
   *
   * @param $info     - Array of data from controller
   * @param $request  - Http current request object
   * @return array    - Builded structure
   */
  public function make_api2_response_data($info, $request): array {
    $user   = (key_exists('user', $info) && !is_null($info['user'])) ? $info['user'] : 'asrank';
    $type   = key_exists('type', $info) ? $info['type'] : "unknown";
    $descr  = key_exists('description', $info) ? $info['description'] : "No information.";
    $status = key_exists('status', $info) ? $info['status'] : "success";
    $errors = key_exists('errors', $info) ? $info['errors'] : "null";
    $total  = key_exists('total', $info)  ? $info['total'] : 0;
    $data   = key_exists('data', $info) ? $info['data'] : [];
    $pagesize = key_exists('page_size', $info) ? $info['page_size'] : $this->perpage;
    $pagenumber = key_exists('page_number', $info) ? $info['page_number'] : $this->page;
    $start_date = key_exists('start_date', $info) ? $info['start_date'] : "";
    $end_date = key_exists('end_date', $info) ? $info['end_date'] : "";

    $result = [];
    $result["type"] = $type;
    $result["error"] = $errors;
    $result["metadata"]["status"] = $status;
    $result["metadata"]["user"] = $user;
    $result["metadata"]["description"] = $descr;
    $result["metadata"]["page_number"] = $pagenumber;
    $result["metadata"]["page_size"] = $pagesize;
    $result["metadata"]["total"] = $total;
    $result["metadata"]["query_time"] = (new DateTime())->format(DateTime::ISO8601);
    $result["metadata"]["start_date"] = $start_date;
    $result["metadata"]["end_date"] = $end_date;
    $result["query_parameters"] = $this->extract_request_params($request);
    $result["data"] = $data;

    return $result;
  }

  public function normalize_date_range($start, $end){
    $result = [];
    $d1 = new DateTime();
    $d2 = new DateTime();
    if(is_string($start) && is_string($end)){
      $d1 = strtotime($start) ? new DateTime($start) : new DateTime();
      $d2 = strtotime($end) ? new DateTime($end) : new DateTime();
    }elseif(($start instanceof DateTime) && ($end instanceof DateTime)){
      $d1 = $start;
      $d2 = $end;
    }
    if($d1 > $d2){
      $tmp  = $d2;
      $d2 = $d1;
      $d1 = $tmp;
    }
    $result[] = $d1;
    $result[] = $d2;
    return $result;
  }

  /**
   * Normalize date and add one month delta using $date param as start date.
   *
   * @param $date   - initial date
   * @return array  - Fisrt element - start date
   *                  Second element - end date
   */
  public function one_month_add($date){
    $result = [];
    $start = new DateTime();
    $end = new DateTime();

    if($date instanceof DateTime ){
      $start = $date;
    }elseif (is_string($date)){
      $start = strtotime($date) ? new DateTime($date) : new DateTime();
    }
    try {
      if ($start >= $end) {
        $start = $end;
        $result[] = $start;
        $result[] = $end;
        return $result;
      }
      $tmp = clone $start;
      $delta = $tmp->add(new DateInterval('P1M'));
      if($delta > $end){
        $delta = $end;
      }
      $result[] = $start;
      $result[] = $delta;
    }catch (Exception $e){
      $this->logger->error($e->getMessage());
    }
    return $result;
  }

  public function one_month_sub($date){
    $result = [];
    $end = new DateTime();
    if($date instanceof DateTime ){
      $end = $date;
    }elseif (is_string($date)){
      $end = strtotime($date) ? new DateTime($date) : new DateTime();
    }
    try {
      $tmp = clone $end;
      $start = $tmp->sub(new DateInterval('P1M'));
      $result[] = $start;
      $result[] = $end;

    }catch (Exception $e){
      $this->logger->error($e->getMessage());
    }
    return $result;
  }

  public function datefstr($date, $pattern="Y-m-d"){
    if($date instanceof DateTime) {
      $result = date($pattern, $date->getTimestamp());
    }else{
      $result = date($pattern, (new DateTime())->getTimestamp());
    }
    return $result;
  }

  /**
   * Extract allowed params from request.
   * @param $request  - Http request
   * @return array    - Array of allowed params
   */
  private function extract_request_params($request): array{
    $result = [];
    $params = [ "id", "ds", "name", "verbose", "ranked", "sort", "start_date", "end_date", "user"];
    if(!is_null($request)){
      foreach($request->query->all() as $key => $val){
        if(!in_array($key, $params)){
          continue;
        }
        $result[$key] = $val;
      }
    }
    return $result;
  }

}