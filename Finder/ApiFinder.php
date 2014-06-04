<?php

namespace Solution\JsonRpcBundle\Finder;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ApiFinder
{
    const CLASS_ANNOTATION = 'JsonRpcApi';

    /** @var  Finder */
    protected $finder;

    /** @var  Finder */
    protected $reader;

    function __construct(Finder $finder, Reader $reader)
    {
        $this->finder = $finder;
        $this->reader = $reader;
    }

    /**
     * @param $dirs
     * @return array
     */
    public function findApiClasses($dirs)
    {
        $files = $this->finder
            ->in($dirs)
            ->name('*.php')
            ->contains(self::CLASS_ANNOTATION)
            ->ignoreDotFiles(true)
            ->files();

        $apiClasses = [];

        foreach ($files as $file) {
            # var_dump($file)
            /** @var SplFileInfo $file */
            $classname = $this->getClassName($file->getContents());
            if ($classname AND $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($classname), 'Solution\JsonRpcBundle\Annotation\JsonRpcApi')) {
                $apiClasses[$file->getPath()] = ['classname' =>$classname, 'annotation' => $annotation ] ;
            }
        }

        return $apiClasses;
    }


    private function getClassName($content)
    {
        if (!preg_match('/\bnamespace\s+([^;]+);/s', $content, $match)) {
            return null;
        }
        $namespace = $match[1];

        if (!preg_match('/\bclass\s+([^\s]+)\s+(?:extends|implements|{)/s', $content, $match)) {
            return null;
        }

        return $namespace . '\\' . $match[1];
    }
}
