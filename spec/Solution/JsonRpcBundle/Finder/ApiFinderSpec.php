<?php

namespace spec\Solution\JsonRpcBundle\Finder {

    use Doctrine\Common\Annotations\Reader;
    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;
    use Prophecy\Doubler\Doubler;
    use Solution\JsonRpcBundle\Annotation\JsonRpcApi;
    use Symfony\Component\Finder\Finder;
    use Symfony\Component\Finder\SplFileInfo;
    use Prophecy\Doubler\ClassPatch;

    class ApiFinderSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->shouldHaveType('Solution\JsonRpcBundle\Finder\ApiFinder');
        }

        function let(Finder $finder, Reader $reader)
        {
            $this->beConstructedWith($finder, $reader);
        }

        function it_should_find_all_api_classes(Finder $finder, Reader $reader)
        {
            $apiAnnotation = new JsonRpcApi(['service'=> 'test.service']);

            $reader
                ->getClassAnnotation(Argument::which('getName', 'TestNamespace1\TestClass1'), 'Solution\JsonRpcBundle\Annotation\JsonRpcApi')
                ->willReturn($apiAnnotation)
                ->shouldBeCalled();

            $reader
                ->getClassAnnotation(Argument::which('getName', 'TestNamespace2\TestClass2'), 'Solution\JsonRpcBundle\Annotation\JsonRpcApi')
                ->willReturn(null)
                ->shouldBeCalled();

            $file1 = $this->getSplFileInfo();
            $file1->getContents()->willReturn("<?php namespace TestNamespace1;  \n class TestClass1 { }");
            $file1->getPath()->willReturn("/path1.php");

            $file2 = $this->getSplFileInfo();
            $file2->getContents()->willReturn("<?php namespace TestNamespace2; \n  class TestClass2 { }");
            $file2->getPath()->willReturn("/path2.php");

            $finder
                ->in(['dir1', 'dir2'])
                ->willReturn($finder)
                ->shouldBeCalled();

            $finder
                ->name('*.php')
                ->willReturn($finder)
                ->shouldBeCalled();

            $finder
                ->contains('JsonRpcApi')
                ->willReturn($finder)
                ->shouldBeCalled();

            $finder
                ->ignoreDotFiles(true)
                ->willReturn($finder)
                ->shouldBeCalled();

            $finder
                ->files()
                ->willReturn([$file1, $file2])
                ->shouldBeCalled();

            $this->findApiClasses(['dir1', 'dir2'])->shouldReturn(['/path1.php' => ['classname' => 'TestNamespace1\TestClass1', 'annotation' => $apiAnnotation]]);
        }


        /**
         * @return SplFileInfo
         */
        private function getSplFileInfo()
        {
            $doubler = new Doubler();
            $doubler->registerClassPatch(new ClassPatch\TraversablePatch);
            $doubler->registerClassPatch(new ClassPatch\DisableConstructorPatch);
            $doubler->registerClassPatch(new ClassPatch\ProphecySubjectPatch);
            $doubler->registerClassPatch(new ClassPatch\KeywordPatch);
            $prophet = new \Prophecy\Prophet($doubler);

            return $prophet->prophesize('Symfony\Component\Finder\SplFileInfo');
        }
    }
}

namespace TestNamespace1 {
    class TestClass1
    {
    }
}

namespace TestNamespace2 {
    class TestClass2
    {
    }
}
