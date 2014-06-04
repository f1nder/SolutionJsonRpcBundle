<?php

namespace Solution\JsonRpcBundle\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class JsonRpcMethod
{
    public $name;

    public $desc;
}
