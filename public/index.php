<?php
use Slim\Factory\AppFactory;
use App\Controllers\NewsController;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../bootstrap/dependencies.php';
AppFactory::setContainer($container);
$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', [NewsController::class, 'index']);

$app->run();
