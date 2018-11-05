<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 05.09.18
 * Time: 13:11
 */

namespace App\Api2\Controller;

use App\Api2\Service\RelationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Swagger\Annotations as SWG;

use App\Api2\Helper\ReqUtils;
use App\Api2\Service\AsnService;

/**
 * @Route("/api/v2/asns")
 * @Route("/api/v2/asns/")
 */
class AsnController extends AbstractController {

  private $service;
  private $relation;
  private $reqUtils;
  private $perpage;
  private $page;

  public function __construct(int $perpage, int $page,
                              AsnService $service, RelationService $relation, ReqUtils $reqUtils) {
    $this->service = $service;
    $this->relation = $relation;
    $this->perpage = $perpage;
    $this->page = $page;
    $this->reqUtils = $reqUtils;
  }

  /**
   * Return list of Asns.
   *
   * @Route("/", methods={"GET"})
   *
   * @SWG\Response(
   *   response=200,
   *   description="Return list of Asns."
   * )
   *
   * @SWG\Parameter(
   *     name="name",
   *     in="query",
   *     type="string",
   *     description="ASN name. If present getting one or more Asn(s) specified by its Name.")
   *
   * @SWG\Parameter(
   *     name="ranked",
   *     in="query",
   *     type="boolean",
   *     description="Asn rank. If present getting only Asns thats rank more that 0 and customer cone is defined.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand asn information.")
   *
   * @SWG\Parameter(
   *     name="sort",
   *     in="query",
   *     type="string",
   *     description="Sorting column. By default = rank")
   *
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
   *
   * @param Request $request
   * @return bool|false|float|int|string
   */
  public function asns(Request $request) {
    $params = [];
    $params['user'] = $request->get("user");
    $params['name'] = $request->get("name");
    $params['ranked'] = $request->get("ranked");
    $params['sort'] = $request->get("sort");
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
   * Return one Asn specified by Id.
   *
   * @Route("/{id}", methods={"GET"}, requirements={"id":"[\d]+"})
   *
   * @SWG\Response(
   *   response=200,
   *   description="Return one Asn specified by its ID.")
   *
   *  @SWG\Parameter(
   *     name="Id",
   *     in="path",
   *     type="string",
   *     description="An Asn Id.")
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
   * @param $id - Specific asn id
   * @param Request $request
   * @return Response
   * @throws \InfluxDB\Database\Exception
   */
  public function asn($id, Request $request) {
    $params = [];
    $params['user'] = $request->get("user");
    $params['ranked'] = $request->get("ranked");
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
   * Return links (relations) involving the given ASN by Id.
   *
   * @Route("/{id}/links", methods={"GET"}, requirements={"id"="[\d]+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return one asn specified by placeholder {id}."
   * )
   *
   * @SWG\Parameter(
   *     name="Id.",
   *     in="path",
   *     type="string",
   *     description="An Asn Id.")
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
   * @param $id   - Specific asn id
   * @param Request $request
   * @return Response
   * @throws \InfluxDB\Database\Exception
   */
  public function asn_links($id, Request $request) {
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

    $data = $this->relation->get_by_asn($id, $params);

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

  /**
   * Return the links (trelations) between two ASNs.
   *
   * @Route("/{id1}/links/{id2}", methods={"GET"}, requirements={"id1"="[\d]+", "id2"="[\d]+"})
   * @Route("/links/{id1}/{id2}", methods={"GET"}, requirements={"id1"="[\d]+", "id2"="[\d]+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return links between two asns."
   * )
   *
   * @SWG\Parameter(
   *     name="id1",
   *     in="path",
   *     type="string",
   *     description="Ans id1.")
   *
   * @SWG\Parameter(
   *     name="id2",
   *     in="path",
   *     type="string",
   *     description="Ans id2.")
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
   * @param $id1          - First Asn Id
   * @param $id2          - econd Asn Id
   * @param Request $request
   * @return Response
   */
  public function asn_ranged_links($id1, $id2, Request $request) {
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

    $data = $this->relation->get_ranged($id1, $id2, $params);

    $params['type'] = $data['type'];
    $params['description'] = $data['description'];
    $params['status'] = $data['status'];
    $params['data'] = $data['data'];
    $params['start_date'] = $data['start_date'];
    $params['end_date'] = $data['end_date'];
    $params['total'] = $data['total'];
    if (array_key_exists('error', $data)) {
      $params['errors'] = $data['error'];
    }
    $params['page_size'] = $limit;
    $params['page_number'] = $page;

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