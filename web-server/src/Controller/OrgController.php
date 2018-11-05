<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



use App\Classes\Location;
use App\Service\OrgService;
use App\Classes\Org_Information;


class OrgController extends AbstractController{
    const PAGE_SIZE = 40;
    private $orgService;

  public function __construct(OrgService $service) {
    $this->orgService = $service;
  }

  /**
   * @Route("/orgs", name="orgs_ranking")
   * @Route("/orgs/ranked", name="orgs_ranked")
   * @param Request $request
   * @return Response
   */
  public function orgs(Request $request){
    $params = [];

    $params['page_number'] = $request->get("page_number") ? $request->get("page_number") : 1;
    $params['page_size'] =  $request->get("page_size") ? $request->get("page_size") : self::PAGE_SIZE;
    $params['ranked'] =  $request->get("ranked");
    $params['verbose'] =  $request->get("verbose");
    $params['sort'] = !is_null($request->get("sort")) ? $request->get("sort") : 'rank';
    $params['org'] = true;

    $params['page_number'] = $this->digit_santizer($params['page_number'], 1);
    return $this->render('asns/orgs.html.twig', ['params'=>$params]);
  }

  /**
   * @Route("/orgs/{org}", name="org_members")
   * @Route("/orgs/{org}/members")
   * @Route("/orgs/{org}/as-core", name="org_as_core", defaults={"area"="as-core"})
   * @param Request $request
   * @param string $org
   * @param string $area
   * @return Response
   */
    public function org(Request $request, $org="", $area="members"){
      $params = [];

      $params['page_number'] = $request->get("page_number") ? $request->get("page_number") : 1;
      $params['page_size'] = $request->get("page_size") ? $request->get("page_size") : self::PAGE_SIZE;
      $params['ranked'] = $request->get("ranked");
      $params['verbose'] = $request->get("verbose");
      $params['sort'] = !is_null($request->get("sort")) ? $request->get("sort") : 'rank';
      $params['org'] = true;

      $params['page_number'] = $this->digit_santizer($params['page_number'], 1);

      $org_info = new Org_Information($org);
      $org_info->GET_JSON($this->orgService, $params);

      $location = new Location("org", $area, $org_info);
      $params['location'] = $location;
      $params['org_info'] = $org_info;

      return $this->render('asns/org.html.twig', ['params' => $params]);
    }


    /**
     * @Route("/orgs/", name="orgs_search")
     * @Route("/orgs/by-name")
     * @Route("/orgs/by-name/")
     * @Route("/orgs/by-name/{name}")
     */
    public function org_search(Request $request, $name = NULL)
    {
        $page = $this->digit_santizer($request->query->get('page'), 1);
        if ($name == NULL) {
            $name = $request->query->get('name');
        }
        $type = $request->query->get('type');

        if ($type != NULL && strcmp($type,"go to") == 0) {
            return $this->org($request, $name);
        }

        return $this->render('asns/asn_search.html.twig', array(
            'name' => $name
            ,'page' => $page
            ,'page_size' => self::PAGE_SIZE
        ));
    }

    /*
     * Used to sanitize digits
     */
    private function digit_santizer($digit, $default=0)
    {
        if ($digit == NULL || !preg_match("/^\d+$/", $digit)) {
            $digit = $default;
        }
        return $digit;
    }

    private function GET_JSON($asn)
    {
        $json = file_get_contents(getenv('RESTFUL_DATABASE_URL').'/orgs/'.$asn.'?populate=1');

        if ($json != NULL)
        {
            $parsed = json_decode($json);
            if (property_exists($parsed,'data'))
            {
                $data = $parsed->{'data'};
                if (property_exists($data,'org') and property_exists($data->{'org'},'name')) 
                {
                    return $data->{'org'}->{'name'};
                }
                if (property_exists($data,'name'))
                {
                    return $data->{'name'};
                }
            }
        }
        return "";
    }

}
