<?php
namespace VirtualPhone\Middleware;

use VirtualPhone\API\UrlBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class UrlBuilderMiddleware implements MiddlewareInterface
{
    /**
     * @var UrlBuilder 
     */
    private $urlBuilder ;

    public function __construct(UrlBuilder $urlBuilder)
    {
        $this->urlBuilder= $urlBuilder;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->urlBuilder->setRequest($request);

        return $handler->handle($request);
    }
}

?>