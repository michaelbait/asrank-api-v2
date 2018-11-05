<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 12.09.18
 * Time: 12:11
 */

namespace App\Api2\Controller;

use App\Api2\Helper\ReqUtils;
use App\Api2\Service\RelationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/v2/links")
 * @Route("/api/v2/links/");
 */
class RelationController extends AbstractController {

  private $service;
  private $reqUtils;
  private $perpage;
  private $page;
  private $logger;

  public function __construct(int $perpage, int $page,
                              RelationService $service,
                              ReqUtils $reqUtils,
                              LoggerInterface $logger){
    $this->service = $service;
    $this->perpage = $perpage;
    $this->page = $page;
    $this->reqUtils = $reqUtils;
    $this->logger = $logger;
  }

  /**
   * Return links (realtions) between two ASNs.
   *
   * @Route("/{asn1}/{asn2}", methods={"GET"}, requirements={"asn1"="[\d]+", "asn2"="[\d]+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Return links between two asns."
   * )
   * @SWG\Parameter(
   *     name="asn1",
   *     in="path",
   *     type="string",
   *     description="Expand asn information.")
   *
   * @SWG\Parameter(
   *     name="asn2",
   *     in="path",
   *     type="string",
   *     description="Expand asn information.")
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
   * @param $asn1
   * @param $asn2
   * @param Request $request
   * @return Response
   */
  public function ranged_links($asn1, $asn2, Request $request){
    $params = [];
    $params['user'] = $request->get("user");
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

    $data = $this->service->get_ranged($asn1, $asn2, $params);

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

  /**
   * Return links for an Asn scpecified by Id/
   *
   * @Route("/{asn}", methods={"GET"}, requirements={"asn":"[\d]+"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Returns all links for specified Asn.")
   *
   * @SWG\Parameter(
   *     name="asn",
   *     in="path",
   *     type="string",
   *     description="An Asn id.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand link information.")
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
   * @param $asn        - Asn Id.
   * @param Request $request
   * @return bool|false|float|int|string
   */
  public function link($asn, Request $request){
    $params = [];
    $params['verbose'] = $request->get("verbose");
    $params['user'] = $request->get("user");
    $params['start_date'] = $request->get("start_date");
    $params['end_date'] = $request->get("end_date");

    list($offset, $limit, $page) = $this->reqUtils->pagination_sanitize(
      $request->get("page_size"),
      $request->get("page_number"));

    $params['page_size'] = $limit;
    $params['page_number'] = $page;
    $params['limit'] = $limit;
    $params['offset'] = $offset;

    $data = $this->service->get_by_asn($asn, $params);

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

  /**
   * Return all links (relations).
   *
   * @Route("/", methods={"GET"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Returns all links.")
   *
   * @SWG\Parameter(
   *     name="verbose",
   *     in="query",
   *     type="boolean",
   *     description="Expand link information.")
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
  public function links(Request $request) {
    $params = [];
    $params['user'] = $request->get("user");
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

    $data = $this->service->get_all($params);

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


}