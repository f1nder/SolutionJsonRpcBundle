<?php

namespace Solution\JsonRpcBundle\Controller;

use Solution\JsonRpcBundle\Server\RequestHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Zend\Json\Server\Smd;

class JsonRpcController
{
    /** @var \Solution\JsonRpcBundle\Server\RequestHandler */
    protected $handler;
    /** @var \Zend\Json\Server\Smd */
    protected $smd;

    public function __construct(RequestHandler $handler, Smd $smd)
    {
        $this->handler = $handler;
        $this->smd = $smd;
    }

    public function execute(Request $request)
    {
        if ($request->isMethod('POST')) {
            return $this->handler->handle($request);
        }

        return new JsonResponse($this->smd->toArray());
    }
}
