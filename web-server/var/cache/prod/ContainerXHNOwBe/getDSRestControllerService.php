<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'App\Controller\Rest\DSRestController' shared autowired service.

include_once $this->targetDirs[3].'/src/Service/DatasetService.php';
include_once $this->targetDirs[3].'/vendor/symfony/framework-bundle/Controller/ControllerTrait.php';
include_once $this->targetDirs[3].'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
include_once $this->targetDirs[3].'/src/Controller/Rest/DSRestController.php';

$a = ($this->privates['App\Helper\ReqUtils'] ?? $this->load('getReqUtils2Service.php'));

$this->services['App\Controller\Rest\DSRestController'] = $instance = new \App\Controller\Rest\DSRestController(new \App\Service\DatasetService('127.0.0.1', 8086, 'asrankw', 'asrankuser', 'rankas', 'autogen', $a, ($this->privates['monolog.logger'] ?? $this->getMonolog_LoggerService())), $a);

$instance->setContainer(($this->privates['.service_locator.ychIlgF'] ?? $this->load('get_ServiceLocator_YchIlgFService.php'))->withContext('App\\Controller\\Rest\\DSRestController', $this));

return $instance;