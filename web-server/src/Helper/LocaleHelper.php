<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 10.09.18
 * Time: 17:24
 */

namespace App\Helper;

use Symfony\Component\Intl\Intl;

/**
 * Class LocaleHelper mixing INTL Localization Component.
 *
 * @package App\Api2\Helper
 */
class LocaleHelper {

  private $languages = [];
  private $countries = [];

  public function __construct() {
      $this->init_langs();
      $this->init_countries();
  }

  public function get_languages(){
    return $this->languages;
  }

  public function get_countries(){
    return $this->countries;
  }

  public function get_language($locale){
    $locale = strtoupper($locale);
    return array_key_exists($locale, $this->languages) ? $this->languages[$locale] : $this->languages[""];
  }

  public function get_country($locale){
    $locale = strtoupper($locale);
    return array_key_exists($locale, $this->countries) ? $this->countries[$locale] : $this->countries[""];
  }

  private function init_langs(){
    $this->languages[""] = "unknown";
    $this->countries["EU"] = "European Union";
    $langs = Intl::getLanguageBundle()->getLanguageNames();
    $this->languages = array_merge($this->countries, $langs);
  }

  private function init_countries(){
    $this->countries[""] = "unknown";
    $ctrs = Intl::getRegionBundle()->getCountryNames();
    $this->countries = array_merge($this->countries, $ctrs);
  }

}