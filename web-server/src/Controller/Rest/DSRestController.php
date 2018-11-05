<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 25.10.18
 * Time: 12:59
 */

namespace App\Controller\Rest;


use App\Helper\ReqUtils;
use App\Service\DatasetService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DSRestController extends AbstractController{

  private $dsService;
  private $reqUtils;

  public function __construct(DatasetService $datasetService, ReqUtils $reqUtils) {
    $this->dsService = $datasetService;
    $this->reqUtils = $reqUtils;
  }

  /**
   *
   * @Route("/rest/ds/", methods={"GET"})
   *
   * @param Request $request
   * @param bool $top_ten
   * @return Response
   *
   */
  public function datasets(Request $request) {
    $params = [];
    $params['name'] = $request->get("name");
    $params['user'] = $request->get("user");
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

  private function params_based_data_retriever($params) {
    if (!is_null($params['name'])) {
      $result = $this->dsService->get_by_name($params);
    }else{
      $result = $this->dsService->get_all($params);
    }
    return $result;
  }
}