<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'App\Service\RelationService' shared autowired service.

include_once $this->targetDirs[3].'/src/Service/RelationService.php';

return $this->privates['App\Service\RelationService'] = new \App\Service\RelationService('127.0.0.1', 8086, 'asrankw', 'asrankuser', 'rankas', 'autogen', ($this->privates['App\Helper\ReqUtils'] ?? $this->load('getReqUtils2Service.php')), ($this->privates['App\Service\AsnService'] ?? $this->load('getAsnService2Service.php')), ($this->privates['monolog.logger'] ?? $this->getMonolog_LoggerService()));