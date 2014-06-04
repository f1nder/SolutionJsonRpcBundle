<?php

namespace Solution\JsonRpcBundle;

use Solution\JsonRpcBundle\DependencyInjection\Compiler\AnnotationConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class SolutionJsonRpcBundle extends Bundle
{
    /** @var  KernelInterface */
    private $kernel;

    function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function build(ContainerBuilder $container)
    {
        $config = $container->getCompilerPassConfig();
        $passes = $config->getBeforeOptimizationPasses();
        array_unshift($passes, new AnnotationConfigurationPass($this->kernel));
        $config->setBeforeOptimizationPasses($passes);
    }

}
