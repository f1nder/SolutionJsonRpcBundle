<?php

namespace Solution\JsonRpcBundle\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class JsonRpcApi
{
    public $namespace;

    public $service;

    public $desc;
}
