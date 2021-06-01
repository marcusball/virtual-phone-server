<?php

use Psr\Container\ContainerInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Lcobucci\JWT\Configuration as JwtConfiguration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Slim\Routing\RouteParser;
use VirtualPhone\API\UrlBuilder;
use VirtualPhone\Middleware\UrlBuilderMiddleware;
use VirtualPhone\API\JwtAuth;
use Psr\Http\Message\ResponseFactoryInterface;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    ErrorMiddleware::class => function (ContainerInterface $container, Logger $logger) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['error'];

        return new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details'],
            $logger
        );
    },

    UrlBuilder::class => function(RouteParser $parser, ContainerInterface $container) {
        return new UrlBuilder($parser, $container);
    },

    UrlBuilderMiddleware::class => function (UrlBuilder $urlBuilder) {
        return new UrlBuilderMiddleware($urlBuilder);
    },

    RouteParser::class => function (App $app) {
        return $app->getRouteCollector()->getRouteParser();
    },

    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];

        $driver = $settings['driver'];
        $host = $settings['host'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['charset'];
        $flags = $settings['flags'];

        $charsetStr = '';
        if ($driver === 'pgsql') { $charsetStr = "options='--client_encoding=$charset'"; }
        else if ($driver === 'mysql') { $charsetStr = "charset=$charset"; }

        $dsn = "$driver:host=$host;dbname=$dbname;$charsetStr";

        return new PDO($dsn, $username, $password, $flags);
    },

    Monolog\Logger::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['logger'];

        $logger = new Logger($settings['name']);
        $streamHandler = new StreamHandler('php://stderr', 100);
        $logger->pushHandler($streamHandler);

        return $logger;
    },

    Twilio\Rest\Client::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['twilio'];

        $client = new Twilio\Rest\Client(
            $settings['sid'],
            $settings['token']
        );

        return $client;
    },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },

    JwtAuth::class => function (ContainerInterface $container) {
        $configuration = $container->get(JwtConfiguration::class);

        $jwtSettings = $container->get('settings')['jwt'];
        $issuer = (string)$jwtSettings['issuer'];
        $lifetime = (int)$jwtSettings['lifetime'];

        return new JwtAuth($configuration, $issuer, $lifetime);
    },
    
    JwtConfiguration::class => function (ContainerInterface $container) {
        $jwtSettings = $container->get('settings')['jwt'];
        $privateKey = (string)$jwtSettings['private_key'];
        $publicKey = (string)$jwtSettings['public_key'];

        // Asymmetric algorithms use a private key for signature creation
        // and a public key for verification
        return JwtConfiguration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText($privateKey),
            InMemory::plainText($publicKey)
        );
    },
];