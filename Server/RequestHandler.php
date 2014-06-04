<?php

namespace Solution\JsonRpcBundle\Server;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zend\Json\Server\Server;

class RequestHandler
{
    /** @var  Server */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        $jsonRequest = new \Zend\Json\Server\Request();
        $jsonRequest->loadJson($request->getContent(false));

        $this->server->setReturnResponse(true);

        $rpcResponse = $this->server->handle($jsonRequest);
        $response = new Response($rpcResponse->toJson(), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function getServer()
    {
        return $this->server;
    }
}
