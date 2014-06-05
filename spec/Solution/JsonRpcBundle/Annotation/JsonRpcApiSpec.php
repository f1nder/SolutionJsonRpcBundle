<?php

namespace spec\Solution\JsonRpcBundle\Annotation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JsonRpcApiSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Solution\JsonRpcBundle\Annotation\JsonRpcApi');
    }

    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_should_validate_params()
    {
        $args = [];
        $this->shouldThrow('\InvalidArgumentException')->during('__construct', [$args]);
    }

    function it_should_pass_validate_params()
    {
        $args = ['service' => 'test.service'];
        $this->shouldNotThrow('\InvalidArgumentException')->during('__construct', [$args]);
    }
}
