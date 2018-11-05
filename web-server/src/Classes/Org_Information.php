<?php

namespace App\Classes;

class Org_Information {
  const UNKNOWN = '<span class="asrank-unknown">unknown</span>';

  public $id = self::UNKNOWN;
  public $name = self::UNKNOWN;
  public $rank = self::UNKNOWN;
  public $country = self::UNKNOWN;

  public $number_members = 0;
  public $number_members_ranked = 0;
  public $members = array();

  public $cone = array(
    "addresses" => 0
  , "orgs" => 0
  , "prefixes" => 0
  , "asns" => 0
  );

  public $degree = array(
    "asn" => array(
      "global" => 0
    , "transit" => 0
    )
  , "org" => array(
      "global" => 0
    , "transit" => 0
    )
  );

  public function __construct($id) {
    $this->id = $id;
  }

  public function GET_JSON($orgService, $params) {
    $el = $orgService->get_by_id($this->id, $params);
    if (array_key_exists('data', $el) && count($el['data']) > 0) {
      $data = $el['data'][0];
      if (count($data) > 0) {
        foreach ($data as $key => $value) {
          if ($key == "cone") {
            foreach ($value as $k => $v) {
              $this->$key[$k] = $v;
            }
          } else if ($key == "degree") {
            foreach ($value as $k => $v) {
              foreach ($v as $l => $m) {
                $this->$key[$k][$l] = $m;
              }
            }
          } else {
            $this->$key = $value;
          }
        }
      }
    }
  }

  public function GET_JSON1() {
    $json = file_get_contents(getenv('RESTFUL_DATABASE_URL') . '/orgs/' . $this->id . '?verbose');
    if ($json != NULL) {
      $parsed = json_decode($json);
      if ($parsed->{'data'} != NULL) {
        $data = $parsed->{'data'};

        foreach ($data as $key => $value) {
          if ($key == "cone") {
            foreach ($value as $k => $v) {
              $this->$key[$k] = $v;
            }
          } else if ($key == "degree") {
            foreach ($value as $k => $v) {
              foreach ($v as $l => $m) {
                $this->$key[$k][$l] = $m;
              }
            }
          } else {
            $this->$key = $value;
          }
        }
      }
    }
  }

  public function get_name() {
    return $this->name || $this->id;
  }

  public function get_json_ld() {
    $this->{'name'} = str_replace("\"", " ", $this->{'name'});
    return json_encode($this);
  }
}
