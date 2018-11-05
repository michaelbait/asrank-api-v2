<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class srcProdProjectContainerUrlGenerator extends Symfony\Component\Routing\Generator\UrlGenerator
{
    private static $declaredRoutes;
    private $defaultLocale;

    public function __construct(RequestContext $context, LoggerInterface $logger = null, string $defaultLocale = null)
    {
        $this->context = $context;
        $this->logger = $logger;
        $this->defaultLocale = $defaultLocale;
        if (null === self::$declaredRoutes) {
            self::$declaredRoutes = array(
        'about' => array(array(), array('_controller' => 'App\\Controller\\About::about'), array(), array(array('text', '/about')), array(), array()),
        'as_core' => array(array('asn'), array('asn' => null, '_controller' => 'App\\Controller\\AsCoreController::asn_core'), array(), array(array('variable', '/', '[^/]++', 'asn'), array('text', '/as_core')), array(), array()),
        'app_ascore_asn_core' => array(array('asn', 'asn2'), array('asn' => null, 'asn2' => null, '_controller' => 'App\\Controller\\AsCoreController::asn_core'), array(), array(array('variable', '/', '[^/]++', 'asn2'), array('variable', '/', '[^/]++', 'asn'), array('text', '/as_core')), array(), array()),
        'app_ascore_asn_core_1' => array(array(), array('_controller' => 'App\\Controller\\AsCoreController::asn_core'), array(), array(array('text', '/as_core/')), array(), array()),
        'app_ascore_asn_core_2' => array(array(), array('_controller' => 'App\\Controller\\AsCoreController::asn_core'), array(), array(array('text', '/as_core')), array(), array()),
        'default' => array(array(), array('_controller' => 'App\\Controller\\AsnController::asns_top_ten'), array(), array(array('text', '/')), array(), array()),
        'asns_ranking' => array(array(), array('_controller' => 'App\\Controller\\AsnController::asns'), array(), array(array('text', '/asns')), array(), array()),
        'asn_neighbors' => array(array('asn'), array('asn' => '', '_controller' => 'App\\Controller\\AsnController::asn_neighbors'), array(), array(array('variable', '/', '[^/]++', 'asn'), array('text', '/asns')), array(), array()),
        'app_asn_asn_neighbors' => array(array('asn'), array('asn' => '', '_controller' => 'App\\Controller\\AsnController::asn_neighbors'), array(), array(array('text', '/neighbors'), array('variable', '/', '[^/]++', 'asn'), array('text', '/asns')), array(), array()),
        'asn_as_core' => array(array('asn'), array('area' => 'as-core', 'asn' => '', '_controller' => 'App\\Controller\\AsnController::asn_neighbors'), array(), array(array('text', '/as-core'), array('variable', '/', '[^/]++', 'asn'), array('text', '/asns')), array(), array()),
        'asn_search' => array(array(), array('_controller' => 'App\\Controller\\AsnController::asn_search'), array(), array(array('text', '/asns/')), array(), array()),
        'app_asn_asn_search' => array(array(), array('_controller' => 'App\\Controller\\AsnController::asn_search'), array(), array(array('text', '/asns/by-name')), array(), array()),
        'app_asn_asn_search_1' => array(array(), array('_controller' => 'App\\Controller\\AsnController::asn_search'), array(), array(array('text', '/asns/by-name/')), array(), array()),
        'app_asn_asn_search_2' => array(array('name'), array('name' => null, '_controller' => 'App\\Controller\\AsnController::asn_search'), array(), array(array('variable', '/', '[^/]++', 'name'), array('text', '/asns/by-name')), array(), array()),
        'contact' => array(array(), array('_controller' => 'App\\Controller\\Contact::about'), array(), array(array('text', '/contact')), array(), array()),
        'faq' => array(array(), array('_controller' => 'App\\Controller\\FAQ::about'), array(), array(array('text', '/faq')), array(), array()),
        'maintenance' => array(array(), array('_controller' => 'App\\Controller\\Maintenance::maintenance'), array(), array(array('text', '/maintenance')), array(), array()),
        'orgs_ranking' => array(array(), array('_controller' => 'App\\Controller\\OrgController::orgs'), array(), array(array('text', '/orgs')), array(), array()),
        'orgs_ranked' => array(array(), array('_controller' => 'App\\Controller\\OrgController::orgs'), array(), array(array('text', '/orgs/ranked')), array(), array()),
        'org_members' => array(array('org'), array('org' => '', '_controller' => 'App\\Controller\\OrgController::org'), array(), array(array('variable', '/', '[^/]++', 'org'), array('text', '/orgs')), array(), array()),
        'app_org_org' => array(array('org'), array('org' => '', '_controller' => 'App\\Controller\\OrgController::org'), array(), array(array('text', '/members'), array('variable', '/', '[^/]++', 'org'), array('text', '/orgs')), array(), array()),
        'org_as_core' => array(array('org'), array('area' => 'as-core', 'org' => '', '_controller' => 'App\\Controller\\OrgController::org'), array(), array(array('text', '/as-core'), array('variable', '/', '[^/]++', 'org'), array('text', '/orgs')), array(), array()),
        'orgs_search' => array(array(), array('_controller' => 'App\\Controller\\OrgController::org_search'), array(), array(array('text', '/orgs/')), array(), array()),
        'app_org_org_search' => array(array(), array('_controller' => 'App\\Controller\\OrgController::org_search'), array(), array(array('text', '/orgs/by-name')), array(), array()),
        'app_org_org_search_1' => array(array(), array('_controller' => 'App\\Controller\\OrgController::org_search'), array(), array(array('text', '/orgs/by-name/')), array(), array()),
        'app_org_org_search_2' => array(array('name'), array('name' => null, '_controller' => 'App\\Controller\\OrgController::org_search'), array(), array(array('variable', '/', '[^/]++', 'name'), array('text', '/orgs/by-name')), array(), array()),
        'app_rest_asnrest_asns' => array(array(), array('_controller' => 'App\\Controller\\Rest\\AsnRestController::asns'), array(), array(array('text', '/rest/asns/')), array(), array()),
        'app_rest_asnrest_asn_links' => array(array('id'), array('_controller' => 'App\\Controller\\Rest\\AsnRestController::asn_links'), array('id' => '[\\d]+'), array(array('text', '/links'), array('variable', '/', '[\\d]+', 'id'), array('text', '/rest/asns')), array(), array()),
        'app_rest_dsrest_datasets' => array(array(), array('_controller' => 'App\\Controller\\Rest\\DSRestController::datasets'), array(), array(array('text', '/rest/ds/')), array(), array()),
        'app_rest_orgrest_org' => array(array('id'), array('_controller' => 'App\\Controller\\Rest\\OrgRestController::org'), array(), array(array('variable', '/', '[^/]++', 'id'), array('text', '/rest/orgs')), array(), array()),
        'app_rest_orgrest_orgs' => array(array(), array('_controller' => 'App\\Controller\\Rest\\OrgRestController::orgs'), array(), array(array('text', '/rest/orgs/')), array(), array()),
        'app_rest_orgrest_org_members' => array(array('id'), array('_controller' => 'App\\Controller\\Rest\\OrgRestController::org_members'), array(), array(array('text', '/members'), array('variable', '/', '[^/]++', 'id'), array('text', '/rest/orgs')), array(), array()),
        'sitemap' => array(array(), array('_format' => 'xml', '_controller' => 'App\\Controller\\Sitemap::sitemap_sitemapindex'), array(), array(array('text', '/sitemap')), array(), array()),
        'sitemap2' => array(array(), array('_format' => 'xml', '_controller' => 'App\\Controller\\Sitemap::sitemap_sitemapindex'), array(), array(array('text', '/sitemap/')), array(), array()),
        'app_sitemap_sitemap_urlset' => array(array('type', 'page'), array('_format' => 'xml', '_controller' => 'App\\Controller\\Sitemap::sitemap_urlset'), array(), array(array('variable', '/', '[^/]++', 'page'), array('variable', '/', '[^/]++', 'type'), array('text', '/sitemap')), array(), array()),
        'app_sitemap_sitemap_urlset_1' => array(array('type'), array('_format' => 'xml', 'page' => 1, '_controller' => 'App\\Controller\\Sitemap::sitemap_urlset'), array(), array(array('variable', '/', '[^/]++', 'type'), array('text', '/sitemap')), array(), array()),
        'app_api2_asn_asns' => array(array(), array('_controller' => 'App\\Api2\\Controller\\AsnController::asns'), array(), array(array('text', '/api/v2/asns/')), array(), array()),
        'app_api2_asn_asn' => array(array('id'), array('_controller' => 'App\\Api2\\Controller\\AsnController::asn'), array('id' => '[\\d]+'), array(array('variable', '/', '[\\d]+', 'id'), array('text', '/api/v2/asns')), array(), array()),
        'app_api2_asn_asn_links' => array(array('id'), array('_controller' => 'App\\Api2\\Controller\\AsnController::asn_links'), array('id' => '[\\d]+'), array(array('text', '/links'), array('variable', '/', '[\\d]+', 'id'), array('text', '/api/v2/asns')), array(), array()),
        'app_api2_asn_asn_ranged_links' => array(array('id1', 'id2'), array('_controller' => 'App\\Api2\\Controller\\AsnController::asn_ranged_links'), array('id1' => '[\\d]+', 'id2' => '[\\d]+'), array(array('variable', '/', '[\\d]+', 'id2'), array('text', '/links'), array('variable', '/', '[\\d]+', 'id1'), array('text', '/api/v2/asns')), array(), array()),
        'app_api2_asn_asn_ranged_links_1' => array(array('id1', 'id2'), array('_controller' => 'App\\Api2\\Controller\\AsnController::asn_ranged_links'), array('id1' => '[\\d]+', 'id2' => '[\\d]+'), array(array('variable', '/', '[\\d]+', 'id2'), array('variable', '/', '[\\d]+', 'id1'), array('text', '/api/v2/asns/links')), array(), array()),
        'app_api2_dataset_datasets' => array(array(), array('_controller' => 'App\\Api2\\Controller\\DatasetController::datasets'), array(), array(array('text', '/api/v2/ds/')), array(), array()),
        'app_api2_dataset_dataset' => array(array('id'), array('_controller' => 'App\\Api2\\Controller\\DatasetController::dataset'), array('location' => '[\\d]+'), array(array('variable', '/', '[^/]++', 'id'), array('text', '/api/v2/ds')), array(), array()),
        'app_api2_location_locations' => array(array(), array('_controller' => 'App\\Api2\\Controller\\LocationController::locations'), array(), array(array('text', '/api/v2/locations/')), array(), array()),
        'app_api2_location_location' => array(array('id'), array('_controller' => 'App\\Api2\\Controller\\LocationController::location'), array('id' => '[^.]+'), array(array('variable', '/', '[^.]+', 'id'), array('text', '/api/v2/locations')), array(), array()),
        'app_api2_org_org_members_by_name' => array(array(), array('_controller' => 'App\\Api2\\Controller\\OrgController::org_members_by_name'), array(), array(array('text', '/api/v2/orgs/members')), array(), array()),
        'app_api2_org_org_members_by_id' => array(array('id'), array('_controller' => 'App\\Api2\\Controller\\OrgController::org_members_by_id'), array('id' => '(.)+'), array(array('text', '/members'), array('variable', '/', '(?:.)+', 'id'), array('text', '/api/v2/orgs')), array(), array()),
        'app_api2_org_between_orgs_neighbors' => array(array('name1', 'name2'), array('_controller' => 'App\\Api2\\Controller\\OrgController::between_orgs_neighbors'), array('name1' => '[^/]+', 'name2' => '[^/]+'), array(array('variable', '/', '[^/]+', 'name2'), array('text', '/neighbors'), array('variable', '/', '[^/]+', 'name1'), array('text', '/api/v2/orgs')), array(), array()),
        'app_api2_org_between_orgs_neighbors_1' => array(array('name1', 'name2'), array('_controller' => 'App\\Api2\\Controller\\OrgController::between_orgs_neighbors'), array('name1' => '[^/]+', 'name2' => '[^/]+'), array(array('variable', '/', '[^/]+', 'name2'), array('variable', '/', '[^/]+', 'name1'), array('text', '/api/v2/orgs/neighbors')), array(), array()),
        'app_api2_org_org_neighbors' => array(array('name'), array('_controller' => 'App\\Api2\\Controller\\OrgController::org_neighbors'), array('name' => '[^/]+'), array(array('text', '/neighbors'), array('variable', '/', '[^/]+', 'name'), array('text', '/api/v2/orgs')), array(), array()),
        'app_api2_org_org' => array(array('id'), array('_controller' => 'App\\Api2\\Controller\\OrgController::org'), array('id' => '[^/]+'), array(array('variable', '/', '[^/]+', 'id'), array('text', '/api/v2/orgs')), array(), array()),
        'app_api2_org_orgs' => array(array(), array('_controller' => 'App\\Api2\\Controller\\OrgController::orgs'), array(), array(array('text', '/api/v2/orgs/')), array(), array()),
        'app_api2_relation_ranged_links' => array(array('asn1', 'asn2'), array('_controller' => 'App\\Api2\\Controller\\RelationController::ranged_links'), array('asn1' => '[\\d]+', 'asn2' => '[\\d]+'), array(array('variable', '/', '[\\d]+', 'asn2'), array('variable', '/', '[\\d]+', 'asn1'), array('text', '/api/v2/links')), array(), array()),
        'app_api2_relation_link' => array(array('asn'), array('_controller' => 'App\\Api2\\Controller\\RelationController::link'), array('asn' => '[\\d]+'), array(array('variable', '/', '[\\d]+', 'asn'), array('text', '/api/v2/links')), array(), array()),
        'app_api2_relation_links' => array(array(), array('_controller' => 'App\\Api2\\Controller\\RelationController::links'), array(), array(array('text', '/api/v2/links/')), array(), array()),
        'app.swagger' => array(array(), array('_controller' => 'nelmio_api_doc.controller.swagger'), array(), array(array('text', '/api/doc.json')), array(), array()),
        'app.swagger_ui' => array(array(), array('_controller' => 'nelmio_api_doc.controller.swagger_ui'), array(), array(array('text', '/api/doc')), array(), array()),
    );
        }
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $locale = $parameters['_locale']
            ?? $this->context->getParameter('_locale')
            ?: $this->defaultLocale;

        if (null !== $locale && (self::$declaredRoutes[$name.'.'.$locale][1]['_canonical_route'] ?? null) === $name) {
            unset($parameters['_locale']);
            $name .= '.'.$locale;
        } elseif (!isset(self::$declaredRoutes[$name])) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        list($variables, $defaults, $requirements, $tokens, $hostTokens, $requiredSchemes) = self::$declaredRoutes[$name];

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }
}
