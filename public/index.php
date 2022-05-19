<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require __DIR__ . '/../vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

// Add route callbacks
$app->get('/', function (Request $request, Response $response, array $args) use ($twig) {
    $body = $twig->render('home.twig.html');
    $response->getBody()->write($body);
    return $response;
});

$app->get('/login', function (Request $request, Response $response, array $args) use ($twig) {
    $body = $twig->render('login.twig.html');
    $response->getBody()->write($body);
    return $response;
});

$app->get('/siginup', function (Request $request, Response $response, array $args) use ($twig) {
    $body = $twig->render('siginup.twig.html');
    $response->getBody()->write($body);
    return $response;
});

$app->get('/{name}', function (Request $request, Response $response, array $args) use ($twig) {
    $name = $args['name'];
    $body = $twig->render('hello.twig.html', ['name' => $name]);
    $response->getBody()->write($body);
    return $response;
});

// Run application
$app->run();
