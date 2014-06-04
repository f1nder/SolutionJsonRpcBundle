<?php

namespace Solution\JsonRpcBundle\DependencyInjection\Compiler;

use Solution\JsonRpcBundle\Annotation\JsonRpcApi;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\KernelInterface;
use Zend\Json\Server\Smd;

class AnnotationConfigurationPass implements CompilerPassInterface
{
    /** @var  \AppKernel */
    protected $kernel;

    function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $reader = $container->get('annotation_reader');

        $apiFinder = $container->get('solution_json_rpc.api_finder');
        $directories = $this->getScanDirectories($container);
        if (!$directories) {
            $container->getCompiler()->addLogMessage('No directories configured for AnnotationConfigurationPass.');

            return;
        }

        $annotationClasses = $apiFinder->findApiClasses($directories);
        foreach ($annotationClasses as $file => $class) {
            $annotation = $class['annotation'];
            $classname = $class['classname'];

            $container->addResource(new FileResource($file));

            /** @var JsonRpcApi $annotation */
            $refApiClass = new \ReflectionClass($classname);
            $methods = $refApiClass->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if ($methodAnnotation = $reader->getMethodAnnotation($method, 'Solution\JsonRpcBundle\Annotation\JsonRpcMethod')) {

                    $container
                        ->getDefinition('solution_json_rpc.server')
                        ->addMethodCall(
                            'addFunction',
                            [
                                [new Reference($annotation->service), $method->getName()],
                                $annotation->namespace
                            ]
                        );
                }
            }
        }
    }

    private function getScanDirectories(ContainerBuilder $c)
    {
        $bundles = $this->kernel->getBundles();
        $scanBundles = $c->getParameter('solution_json_rpc.bundles');

        $directories = [];
        foreach ($bundles as $bundle) {
            if (!in_array($bundle->getName(), $scanBundles, true)) {
                continue;
            }

            $directories[] = $bundle->getPath();
        }

        return $directories;
    }
}
