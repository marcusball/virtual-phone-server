<?php
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;
use GuzzleHttp\Psr7\Response;
use Slim\Factory\AppFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';

/***************************************************/
/** Set up Slim App                               **/
/***************************************************/

// Create a PHP DI Container
$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

/***************************************************/
/** Set up Dependencies                           **/
/***************************************************/

// Create the logger dependency
$container->set('logger', function () {
    $logger = new Logger('app');
    $streamHandler = new StreamHandler('php://stderr', 100);
    $logger->pushHandler($streamHandler);

    return $logger;
});

try {
	$dbopts = parse_url(getenv('DATABASE_URL'));

	$pdo = new PDO(sprintf('pgsql:host=%s;dbname=%s;options=\'--client_encoding=UTF8\'', $dbopts["host"], ltrim($dbopts["path"],'/')) , $dbopts['user'], $dbopts['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
} catch (Exception $e) {
	$container->get('logger')->emergency('Error connecting to database! ' . $e->getMessage());
    http_response_code(503);
    exit;
}

$container->set('db', $pdo);

/**
 * Add Error Middleware
 *
 * @param bool                  $displayErrorDetails -> Should be set to false in production
 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                  $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger  
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true, $container->get('logger'));


$app->get('/', function (IRequest $request, IResponse $response, $args) {
    $response->getBody()->write("Hello world!");
    $this->get('logger')->info('testing new logger');
    return $response;
});

$app->get('/test', function (IRequest $request, IResponse $response, $args) {
    $response->getBody()->write("Hello!");
    return $response;
});

$app->get('/info', function (IRequest $request, IResponse $response, $args) {
    phpinfo();
    return $response;
});

$app->get('/db', function (IRequest $request, IResponse $response, $args) {
    $stmt = $this->get('db')->prepare('SELECT \'database says hello\' AS msg');
    $stmt->execute();

    $stmt->setFetchMode(\PDO::FETCH_COLUMN, 0);

    $response->getBody()->write($stmt->fetch());
    return $response;
});

$app->run();
