<?php

namespace spec\Solution\JsonRpcBundle\Server;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zend\Json\Server\Server;

class RequestHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Solution\JsonRpcBundle\Server\RequestHandler');
    }

    function let(Server $server)
    {
        $this->beConstructedWith($server);
    }

    function it_server_must_be_mutable(Server $server)
    {
        $this->getServer()->shouldReturn($server);
    }

    function it_must_handle_sf2_request(Server $server, Request $request, \Zend\Json\Server\Response $response)
    {
        $response
            ->toJson()
            ->willReturn(json_encode(['test' => 1]))
            ->shouldBeCalled();

        $server
            ->setReturnResponse(true)
            ->shouldBeCalled();

        $server
            ->handle(Argument::type('Zend\Json\Server\Request'))
            ->willReturn($response)
            ->shouldBeCalled();

        /** @var Response $handledResponse */
        $handledResponse = $this->handle($request);
        $handledResponse->shouldHaveType('Symfony\Component\HttpFoundation\Response');
        $handledResponse->getStatusCode()->shouldReturn(200);
        $handledResponse->headers->contains('Content-Type', 'application/json')->shouldReturn(true);
        $handledResponse->getContent()->shouldReturn('{"test":1}');
    }
}
