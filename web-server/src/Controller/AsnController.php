<?php

namespace App\Controller;

use App\Classes\ASN_Information;
use App\Classes\Location;
use App\Service\AsnService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AsnController extends Controller {
  const PAGE_SIZE = 40;

  private $asnService;

  public function __construct(AsnService $service) {
    $this->asnService = $service;
  }


  /**
   * @Route("/", name="default")
   * @param Request $request
   * @return Response
   */
  public function asns_top_ten(Request $request) {
    return $this->asns($request, True);
  }

  /**
   *
   * @Route("/asns", name="asns_ranking", methods={"GET"})
   *
   * @param Request $request
   * @param bool $top_ten
   * @return Response
   *
   */
  public function asns(Request $request, $top_ten = False) {
    $params = [];

    $params['page_number'] = $request->get("page_number") ? $request->get("page_number") : 1;
    $params['page_size'] = $request->get("page_size") ? $request->get("page_size") : self::PAGE_SIZE;
    $params['ranked'] = $request->get("ranked");
    $params['verbose'] = $request->get("verbose");
    $params['sort'] = !is_null($request->get("sort")) ? $request->get("sort") : 'rank';
    $params['top_ten'] = $top_ten;
    $params['org'] = false;

    $params['page_number'] = $this->digit_santizer($params['page_number'], 1);
    return $this->render('asns/asns.html.twig', ['params' => $params]);
  }


  /**
   * @Route("/asns/{asn}", name="asn_neighbors")
   * @Route("/asns/{asn}/neighbors")
   * @Route("/asns/{asn}/as-core", name="asn_as_core", defaults={"area"="as-core"})
   * @param Request $request
   * @param string $asn
   * @param string $area
   * @return Response
   */
  public function asn_neighbors(Request $request, $asn = "", $area = "neighbors") {
    $params = [];

    $params['page_number'] = $request->get("page_number") ? $request->get("page_number") : 1;
    $params['page_size'] = $request->get("page_size") ? $request->get("page_size") : self::PAGE_SIZE;
    $params['ranked'] = $request->get("ranked");
    $params['verbose'] = $request->get("verbose");
    $params['sort'] = $request->get("sort");
    $params['org'] = false;

    if (!preg_match("/^\d+$/", $asn)) {
      return $this->asn_search($request, $asn);
    }

    $params['page_number'] = $this->digit_santizer($params['page_number'], 1);

    $asn_info = new ASN_Information($asn);
    $asn_info->GET_JSON($this->asnService, $params);

    $location = new Location("asn", $area, $asn_info);
    $params['asn_info'] = $asn_info;
    $params['location'] = $location;

    return $this->render('asns/asn.html.twig', ['params' => $params]);
  }

  /**
   * @Route("/asns/", name="asn_search")
   * @Route("/asns/by-name")
   * @Route("/asns/by-name/")
   * @Route("/asns/by-name/{name}")
   * @param Request $request
   * @param null $name
   * @return Response
   */
  public function asn_search(Request $request, $name = NULL) {
    $page = $this->digit_santizer($request->query->get('page_number'), 1);
    if ($name == NULL) {
      $name = $request->query->get('name');
    }

    if (preg_match("/^\d+$/", $name) || preg_match("/^asn?(\d+)$/i", $name)) {
      $name = preg_replace("/\D/", '', $name);
      return $this->asn_neighbors($request, $name);
    }

    return $this->render('asns/asn_search.html.twig', array(
      'name' => $name
    , 'page_number' => $page
    , 'page_size' => self::PAGE_SIZE
    ));
  }

  /*
   * Used to sanitize digits
   */
  private function digit_santizer($digit, $default = 0) {
    if ($digit == NULL || !preg_match("/^\d+$/", $digit)) {
      $digit = $default;
    }
    return $digit;
  }


}
