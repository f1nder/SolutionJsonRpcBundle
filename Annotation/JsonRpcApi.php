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

    function __construct(array $values)
    {
        if (isset($values['service'])) {
            $this->service = $values['service'];
        } else {
            throw new \InvalidArgumentException('You must define a "service" attribute for each JsonRpcApi annotation.');
        }

        if(isset($values['namespace'])) {
            $this->namespace = $values['namespace'];
        }

        if(isset($values['desc'])) {
            $this->desc = $values['desc'];
        }
    }
}
