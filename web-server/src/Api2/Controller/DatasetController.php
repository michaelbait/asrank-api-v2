<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 29.08.18
 * Time: 10:58
 */

namespace App\Api2\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Swagger\Annotations as SWG;

use App\Api2\Helper\ReqUtils;
use App\Api2\Service\DatasetService;



/**
 * @Route("/api/v2/ds")
 * @Route("/api/v2/ds/")
 *
 */
class DatasetController extends AbstractController{

  private $service;
  private $reqUtils;
  private $perpage;
  private $page;

  public function __construct(int $perpage, int $page,
                              DatasetService $datasetService,
                              ReqUtils $reqUtils){
    $this->service = $datasetService;
    $this->perpage = $perpage;
    $this->page = $page;
    $this->reqUtils = $reqUtils;
  }

  /**
   * Return all datasets.
   *
   * @Route("/", methods={"GET"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Returns all datasets.")
   *
   * @SWG\Parameter(
   *     name="name",
   *     in="query",
   *     type="string",
   *     description="Dataset name. If present getting one or more Dataset(s) specified by Name.")
   *
   * @SWG\Parameter(
   *     name="ranked",
   *     in="query",
   *     type="string",
   *     description="Dataset rank. If present getting only Datasets thats rank more that 0 and customer cone is defined.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand asn information.")
   *
   * @SWG\Parameter(
   *     name="page_size",
   *     in="query",
   *     type="number",
   *     description="Elements per page. By default = 500.")
   *
   * @SWG\Parameter(
   *     name="page_number",
   *     in="query",
   *     type="number",
   *     description="Pagination page number. By default = 1.")
   *
   * @param Request $request
   * @return bool|false|float|int|string
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

  /**
   * Return an dataset specified by id.
   *
   * @Route("/{id}", methods={"GET"}, requirements={"location"="[\d]+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return one dataset specified by Id."
   * )
   *
   * @SWG\Parameter(
   *     name="id",
   *     in="path",
   *     type="string",
   *     description="An dataset Id.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand dataset information.")
   *
   * @param $id   - Specific dataset id
   * @param Request $request
   * @return Response
   */
  public function dataset($id, Request $request) {
    $params = [];
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

    $data = $this->service->get_by_id($id, $params);

    $params['type'] = $data['type'];
    $params['status'] = $data['status'];
    $params['data'] = $data['data'];
    $params['total'] = $data['total'];
    $params['description'] = $data['description'];
    if (array_key_exists('error', $data)) {
      $params['errors'] = $data['error'];
    }

    $result = $this->reqUtils->make_api2_response_data($params, $request);
    return $this->json($result);
  }

  private function params_based_data_retriever($params) {
    if (!is_null($params['name'])) {
      $result = $this->service->get_by_name($params);
    }else{
      $result = $this->service->get_all($params);
    }
    return $result;
  }



}