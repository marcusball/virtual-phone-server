<?php 
namespace VirtualPhone\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use VirtualPhone\API\APIResponse;
use VirtualPhone\Domain\Thread\Service\ThreadQueryService;

class ThreadsListAction {

    /** @var ThreadQueryService */
    private $service;

    public function __construct(ThreadQueryService $service) {
        $this->service = $service;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $personId = $request->getAttribute('personId');

        $threads = $this->service->getAllForPerson($personId);

        return APIResponse::success($response, $threads ?? [], 'threads')->into();
    }
}