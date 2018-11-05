<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class srcProdProjectContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = $allowSchemes = array();
        if ($ret = $this->doMatch($pathinfo, $allow, $allowSchemes)) {
            return $ret;
        }
        if ($allow) {
            throw new MethodNotAllowedException(array_keys($allow));
        }
        if (!in_array($this->context->getMethod(), array('HEAD', 'GET'), true)) {
            // no-op
        } elseif ($allowSchemes) {
            redirect_scheme:
            $scheme = $this->context->getScheme();
            $this->context->setScheme(key($allowSchemes));
            try {
                if ($ret = $this->doMatch($pathinfo)) {
                    return $this->redirect($pathinfo, $ret['_route'], $this->context->getScheme()) + $ret;
                }
            } finally {
                $this->context->setScheme($scheme);
            }
        } elseif ('/' !== $pathinfo) {
            $pathinfo = '/' !== $pathinfo[-1] ? $pathinfo.'/' : substr($pathinfo, 0, -1);
            if ($ret = $this->doMatch($pathinfo, $allow, $allowSchemes)) {
                return $this->redirect($pathinfo, $ret['_route']) + $ret;
            }
            if ($allowSchemes) {
                goto redirect_scheme;
            }
        }

        throw new ResourceNotFoundException();
    }

    private function doMatch(string $rawPathinfo, array &$allow = array(), array &$allowSchemes = array()): ?array
    {
        $allow = $allowSchemes = array();
        $pathinfo = rawurldecode($rawPathinfo);
        $context = $this->context;
        $requestMethod = $canonicalMethod = $context->getMethod();

        if ('HEAD' === $requestMethod) {
            $canonicalMethod = 'GET';
        }

        switch ($pathinfo) {
            default:
                $routes = array(
                    '/about' => array(array('_route' => 'about', '_controller' => 'App\\Controller\\About::about'), null, null, null),
                    '/as_core/' => array(array('_route' => 'app_ascore_asn_core_1', '_controller' => 'App\\Controller\\AsCoreController::asn_core'), null, null, null),
                    '/' => array(array('_route' => 'default', '_controller' => 'App\\Controller\\AsnController::asns_top_ten'), null, null, null),
                    '/asns' => array(array('_route' => 'asns_ranking', '_controller' => 'App\\Controller\\AsnController::asns'), null, array('GET' => 0), null),
                    '/asns/' => array(array('_route' => 'asn_search', '_controller' => 'App\\Controller\\AsnController::asn_search'), null, null, null),
                    '/asns/by-name/' => array(array('_route' => 'app_asn_asn_search_1', '_controller' => 'App\\Controller\\AsnController::asn_search'), null, null, null),
                    '/contact' => array(array('_route' => 'contact', '_controller' => 'App\\Controller\\Contact::about'), null, null, null),
                    '/faq' => array(array('_route' => 'faq', '_controller' => 'App\\Controller\\FAQ::about'), null, null, null),
                    '/maintenance' => array(array('_route' => 'maintenance', '_controller' => 'App\\Controller\\Maintenance::maintenance'), null, null, null),
                    '/orgs' => array(array('_route' => 'orgs_ranking', '_controller' => 'App\\Controller\\OrgController::orgs'), null, null, null),
                    '/orgs/ranked' => array(array('_route' => 'orgs_ranked', '_controller' => 'App\\Controller\\OrgController::orgs'), null, null, null),
                    '/orgs/' => array(array('_route' => 'orgs_search', '_controller' => 'App\\Controller\\OrgController::org_search'), null, null, null),
                    '/orgs/by-name/' => array(array('_route' => 'app_org_org_search_1', '_controller' => 'App\\Controller\\OrgController::org_search'), null, null, null),
                    '/rest/asns/' => array(array('_route' => 'app_rest_asnrest_asns', '_controller' => 'App\\Controller\\Rest\\AsnRestController::asns'), null, array('GET' => 0), null),
                    '/rest/ds/' => array(array('_route' => 'app_rest_dsrest_datasets', '_controller' => 'App\\Controller\\Rest\\DSRestController::datasets'), null, array('GET' => 0), null),
                    '/rest/orgs/' => array(array('_route' => 'app_rest_orgrest_orgs', '_controller' => 'App\\Controller\\Rest\\OrgRestController::orgs'), null, array('GET' => 0), null),
                    '/sitemap' => array(array('_route' => 'sitemap', '_format' => 'xml', '_controller' => 'App\\Controller\\Sitemap::sitemap_sitemapindex'), null, null, null),
                    '/sitemap/' => array(array('_route' => 'sitemap2', '_format' => 'xml', '_controller' => 'App\\Controller\\Sitemap::sitemap_sitemapindex'), null, null, null),
                    '/api/v2/asns/' => array(array('_route' => 'app_api2_asn_asns', '_controller' => 'App\\Api2\\Controller\\AsnController::asns'), null, array('GET' => 0), null),
                    '/api/v2/ds/' => array(array('_route' => 'app_api2_dataset_datasets', '_controller' => 'App\\Api2\\Controller\\DatasetController::datasets'), null, array('GET' => 0), null),
                    '/api/v2/locations/' => array(array('_route' => 'app_api2_location_locations', '_controller' => 'App\\Api2\\Controller\\LocationController::locations'), null, array('GET' => 0), null),
                    '/api/v2/orgs/members' => array(array('_route' => 'app_api2_org_org_members_by_name', '_controller' => 'App\\Api2\\Controller\\OrgController::org_members_by_name'), null, array('GET' => 0), null),
                    '/api/v2/orgs/' => array(array('_route' => 'app_api2_org_orgs', '_controller' => 'App\\Api2\\Controller\\OrgController::orgs'), null, array('GET' => 0), null),
                    '/api/v2/links/' => array(array('_route' => 'app_api2_relation_links', '_controller' => 'App\\Api2\\Controller\\RelationController::links'), null, array('GET' => 0), null),
                    '/api/doc.json' => array(array('_route' => 'app.swagger', '_controller' => 'nelmio_api_doc.controller.swagger'), null, array('GET' => 0), null),
                    '/api/doc' => array(array('_route' => 'app.swagger_ui', '_controller' => 'nelmio_api_doc.controller.swagger_ui'), null, array('GET' => 0), null),
                );

                if (!isset($routes[$pathinfo])) {
                    break;
                }
                list($ret, $requiredHost, $requiredMethods, $requiredSchemes) = $routes[$pathinfo];

                $hasRequiredScheme = !$requiredSchemes || isset($requiredSchemes[$context->getScheme()]);
                if ($requiredMethods && !isset($requiredMethods[$canonicalMethod]) && !isset($requiredMethods[$requestMethod])) {
                    if ($hasRequiredScheme) {
                        $allow += $requiredMethods;
                    }
                    break;
                }
                if (!$hasRequiredScheme) {
                    $allowSchemes += $requiredSchemes;
                    break;
                }

                return $ret;
        }

        $matchedPathinfo = $pathinfo;
        $regexList = array(
            0 => '{^(?'
                    .'|/a(?'
                        .'|s(?'
                            .'|_core(?'
                                .'|(?:/([^/]++))?(*:38)'
                                .'|(?:/([^/]++)(?:/([^/]++))?)?(*:73)'
                                .'|(*:80)'
                            .')'
                            .'|ns(?'
                                .'|(?:/([^/]++))?(*:107)'
                                .'|/(?'
                                    .'|([^/]++)/(?'
                                        .'|neighbors(*:140)'
                                        .'|as\\-core(*:156)'
                                    .')'
                                    .'|by\\-name(?'
                                        .'|(*:176)'
                                        .'|(?:/([^/]++))?(*:198)'
                                    .')'
                                .')'
                            .')'
                        .')'
                        .'|pi/v2/(?'
                            .'|asns/(?'
                                .'|([\\d]+)(*:234)'
                                .'|([\\d]+)/links(*:255)'
                                .'|([\\d]+)/links/([\\d]+)(*:284)'
                                .'|links/([\\d]+)/([\\d]+)(*:313)'
                            .')'
                            .'|ds/([^/]++)(*:333)'
                            .'|l(?'
                                .'|ocations/([^.]+)(*:361)'
                                .'|inks/(?'
                                    .'|([\\d]+)/([\\d]+)(*:392)'
                                    .'|([\\d]+)(*:407)'
                                .')'
                            .')'
                            .'|orgs/(?'
                                .'|((?:.)+)/members(*:441)'
                                .'|([^/]+)/neighbors/([^/]+)(*:474)'
                                .'|neighbors/([^/]+)/([^/]+)(*:507)'
                                .'|([^/]+)/neighbors(*:532)'
                                .'|([^/]+)(*:547)'
                            .')'
                        .')'
                    .')'
                    .'|/orgs(?'
                        .'|(?:/([^/]++))?(*:580)'
                        .'|/(?'
                            .'|([^/]++)/(?'
                                .'|members(*:611)'
                                .'|as\\-core(*:627)'
                            .')'
                            .'|by\\-name(?'
                                .'|(*:647)'
                                .'|(?:/([^/]++))?(*:669)'
                            .')'
                        .')'
                    .')'
                    .'|/rest/(?'
                        .'|asns/([\\d]+)/links(*:707)'
                        .'|orgs/([^/]++)(?'
                            .'|(*:731)'
                            .'|/members(*:747)'
                        .')'
                    .')'
                    .'|/sitemap/([^/]++)(?'
                        .'|/([^/]++)(*:786)'
                        .'|(*:794)'
                    .')'
                .')$}sD',
        );

        foreach ($regexList as $offset => $regex) {
            while (preg_match($regex, $matchedPathinfo, $matches)) {
                switch ($m = (int) $matches['MARK']) {
                    default:
                        $routes = array(
                            38 => array(array('_route' => 'as_core', 'asn' => null, '_controller' => 'App\\Controller\\AsCoreController::asn_core'), array('asn'), null, null),
                            73 => array(array('_route' => 'app_ascore_asn_core', 'asn' => null, 'asn2' => null, '_controller' => 'App\\Controller\\AsCoreController::asn_core'), array('asn', 'asn2'), null, null),
                            80 => array(array('_route' => 'app_ascore_asn_core_2', '_controller' => 'App\\Controller\\AsCoreController::asn_core'), array(), null, null),
                            107 => array(array('_route' => 'asn_neighbors', 'asn' => '', '_controller' => 'App\\Controller\\AsnController::asn_neighbors'), array('asn'), null, null),
                            140 => array(array('_route' => 'app_asn_asn_neighbors', 'asn' => '', '_controller' => 'App\\Controller\\AsnController::asn_neighbors'), array('asn'), null, null),
                            156 => array(array('_route' => 'asn_as_core', 'area' => 'as-core', 'asn' => '', '_controller' => 'App\\Controller\\AsnController::asn_neighbors'), array('asn'), null, null),
                            176 => array(array('_route' => 'app_asn_asn_search', '_controller' => 'App\\Controller\\AsnController::asn_search'), array(), null, null),
                            198 => array(array('_route' => 'app_asn_asn_search_2', 'name' => null, '_controller' => 'App\\Controller\\AsnController::asn_search'), array('name'), null, null),
                            234 => array(array('_route' => 'app_api2_asn_asn', '_controller' => 'App\\Api2\\Controller\\AsnController::asn'), array('id'), array('GET' => 0), null),
                            255 => array(array('_route' => 'app_api2_asn_asn_links', '_controller' => 'App\\Api2\\Controller\\AsnController::asn_links'), array('id'), array('GET' => 0), null),
                            284 => array(array('_route' => 'app_api2_asn_asn_ranged_links', '_controller' => 'App\\Api2\\Controller\\AsnController::asn_ranged_links'), array('id1', 'id2'), array('GET' => 0), null),
                            313 => array(array('_route' => 'app_api2_asn_asn_ranged_links_1', '_controller' => 'App\\Api2\\Controller\\AsnController::asn_ranged_links'), array('id1', 'id2'), array('GET' => 0), null),
                            333 => array(array('_route' => 'app_api2_dataset_dataset', '_controller' => 'App\\Api2\\Controller\\DatasetController::dataset'), array('id'), array('GET' => 0), null),
                            361 => array(array('_route' => 'app_api2_location_location', '_controller' => 'App\\Api2\\Controller\\LocationController::location'), array('id'), array('GET' => 0), null),
                            392 => array(array('_route' => 'app_api2_relation_ranged_links', '_controller' => 'App\\Api2\\Controller\\RelationController::ranged_links'), array('asn1', 'asn2'), array('GET' => 0), null),
                            407 => array(array('_route' => 'app_api2_relation_link', '_controller' => 'App\\Api2\\Controller\\RelationController::link'), array('asn'), array('GET' => 0), null),
                            441 => array(array('_route' => 'app_api2_org_org_members_by_id', '_controller' => 'App\\Api2\\Controller\\OrgController::org_members_by_id'), array('id'), array('GET' => 0), null),
                            474 => array(array('_route' => 'app_api2_org_between_orgs_neighbors', '_controller' => 'App\\Api2\\Controller\\OrgController::between_orgs_neighbors'), array('name1', 'name2'), array('GET' => 0), null),
                            507 => array(array('_route' => 'app_api2_org_between_orgs_neighbors_1', '_controller' => 'App\\Api2\\Controller\\OrgController::between_orgs_neighbors'), array('name1', 'name2'), array('GET' => 0), null),
                            532 => array(array('_route' => 'app_api2_org_org_neighbors', '_controller' => 'App\\Api2\\Controller\\OrgController::org_neighbors'), array('name'), array('GET' => 0), null),
                            547 => array(array('_route' => 'app_api2_org_org', '_controller' => 'App\\Api2\\Controller\\OrgController::org'), array('id'), array('GET' => 0), null),
                            580 => array(array('_route' => 'org_members', 'org' => '', '_controller' => 'App\\Controller\\OrgController::org'), array('org'), null, null),
                            611 => array(array('_route' => 'app_org_org', 'org' => '', '_controller' => 'App\\Controller\\OrgController::org'), array('org'), null, null),
                            627 => array(array('_route' => 'org_as_core', 'area' => 'as-core', 'org' => '', '_controller' => 'App\\Controller\\OrgController::org'), array('org'), null, null),
                            647 => array(array('_route' => 'app_org_org_search', '_controller' => 'App\\Controller\\OrgController::org_search'), array(), null, null),
                            669 => array(array('_route' => 'app_org_org_search_2', 'name' => null, '_controller' => 'App\\Controller\\OrgController::org_search'), array('name'), null, null),
                            707 => array(array('_route' => 'app_rest_asnrest_asn_links', '_controller' => 'App\\Controller\\Rest\\AsnRestController::asn_links'), array('id'), array('GET' => 0), null),
                            731 => array(array('_route' => 'app_rest_orgrest_org', '_controller' => 'App\\Controller\\Rest\\OrgRestController::org'), array('id'), array('GET' => 0), null),
                            747 => array(array('_route' => 'app_rest_orgrest_org_members', '_controller' => 'App\\Controller\\Rest\\OrgRestController::org_members'), array('id'), array('GET' => 0), null),
                            786 => array(array('_route' => 'app_sitemap_sitemap_urlset', '_format' => 'xml', '_controller' => 'App\\Controller\\Sitemap::sitemap_urlset'), array('type', 'page'), null, null),
                            794 => array(array('_route' => 'app_sitemap_sitemap_urlset_1', '_format' => 'xml', 'page' => 1, '_controller' => 'App\\Controller\\Sitemap::sitemap_urlset'), array('type'), null, null),
                        );

                        list($ret, $vars, $requiredMethods, $requiredSchemes) = $routes[$m];

                        foreach ($vars as $i => $v) {
                            if (isset($matches[1 + $i])) {
                                $ret[$v] = $matches[1 + $i];
                            }
                        }

                        $hasRequiredScheme = !$requiredSchemes || isset($requiredSchemes[$context->getScheme()]);
                        if ($requiredMethods && !isset($requiredMethods[$canonicalMethod]) && !isset($requiredMethods[$requestMethod])) {
                            if ($hasRequiredScheme) {
                                $allow += $requiredMethods;
                            }
                            break;
                        }
                        if (!$hasRequiredScheme) {
                            $allowSchemes += $requiredSchemes;
                            break;
                        }

                        return $ret;
                }

                if (794 === $m) {
                    break;
                }
                $regex = substr_replace($regex, 'F', $m - $offset, 1 + strlen($m));
                $offset += strlen($m);
            }
        }
        if ('/' === $pathinfo && !$allow && !$allowSchemes) {
            throw new Symfony\Component\Routing\Exception\NoConfigurationException();
        }

        return null;
    }
}
