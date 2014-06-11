SolutionJsonRpcBundle
=====================

Symfony2 Bundle wrapper to [Zend JsonRpc Server][1]

Installation
------------

Add `solution/json-rpc-bundle` to `composer.json`:

```json
{
   "require": {
        "solution/json-rpc-bundle": "dev-master"
    }
}
```

Add `SolutionJsonRpcBundle` to your `AppKernel.php`:

```php
    $bundles = array(
        ...
        new Solution\JsonRpcBundle\SolutionJsonRpcBundle($this),
    );
}
```

Configuration
-------------

Select bundles you want use json-rpc via annotations so that the bundle will pick them up, and add them to your dependency injection container:
```yaml
#app/config/config.yml
solution_json_rpc:
    bundles: [ Bundle1, Bundle2 ]
```

Add route to your `routing.yml`:
```yaml
mailer_api:
    resource: @SolutionJsonRpcBundle/Resources/config/routing.yml
    prefix: /api/json-rpc/
```

Usage
-------------

Class annotation:

```php
use Solution\JsonRpcBundle\Annotation as Api;
/**
 * @Api\JsonRpcApi(service = "api", namespace="test-api")
 */
```

Method annotation:

```php
    /**
     * @Api\JsonRpcMethod
     *
     * @param null $id
     * @return bool
     */
    public function getTemplate($id = null)
    {
        return [
            "id" => 83,
            "name" => "test"
        ];
    }
```

Post request example:
```json
{"jsonrpc":"2.0","method":"test-api.getTemplate","params":[4],"id":"foo"}
```
Response example:
```json
{"result":[{"id":83,"name":"test"}],"id":"foo","jsonrpc":"2.0"}
```

@todo
- README
- Add the ability to describe methods with auto gen SDM
- write functional test

[1]: http://framework.zend.com/manual/1.10/en/zend.json.server.html