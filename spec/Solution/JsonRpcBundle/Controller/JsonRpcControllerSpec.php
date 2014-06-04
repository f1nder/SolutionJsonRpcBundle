<?php

namespace spec\Solution\JsonRpcBundle\Controller;

use Admin\DefaultBundle\Twig\TwigEnvirement;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Solution\JsonRpcBundle\Server\RequestHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zend\Json\Server\Server;
use Zend\Json\Server\Smd;

class JsonRpcControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Solution\JsonRpcBundle\Controller\JsonRpcController');
    }

    function let(RequestHandler $handler, Smd $smd)
    {
        $this->beConstructedWith($handler, $smd);
    }

    function it_should_not_handler_request_if_GET(RequestHandler $handler, Response $response, Request $request)
    {
        $request
            ->isMethod('POST')
            ->shouldBeCalled()
            ->willReturn(false);

        $handler
            ->handle($request)
            ->shouldNotBeCalled();

        $this->execute($request);
    }

    function it_should_handle_request_if_POST(RequestHandler $handler, Response $response, Request $request)
    {
        $request
            ->isMethod('POST')
            ->shouldBeCalled()
            ->willReturn(true);

        $handler
            ->handle($request)
            ->willReturn($response)
            ->shouldBeCalled();

        $this->execute($request);
    }
}
