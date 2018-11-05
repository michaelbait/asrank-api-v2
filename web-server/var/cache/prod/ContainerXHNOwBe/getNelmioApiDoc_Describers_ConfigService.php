<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'nelmio_api_doc.describers.config' shared service.

include_once $this->targetDirs[3].'/vendor/nelmio/api-doc-bundle/Describer/DescriberInterface.php';
include_once $this->targetDirs[3].'/vendor/nelmio/api-doc-bundle/Describer/ExternalDocDescriber.php';

return $this->privates['nelmio_api_doc.describers.config'] = new \Nelmio\ApiDocBundle\Describer\ExternalDocDescriber(array('schemes' => array(0 => 'http', 1 => 'https'), 'info' => array('title' => 'ARank API Doc', 'description' => 'Independent networks (Autonomous Systems, or ASes) engage in typically voluntary bilateral interconnection ("peering") agreements to provide reachability to each other for some subset of the Internet. <br />An AS\'s rank is based on it\'s customer cone size, which in turn is is inferred from BGP paths by CAIDA\'s AS relationships inference algorithm.', 'version' => '2.0.0')));