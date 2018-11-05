<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 24.08.18
 * Time: 14:06
 */

namespace App\Api2\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Api2\Helper\ReqUtils;
use App\Api2\Service\LocationService;

use Swagger\Annotations as SWG;


/**
 * @Route("/api/v2/locations")
 * @Route("/api/v2/locations/")
 */
class LocationController extends AbstractController{

  private $locationService;
  private $reqUtils;
  private $perpage;
  private $page;

  public function __construct(int $perpage, int $page, LocationService $locationService, ReqUtils $reqUtils){
    $this->locationService = $locationService;
    $this->perpage = $perpage;
    $this->page = $page;
    $this->reqUtils = $reqUtils;
  }

  /**
   * Return all locations.
   *
   * @Route("/", methods={"GET"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Returns all locations.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="string",
   *     description="Expand location information")
   *
   * @SWG\Parameter(
   *     name="page_size",
   *     in="query",
   *     type="string",
   *     description="Elements per page. By default = 500.")
   *
   * @SWG\Parameter(
   *     name="page_number",
   *     in="query",
   *     type="string",
   *     description="Pagination page number. By default = 1.")
   *
   * @param Request $request
   * @return bool|false|float|int|string
   */
  public function locations(Request $request) {
    $params = [];
    $params['sort'] = $request->get("sort");
    $params['start_date'] = $request->get("start_date");
    $params['end_date'] = $request->get("end_date");
    $params['verbose'] = $request->get("verbose");

    list($offset, $limit, $page) = $this->reqUtils->pagination_sanitize(
      $request->get("page_size"),
      $request->get("page_number"));

    $params['page_size'] = $limit;
    $params['page_number'] = $page;
    $params['limit'] = $limit;
    $params['offset'] = $offset;

    $data = $this->locationService->get_all($params);

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
    $result = $this->reqUtils->make_api2_response_data($params, $request);
    return $this->json($result);
  }

  /**
   * Return information about one specific location.
   *
   * @Route("/{id}", methods={"GET"}, requirements={"id"="[^.]+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return an location specified by location Id."
   * )
   *
   * @SWG\Parameter(
   *     name="id",
   *     in="path",
   *     type="string",
   *     description="An location Id.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand location information")
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
   * @param $id - Specific location name
   * @param Request $request
   * @return Response
   */
  public function location($id, Request $request){
    $params = [];
    $params['verbose'] = $request->get("verbose");

    list($offset, $limit, $page) = $this->reqUtils->pagination_sanitize(
      $request->get("page_size"),
      $request->get("page_number"));

    $params['page_size'] = $limit;
    $params['page_number'] = $page;
    $params['limit'] = $limit;
    $params['offset'] = $offset;

    $data = $this->locationService->get_location($id, $params['verbose']);

    $params['type'] = $data['type'];
    $params['status'] = $data['status'];
    $params['data'] = $data['data'];
    $params['total'] = $data['total'];
    $params['description'] = $data['description'];
    if(array_key_exists('error', $data)){
      $params['errors'] = $data['error'];
    }
    $result = $this->reqUtils->make_api2_response_data($params, $request);
    return $this->json($result);
  }

}