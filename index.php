<?php

// Moodle
require(__DIR__.'/../../config.php');

// Symfony
use mod_assessment\Http\ContainerControllerResolver;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$container = require_once(__DIR__ . '/bootstrap.php');

$request = Request::createFromGlobals();

$routes = require __DIR__ . '/config/routes.php';
$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

$baseResolver = new ControllerResolver();
$controllerResolver = new ContainerControllerResolver($container);
$argumentResolver = new ArgumentResolver();

try {
    $request->attributes->add($matcher->match($request->getPathInfo()));
    $controller = $controllerResolver->getController($request);
    $arguments = $argumentResolver->getArguments($request, $controller);
    $response = call_user_func_array($controller, $arguments);

    if ($response instanceof Response && !$response instanceof RedirectResponse ) {
        // Wrap the response HTML in the Moodle header and footer
        $assessment = $controller[0]->getAssessment();
        $courseModule = get_coursemodule_from_instance('assessment', $assessment->getId(), $assessment->getCourse());
        require_login($assessment->getCourse(), true, $courseModule);

        $pageurl = new moodle_url($request->getRequestUri());
        $PAGE->set_url($pageurl);

        $response->setContent($OUTPUT->header() . $response->getContent() . $OUTPUT->footer());
    }
} catch (ResourceNotFoundException) {
    $response = new Response('Not Found', 404);
} catch (Exception $e) {
    $response = new Response('Error: ' . $e->getMessage(), 500);
}

$response->send();
