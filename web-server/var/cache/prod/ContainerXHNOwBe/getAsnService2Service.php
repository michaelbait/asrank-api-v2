<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'App\Service\AsnService' shared autowired service.

include_once $this->targetDirs[3].'/src/Service/AsnService.php';

return $this->privates['App\Service\AsnService'] = new \App\Service\AsnService('127.0.0.1', 8086, 'asrankw', 'asrankuser', 'rankas', 'autogen', ($this->privates['App\Helper\ReqUtils'] ?? $this->load('getReqUtils2Service.php')), ($this->privates['monolog.logger'] ?? $this->getMonolog_LoggerService()));