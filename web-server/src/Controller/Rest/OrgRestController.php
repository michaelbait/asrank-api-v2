<?php
/**
 * Created by PhpStorm.
 * User: baitaluk
 * Date: 05.09.18
 * Time: 12:47
 */

namespace App\Controller\Rest;

use App\Service\AsnService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Service\OrgService;
use App\Helper\ReqUtils;


class OrgRestController extends AbstractController {

  private $orgService;
  private $asnService;
  private $reqUtils;

  public function __construct(OrgService $orgService,
                              AsnService $asnService,
                              ReqUtils $reqUtils){
    $this->asnService = $asnService;
    $this->orgService = $orgService;
    $this->reqUtils = $reqUtils;
  }

  /**
   *
   * @Route("/rest/orgs/{id}", methods={"GET"})
   *
   * @param $id
   * @param Request $request
   * @return Response
   */
  public function org($id, Request $request){
    $params = [];
    $params['user'] = $request->get("user");
    $params['ranked'] = $request->get("ranked");
    $params['verbose'] = $request->get("verbose");
    $params['start_date'] = $request->get("start_date");
    $params['end_date'] = $request->get("end_date");
    $params['sort'] = !is_null($request->get("sort")) ? $request->get("sort") : 'rank';

    list($offset, $limit, $page) = $this->reqUtils->pagination_sanitize(
      $request->get("page_size"),
      $request->get("page_number"));

    $params['page_size'] = $limit;
    $params['page_number'] = $page;
    $params['limit'] = $limit;
    $params['offset'] = $offset;

    $data = $this->orgService->get_by_id($id, $params);

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
   * @Route("/rest/orgs/", methods={"GET"})
   *
   * @param Request $request
   * @param bool $top_ten
   * @return Response
   *
   */
  public function orgs(Request $request){
    $params = [];
    $params['user'] = $request->get("user");
    $params['name'] = $request->get("name");
    $params['ranked'] = $request->get("ranked");
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
   * @Route("/rest/orgs/{id}/members", methods={"GET"})
   *
   * @param $id
   * @param Request $request
   * @return Response
   */
  public function org_members($id, Request $request){
    $params = [];
    $params['user'] = $request->get("user");
    $params['name'] = $request->get("name");
    $params['ranked'] = $request->get("ranked");
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

    $data = $this->asnService->get_by_org($id, $params);

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

  private function params_based_data_retriever($params) {
    if (!is_null($params['name'])) {
      $result = $this->orgService->get_by_name($params);
    }else{
      $result = $this->orgService->get_all($params);
    }
    return $result;
  }

}