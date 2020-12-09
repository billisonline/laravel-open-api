# Laravel OpenAPI Generator

![](https://travis-ci.org/billisonline/laravel-open-api.svg?branch=master)

This library automatically generates an OpenAPI definition based on your app's routes, controllers, and more.

## Getting started

1. Install the package [from this Github repository](https://www.daggerhart.com/composer-how-to-use-git-repositories/) (not available on Packagist yet).

2. Create a new default definition:

```
mkdir -p openapi/main && touch openapi/main/definition.php
```

3. Add a route to the definition by adding the following code to `definition.php`:
```php
<?php

use App\Http\Controllers\UserController;
use BYanelli\OpenApiLaravel\Objects\OpenApiInfo;
use BYanelli\OpenApiLaravel\Objects\OpenApiOperation;

OpenApiInfo::make()->title('Some REST API')->version('1.0');

OpenApiOperation::fromAction([UserController::class, 'authenticate'])
    ->request([
        'username' => 'string',
        'password' => 'string',
    ]);
```

Once you have one or more operations in your definition file, you can use the console commands described below to work with your definition.

## Console commands

### Output definition

```
php artisan openapi:spec
```

Send the OpenAPI definition in JSON format to stdout.

### Generate documentation and client libraries

```
php artisan openapi:generate {generator} {output}

# {generator} = name of the generator to use
# {output} = output folder 
```

Generate documentation or client libraries based on the OpenAPI definition.  This command uses either [Redoc](https://github.com/Redocly/redoc) (for the "redoc" generator) or [OpenAPI Generator](https://github.com/OpenAPITools/openapi-generator) (for any other generator). 

### Serve HTML documentation

```
php artisan openapi:serve {--generator=} {--port=}

# {--generator=} = name of the generator to use; default is "redoc"
# {--port=} = port number to use for local Web server; default is 9000
```

Generate HTML documentation to temporary folder and server from local Web server (uses `php artisan serve` internally).

## Building a definition

### Operations

The main object types in an OpenAPI definition are [paths and operations](https://swagger.io/docs/specification/paths-and-operations/). A path is a URL or URL pattern, and an operation is an HTTP method handled by the server at that URL. Laravel routes correspond to operations; hence the OpenAPI library allows you to reference routes directly as operations, automatically bundling them together into paths when it builds the definition. 

Operations are easily created using the `fromAction` static method, which takes a Laravel "action" name in one of the standard formats, e.g., `'UserController@authenticate'` or `[UserController::class, 'authenticate']`.

The `request` method defines a request body for the operation, specified as an array in the format `['key name' => 'data type']`, where a data type can be `string`, `integer`, `float`, or `boolean`. The `response` method does the same for the response body.

Putting all this together we can define the following operation to create a user:
```php
OpenApiOperation::fromAction([UserController::class, 'store'])
    ->request([
        'name'      => 'string',
        'email'     => 'string',
        'password'  => 'string',
    ])
    ->response([
        'id'        => 'integer',
        'name'      => 'string',
        'email'     => 'string',
    ]);
```

## Working with Laravel objects

### Form requests

When creating an OpenAPI operation based on a Laravel controller action, the request type will be inferred from the form request type hint found in the controller method. For example, the following action will have a `UserRequest` type:

```php
class UserController 
{
    public function store(UserRequest $request) 
    {
        //
    }
}
```

The schema of a request cannot be inferred; instead it must be provided in the definition:

```php
FormRequestProperties::for(UserRequest::class)->schema([
    'name'      => 'string',
    'email'     => 'string',
    'password'  => 'string',
]);

// Now the UserController@store action (and any other actions using this request) will have the expected request body
OpenApiOperation::fromAction([UserController::class, 'store']);
```

### JSON resources

When specifying an operation's response schema, a [JSON resource](https://laravel.com/docs/8.x/eloquent-resources) class may be used. The OpenAPI library will attempt to infer the property names from the resource's `toArray` method, and their types using PhpDoc `@property` tags of the associated model class. Consider the following scenario.

User model:
```php
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 * @property string email
 */
class User extends Model
{
    //
}
```

User resource:
```php
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,        
        ];
    }
}
```

When we reference the JSON resource in a definition, the names and types of the `id`, `name`, and `email` properties will be inferred.

```php
OpenApiOperation::fromAction([UserController::class, 'show'])
    ->response(UserResource::class);
```

#### Plural responses

To specify that a response returns an array of resources, use the `plural` helper:

```php
OpenApiOperation::fromAction([UserController::class, 'index'])
    ->response(UserResource::class)
    ->plural();
```

#### Overriding the inferred schema

If the schema can't be inferred correctly from the JSON resource, it can be overridden:

```php
JsonResourceProperties::for(StripeCustomerResource::class)->schema([
    'id'        => 'string',
    'sources[]' => [
        'brand'     => 'string',
        'expMonth'  => 'integer',
        'expYear'   => 'integer',
        'last4'     => 'string',
    ],
]);
```

If the model is guessed incorrectly, it can be specified manually:

```php
JsonResourceProperties::for(UserResource::class)->model(User::class);
```

## Authorization

### Bearer token

To apply bearer token auth to a set of operations, use the `OpenApiAuth::bearerToken()` method. Pass a closure to it and build the operation objects inside the closure.

```php
OpenApiAuth::bearerToken(function () {
    OpenApiOperation::fromAction([UserController::class, 'destroy']);
});
```

### Coming soon

Support for Laravel Sanctum and other auth types coming soon.

## Misc

### Response wrappers

Some APIs will wrap every JSON response's data in a key such as `data` or `content`. This can be expressed using a `ResponseSchemaWrapper`, i.e., a class implementing `\BYanelli\OpenApiLaravel\Objects\ResponseSchemaWrapper`. An implementation called `KeyedResponseSchemaWrapper` is provided for the simple case where the key is always the same. To apply the wrapper to the whole definition:

```php
<?php

use BYanelli\OpenApiLaravel\Objects\OpenApiInfo;
use BYanelli\OpenApiLaravel\Objects\OpenApiDefinition;
use BYanelli\OpenApiLaravel\Objects\KeyedResponseSchemaWrapper;

OpenApiInfo::make()->title('Test API')->version('1.0');

OpenApiDefinition::current()->responseSchemaWrapper(new KeyedResponseSchemaWrapper('data'));
```

## How to test

`composer test` or `./vendor/bin/phpunit`
