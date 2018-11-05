<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 05.09.18
 * Time: 12:47
 */

namespace App\Api2\Controller;

use App\Api2\Service\AsnService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Swagger\Annotations as SWG;

use App\Api2\Service\OrgService;
use App\Api2\Helper\ReqUtils;

/**
 * @Route("/api/v2/orgs")
 * @Route("/api/v2/orgs/");
 */
class OrgController extends AbstractController {

  private $service;
  private $reqUtils;
  private $perpage;
  private $page;

  public function __construct(int $perpage, int $page,
                              OrgService $service,
                              ReqUtils $reqUtils){
    $this->service = $service;
    $this->perpage = $perpage;
    $this->page = $page;
    $this->reqUtils = $reqUtils;
  }

  /**
   * Return members belonging to an organization specified by name.
   *
   * @Route("/members", methods={"GET"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return list of organization members.")
   *
   * @SWG\Parameter(
   *     name="name",
   *     in="query",
   *     type="string",
   *     description="Organization name.")
   *
   * @SWG\Parameter(
   *     name="ranked",
   *     in="query",
   *     type="boolean",
   *     description="Organization rank. If present getting only members thats rank more that 0 and customer cone is defined.")
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
   * @param $name
   * @param Request $request
   * @return bool|false|float|int|string
   */
  public function org_members_by_name(Request $request){
    $params = [];
    $params['user'] = $request->get("user");
    $params['name'] = $request->get("name");
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

    $data = $this->service->get_members_by_name($params);

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
   * Return members for an organization specified by Id.
   *
   * @Route("/{id}/members", methods={"GET"}, requirements={"id"="(.)+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return list of members for an organization.")
   *
   *  @SWG\Parameter(
   *     name="Id",
   *     in="path",
   *     type="string",
   *     description="An organization Id.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand org information.")
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
   * @param $id
   * @param Request $request
   * @return bool|false|float|int|string
   */
  public function org_members_by_id($id, Request $request){
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

    $data = $this->service->get_members_by_id($id, $params);

    $params['type'] = $data['type'];
    $params['status'] = $data['status'];
    $params['data'] = $data['data'];
    $params['total'] = $data['total'];
    $params['start_date'] = $data['start_date'];
    $params['end_date'] = $data['end_date'];
    $params['description'] = $data['description'];
    if(array_key_exists('error', $data)){
      $params['errors'] = $data['error'];
    }
    $params['page_size'] = $limit;
    $params['page_number'] = $page;

    $result = $this->reqUtils->make_api2_response_data($params, $request);

    return $this->json($result);
  }

  /**
   * Get a list of an organization's neighbors.
   *
   * @Route("/{name1}/neighbors/{name2}", methods={"GET"}, requirements={"name1"="[^/]+", "name2"="[^/]+"})
   * @Route("/neighbors/{name1}/{name2}", methods={"GET"}, requirements={"name1"="[^/]+", "name2"="[^/]+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return a list of an organization's neighbors")
   *
   * @SWG\Parameter(
   *     name="name1",
   *     in="path",
   *     type="string",
   *     description="Name of first organization.")
   *
   * @SWG\Parameter(
   *     name="name2",
   *     in="path",
   *     type="string",
   *     description="Name of second organization.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand org information.")
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
   * @param $name1
   * @param $name2
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function between_orgs_neighbors($name1, $name2, Request $request){
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

    $data = $this->service->get_neighbors_between($name1, $name2, $params);

    $params['type'] = $data['type'];
    $params['status'] = $data['status'];
    $params['data'] = $data['data'];
    $params['total'] = $data['total'];
    $params['start_date'] = $data['start_date'];
    $params['end_date'] = $data['end_date'];
    $params['description'] = $data['description'];
    if(array_key_exists('error', $data)){
      $params['errors'] = $data['error'];
    }
    $params['page_size'] = $limit;
    $params['page_number'] = $page;

    $result = $this->reqUtils->make_api2_response_data($params, $request);

    return $this->json($result);
  }

  /**
   * Return a list of an organization's neighbors.
   *
   * @Route("/{name}/neighbors", methods={"GET"}, requirements={"name"="[^/]+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return a list of an organization's neighbors.")
   *
   * @SWG\Parameter(
   *     name="name",
   *     in="path",
   *     type="string",
   *     description="An organization name.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand org information.")
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
   * @param $name
   * @param Request $request
   * @return bool|false|float|int|string
   */
  public function org_neighbors($name, Request $request){
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

    $data = $this->service->get_neighbors($name, $params);

    $params['type'] = $data['type'];
    $params['status'] = $data['status'];
    $params['data'] = $data['data'];
    $params['total'] = $data['total'];
    $params['start_date'] = $data['start_date'];
    $params['end_date'] = $data['end_date'];
    $params['description'] = $data['description'];
    if(array_key_exists('error', $data)){
      $params['errors'] = $data['error'];
    }
    $params['page_size'] = $limit;
    $params['page_number'] = $page;

    $result = $this->reqUtils->make_api2_response_data($params, $request);

    return $this->json($result);
  }

  public function org_by_name($name, Request $request){
    $params = [];
    $params['name'] = $name;
    $params['user'] = $request->get("user");
    $params['start_date'] = $request->get("start_date");
    $params['end_date'] = $request->get("end_date");
    $params['verbose'] = $request->get("verbose");

    $page_size = $request->get("page_size");
    $page_number = $request->get("page_number");

    list(, $limit, $page) = $this->reqUtils->pagination_sanitize($page_size, $page_number);

    $data = $this->service->get_by_name($params);

    $params['type'] = $data['type'];
    $params['status'] = $data['status'];
    $params['data'] = $data['data'];
    $params['total'] = count($data['data']);
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
   * Return information about an organization.
   *
   * @Route("/{id}", methods={"GET"}, requirements={"id"="[^/]+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return one organization.")
   *
   * @SWG\Parameter(
   *     name="id",
   *     in="path",
   *     type="string",
   *     description="An organization ID.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand organization information.")
   *
   * @param $id   - Organization ID.
   * @param Request $request
   * @return bool|false|float|int|string
   */
  public function org($id, Request $request){
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
   * Return information about all organizations.
   *
   * @Route("/", methods={"GET"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Returns list of organizations.")
   *
   * @SWG\Parameter(
   *     name="name",
   *     in="query",
   *     type="string",
   *     description="Organization name. If present getting one or more Org(s).")
   *
   * @SWG\Parameter(
   *     name="ranked",
   *     in="query",
   *     type="boolean",
   *     description="Organization rank. If present getting only organization with rank more that 0 and customer cone is defined.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand information.")
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
  public function orgs(Request $request){
    $params = [];
    $params['user'] = $request->get("user");
    $params['name'] = $request->get("name");
    $params['ranked'] = $request->get("ranked");
    $params['verbose'] = $request->get("verbose");
    $params['sort'] = $request->get("sort");
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
      $result = $this->service->get_by_name($params);
    }else{
      $result = $this->service->get_all($params);
    }
    return $result;
  }

}