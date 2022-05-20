<?php
use App\DataBase;
use App\Session;
use App\Authorization;
use App\AuthorizationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
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

$session = new Session();
$sessionMiddleware = function (Request $request, Handler $handler) use ($session) {
    $session->start();
    $response = $handler->handle($request);
    $session->save();
    return $response;
};
$app->add($sessionMiddleware);

$config = include_once __DIR__ . '/../config/database.php';
$database = new DataBase($config['dsn'], $config['username'], $config['password']);

$authorization = new Authorization($database);

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

$app->get('/siginup', function (Request $request, Response $response, array $args) use ($twig, $session) {
    $body = $twig->render('siginup.twig.html', [
        'message' => $session->flush('message'),
        'form' => $session->flush('form')
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->post('/siginup', function (Request $request, Response $response) use ($authorization, $session) {

    //fetch from $_POST
    $params = (array)$request->getParsedBody();
    //var_dump($params);

    try {
        $authorization->siginup($params);
    }catch (AuthorizationException $exception){
        $session->setData('message', $exception->getMessage());
        $session->setData('form', $params);
        return $response->withHeader('Location', '/siginup')->withStatus(302);
    }
    return $response->withHeader('Location', '/')->withStatus(302);

});

$app->get('/{name}', function (Request $request, Response $response, array $args) use ($twig) {
    $name = $args['name'];
    $body = $twig->render('hello.twig.html', ['name' => $name]);
    $response->getBody()->write($body);
    return $response;
});

// Run application
$app->run();
