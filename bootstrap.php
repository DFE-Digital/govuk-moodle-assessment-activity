<?php

// Symfony autoloader
require_once(__DIR__ . '/vendor/autoload.php');

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use mod_assessment\Twig\SwpdpExtension;
use mod_assessment\Util\EntityManagerProvider;
use mod_assessment\Validator\ValidAssessmentRecordItemDtoValidator;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Translation\Loader\ArrayLoader; 
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

// Create ContainerBuilder
$container = new ContainerBuilder();

// Doctrine connection
$connection = DriverManager::getConnection([
    'driver' => 'pdo_pgsql',
    'host' => $CFG->dbhost,
    'user' => $CFG->dbuser,
    'password' => $CFG->dbpass,
    'dbname' => $CFG->dbname,
]);

// Doctrine configuration
$doctrinePaths = [__DIR__ . '/doctrine'];
$isDevMode = true;
$config = ORMSetup::createXMLMetadataConfiguration(
    $doctrinePaths,
    $isDevMode,
);
// Lower snake-case field names (eg class property createdAt > database column created_at)
$config->setNamingStrategy(new UnderscoreNamingStrategy());

// Create and register entity manager with the container
$entityManager = new EntityManager($connection, $config);
$container->set('doctrine.entity_manager', $entityManager);

// Create and register session with the container
$storage = new NativeSessionStorage();
$session = new Session($storage);
$container->set('session', $session);

// Create and register form factory with the container 
$validator = Validation::createValidatorBuilder()
    ->addYamlMapping(__DIR__ . '/config/validation.yaml')
    ->getValidator();    
$formFactory = Forms::createFormFactoryBuilder()
    ->addExtension(new HttpFoundationExtension())
    ->addExtension(new CsrfExtension(new CsrfTokenManager()))
    ->addExtension(new ValidatorExtension($validator))
    ->getFormFactory();
$container->set('form.factory', $formFactory);

// Create Twig with strictness (best practice)
$twigLoader = new FilesystemLoader(__DIR__ . '/templates');
// Add joshualicense/govuk-frontend-twig
$twigLoader->addPath(__DIR__ . '/vendor/joshualicense/govuk-frontend-twig/src/templates', 'govuk-frontend-twig');
$twig = new Environment($twigLoader, [
    'debug' => true,
    'strict_variables' => true,
]);
// Add the form and SWPDP Twig extensions
$twig->addExtension(new FormExtension());
$twig->addExtension(new SwpdpExtension());
// Add Translator Twig extension (needed for form labels and errors)
$translator = new Translator('en');
$translator->addLoader('array', new ArrayLoader());
$twig->addExtension(new TranslationExtension($translator));

// Form renderer engine
$formEngine = new TwigRendererEngine(['form_div_layout.html.twig'], $twig);
$twig->addRuntimeLoader(new FactoryRuntimeLoader([
    FormRenderer::class => fn() => new FormRenderer($formEngine),
]));

// Register Twig with the container
$container->set('twig', $twig);

// Create and register router with the container
$request = Request::createFromGlobals();
$context = new RequestContext();
$context->fromRequest($request);
$context->setBaseUrl('/mod/assessment/index.php');
$routes = require __DIR__ . '/config/routes.php';
$urlGenerator = new UrlGenerator($routes, $context);
$container->set('router', $urlGenerator);
// Add the Routing Twig extension
$twig->addExtension(new RoutingExtension($urlGenerator));

// Provide a helper to access EntityManager
EntityManagerProvider::setEntityManager($entityManager);

$container->compile();

return $container;
