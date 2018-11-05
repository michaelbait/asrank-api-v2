<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 24.10.18
 * Time: 22:21
 */

namespace App\Controller\Rest;

use App\Service\RelationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use App\Helper\ReqUtils;
use App\Service\AsnService;

class AsnRestController extends AbstractController{

  private $asnService;
  private $relService;
  private $reqUtils;

  public function __construct(AsnService $service,
                              RelationService $relationService,
                              ReqUtils $reqUtils) {
    $this->asnService = $service;
    $this->relService = $relationService;
    $this->reqUtils = $reqUtils;
  }

  /**
   *
   * @Route("/rest/asns/", methods={"GET"})
   *
   * @param Request $request
   * @param bool $top_ten
   * @return Response
   *
   */
  public function asns(Request $request) {
    $params = [];
    $params['user'] = $request->get("user");
    $params['name'] = $request->get("name");
    $params['ranked'] = $request->get("ranked");
    $params['sort'] = !is_null($request->get("sort")) ? $request->get("sort") : 'rank';
    $params['verbose'] = $request->get("verbose");
    $params['start_date'] = $request->get("start_date");
    $params['end_date'] = $request->get("end_date");

    list($offset, $limit, $page) = $this->reqUtils->pagination_sanitize(
      $request->get("page_size"),
      $request->get("page_number"));

    $params['page_size'] = $limit;
    $params['page_number'] = $page;
    $params['limit'] = $limit;
    $params['offset'] = $offset;

    $data = $this->params_based_data_retriever($params);

    $params['type'] = $data['type'];
    $params['status'] = $data['status'];
    $params['data'] = $data['data'];
    $params['total'] = $data['total'];
    $params['start_date'] = $data['start_date'];
    $params['end_date'] = $data['end_date'];
    $params['description'] = $data['description'];
    if (array_key_exists('error', $data)) {
      $params['errors'] = $data['error'];
    }

    $result = $this->reqUtils->make_api2_response_data($params, $request);
    return $this->json($result);
  }

  /**
   *
   * @Route("/rest/asns/{id}/links", methods={"GET"}, requirements={"id"="[\d]+"})
   *
   * @param $id
   * @param Request $request
   * @return Response
   */
  public function asn_links($id, Request $request) {
    $params = [];
    $params['user'] = $request->get("user");
    $params['verbose'] = $request->get("verbose");
    $params['sort'] = !is_null($request->get("sort")) ? $request->get("sort") : 'rank';
    $params['start_date'] = $request->get("start_date");
    $params['end_date'] = $request->get("end_date");

    list($offset, $limit, $page) = $this->reqUtils->pagination_sanitize(
      $request->get("page_size"),
      $request->get("page_number"));

    $params['page_size'] = $limit;
    $params['page_number'] = $page;
    $params['limit'] = $limit;
    $params['offset'] = $offset;

    $data = $this->relService->get_by_asn($id, $params);

    $params['type'] = $data['type'];
    $params['description'] = $data['description'];
    $params['status'] = $data['status'];
    $params['data'] = $data['data'];
    $params['total'] = $data['total'];
    $params['start_date'] = $data['start_date'];
    $params['end_date'] = $data['end_date'];
    if(array_key_exists('error', $data)){
      $params['errors'] = $data['error'];
    }
    $params['page_size'] = $limit;
    $params['page_number'] = $page;

    $result = $this->reqUtils->make_api2_response_data($params, $request);
    return $this->json($result);
  }

  private function params_based_data_retriever($params) {
    $result = [];
    if (!is_null($params['name'])) {
      $result = $this->asnService->get_all($params);
    }else{
      $result = $this->asnService->get_all($params);
    }
    return $result;
  }

}