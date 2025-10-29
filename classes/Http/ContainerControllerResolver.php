<?php

namespace mod_assessment\Http;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpFoundation\Request;

class ContainerControllerResolver implements ControllerResolverInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function getController(Request $request): callable|false
    {
        $controllerSpec = $request->attributes->get('_controller');
        if (!$controllerSpec) {
            return false;
        }
        [$class, $method] = explode('::', $controllerSpec);
        $controller = new $class($this->container);

        return [$controller, $method];
    }
}
