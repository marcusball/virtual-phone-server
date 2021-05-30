<?php
namespace VirtualPhone\API;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Slim\Routing\RouteParser;

class UrlBuilder {
    /** @var RequestInterface */
    private $request;

    /** @var RouteParser */
    private $parser;
    
    /** @var ContainerInterface */
    private $container;

    public function __construct(RouteParser $parser, ContainerInterface $container)
    {
        $this->parser = $parser;
        $this->container = $container;
    }

    public function setRequest(RequestInterface $request) {
        $this->request = $request;
    }

    public function fullUrlFor(string $routeName, array $data = [], array $queryParams = []): string {
        $overwriteBaseUrl = $this->container->get('settings')['overwrite_base_url'];

        if (empty($overwriteBaseUrl)) {
            return $this->parser->fullUrlFor($this->request->getUri(), $routeName, $data, $queryParams);
        }
        else {
            return rtrim($overwriteBaseUrl, '/') . $this->parser->relativeUrlFor($routeName, $data, $queryParams);
        }
    }
}