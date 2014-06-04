<?php

namespace spec\Solution\JsonRpcBundle\DependencyInjection\Compiler {

    use Doctrine\Common\Annotations\Reader;
    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;
    use Solution\JsonRpcBundle\Annotation\JsonRpcApi;
    use Solution\JsonRpcBundle\Annotation\JsonRpcMethod;
    use Solution\JsonRpcBundle\Finder\ApiFinder;
    use Symfony\Component\Config\Resource\FileResource;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Definition;
    use Symfony\Component\DependencyInjection\Reference;
    use Symfony\Component\HttpKernel\Bundle\BundleInterface;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Zend\Json\Server\Server;

    class AnnotationConfigurationPassSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->shouldHaveType('Solution\JsonRpcBundle\DependencyInjection\Compiler\AnnotationConfigurationPass');
            $this->shouldHaveType('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
        }

        function let(KernelInterface $kernel, BundleInterface $bundle1, BundleInterface $bundle2)
        {
            $bundle1->getName()->willReturn('Bundle1');
            $bundle1->getPath()->willReturn('/path1');
            $bundle2->getName()->willReturn('Bundle2');
            $bundle2->getPath()->willReturn('/path2');

            $kernel
                ->getBundles()
                ->willReturn([$bundle1, $bundle2]);

            $this->beConstructedWith($kernel);
        }


        function it_should_scan_configured_bundles(KernelInterface $kernel, ContainerBuilder $cb, Reader $reader, ApiFinder $finder, Server $server, Definition $definition)
        {
            $annotation = new JsonRpcApi(['service' => 'test.service']);
            $annotation->service = 'test_service';
            $annotation->namespace = 'testnamespace';

            $methodAnnotation = new JsonRpcMethod;
            $methodAnnotation->name = 'foo_name';

            $definition->addMethodCall(
                'addFunction',
                Argument::that(
                    function ($args) {
                        return ($args[0][0] instanceof Reference AND $args[0][0] == 'test_service' AND $args[0][1] == 'foo' AND $args[1] == 'testnamespace');
                    }
                )

            )->shouldBeCalled();

            $cb
                ->getDefinition('solution_json_rpc.server')
                ->willReturn($definition)
                ->shouldBeCalled();

            $cb
                ->addResource(Argument::type('\Symfony\Component\Config\Resource\FileResource'))
                ->shouldBeCalled();

            $reader
                ->getMethodAnnotation(Argument::which('getName', 'foo'), 'Solution\JsonRpcBundle\Annotation\JsonRpcMethod')
                ->willReturn($methodAnnotation)
                ->shouldBeCalled();

            $finder
                ->findApiClasses(['/path2'])
                ->willReturn(['file.php' => ['classname' => 'TestJsonRpc\Service', 'annotation' => $annotation]]);

            $cb->get('solution_json_rpc.api_finder')->willReturn($finder);
            $cb->get('annotation_reader')->willReturn($reader);
            $cb->getParameter('solution_json_rpc.bundles')->willReturn(['Bundle2']);

            $this->process($cb);
        }
    }

}

namespace TestJsonRpc {
    class Service
    {
        public function foo()
        {
        }
    }
}