<div align="center">

<!--lint ignore no-dead-urls-->
# PHP REST Service

PHP REST Service is a simple and fast PHP class for RESTful JSON APIs.

[![Build Status](https://img.shields.io/circleci/build/github/cdgco/php-rest-service?style=flat-square)](https://app.circleci.com/pipelines/github/cdgco/php-rest-service)
[![PHP Version](https://img.shields.io/packagist/php-v/cdgco/php-rest-service?style=flat-square)](https://www.php.net/downloads)
[![Package Version](https://img.shields.io/packagist/v/cdgco/php-rest-service?style=flat-square)](https://packagist.org/packages/cdgco/php-rest-service)
[![License](https://img.shields.io/github/license/cdgco/php-rest-service?style=flat-square)](https://github.com/cdgco/php-rest-service/blob/master/LICENSE)
</div>

## Features

+ Easy to use syntax
+ Regular Expression support
+ Error handling through PHP Exceptions
+ JSON, XML, and plain text responses
+ Automatic OpenAPI specification generation
+ Parameter validation through PHP function signature
+ Can return a summary of all routes or one route through `OPTIONS` method based on PHPDoc (if `OPTIONS` is not overridden)
+ Support of `GET`, `POST`, `PUT`, `DELETE`, `PATCH`, `HEAD` and `OPTIONS`
+ Suppress the HTTP status code with `?_suppress_status_code=1` (for clients that have troubles with that)
+ Supports `?_method=<httpMethod>` as addition to the actual HTTP method.
+ Supports custom error handling, logging, access control and response formatting functions.
+ With auto-generation through PHP's `reflection`

## About

PHP REST Service is a lightweight API framework for PHP. It is very easy to learn, use, and integrate with existing PHP projects. 

This package is a fork of marcj/php-rest-service with the following changes:
* Support for PHP 7.4+
* Support for plain text responses
* Support for custom response formats
* Support for pre-response control functions
* Automatic OpenAPI specification generation
* Single file structure
* Complete documentation and examples

Why build a new package? I wanted an express-like API router for PHP but couldn't find anything that fit my needs. marcj/php-rest-service is a great framework but it's deprecated and missing modern PHP support, documentation, and features that I needed.

## Installation

### Requirements

PHP REST Service requires PHP 7.4+. There are no dependecies.

### Composer

Run `php composer require cdgco/php-rest-service`, then include the Composer autoloader with `include 'vendor/autoload.php';`.

### Manual

Copy `Server.php` to your directory and include with `include 'Server.php';`.

## Web Server Configuration

PHP REST Service acts as a single page application, so all requests must be sent to the index.php file (or the file you want to serve).

For example, on apache, you can add the following lines to your `.htaccess` file which will redirect all requests for directories or non-existant files to index.php:

```apacheconf
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (.+) index.php/$1 [L,QSA]
```

## Basic Usage

### Manual Endpoint Creation

With manual endpoint creation, you must define a new route for each endpoint you want to expose.
This is done by using the [`addGetRoute()`](phpdoc/#serveraddgetroute), [`addPostRoute()`](phpdoc/#serveraddpostroute), [`addPutRoute()`](phpdoc/#serveraddputroute),[`addDeleteRoute()`](phpdoc/#serveradddeleteroute), [`addPatchRoute()`](phpdoc/#serveraddpatchroute), [`addHeadRoute()`](phpdoc/#serveraddheadroute), [`addOptionsRoute()`](phpdoc/#serveraddoptionsroute) and [`addRoute()`](phpdoc/#serveraddroute) methods. 

All route methods accept a path and a callback (e.g. `addGetRoute('path', 'callbackFunction')`), while the `addRoute()` method also accepts the HTTP method (e.g. `addRoute('path', 'callbackFunction', 'get')`). If no method is specified, the default method is `_all_`.

Each of the aforementioned methods will add a route for the specified HTTP method, with the exception of `addRoute()` which will add a route for all HTTP methods.

```php
use RestService\Server;

Server::create('/')
  ->addGetRoute('test', function(){
    return 'Yay!';
  })
  ->addPostRoute('foo', function($field1) {
    return 'Hello ' . $field1; // same as "return 'Hello ' . $_POST('field1');"
  })
  >addGetRoute('use/this/name', function(){
      return 'Hi there';
  })
->run();

```

### Auto Endpoint Creation

With automatic endpoint creation, you can supply a PHP class that contains endpoint functions. By calling the [`collectRoutes()`](phpdoc/#servercollectroutes) method, the router will then scan the class for functions that start with `get`, `post`, `put`, `delete`, `patch`, `head` or `options` and add the corresponding route.

Function names will be converted from camel case to dashes,  so `getFooBar()` will be converted to `/foo-bar`.

You can also bind a function to a route other than the function name. Simply use the `@url` annotation to define the route.
For example `@url /foo` will bind the function to the route `/foo`.

```php
namespace MyRestApi;

use RestService\Server;

class Admin {
  public function getTest(){
    return 'Yay!';
  }
  public function postFoo($field1){
    return 'Hello ' . $field1; // same as "return 'Hello ' . $_POST('field1');"
  }
  /*
   * @url /use/this/name
   */
  public function getNotThisName($field1){
    return 'Hi there';
  }
}

Server::create('/', 'myRestApi\Admin')
    ->collectRoutes()
->run();
```

Both methods will generate the following endpoints:
```
+ GET  /test
+ POST /foo
+ GET  /use/this/name
```

## Advanced Usage

### Regex in Paths

For more advanced routing, you can use regular expressions in the path. Either through the `path` argument in the [`addRoute()`](phpdoc/#serveraddroute) method, or the `@url` annotation in the function comment.

For example, in order to match all routes that start with `/foo`, you can use the following:

```php
->addGetRoute('foo/.*', 'getFoo')
```

or 

```php
/**
 * @url foo/.*
 */
public function getFoo(){
  return 'Yay!';
}
```

!> Note that the API server wraps the path / url argument in `^` and `$` to match the entire path, with the delimiter `|`.

### URL Parameters

To capture parameters from the URL for use as function arguments, simply use regex capture groups in the `path` argument of the [`addRoute()`](phpdoc/#serveraddroute) method, or the `@url` annotation in the function comment.

For example, in order to match the `:id` and `:action` parameters in the following URL, `/foo/:id/something/:action`, where `:id` is a int and `:action` is a string, you can use the following:

```php
->addPostRoute('foo/(\d+)/something/(\w+)', 'postFoo')
```

or 

```php
/**
 * @url foo/(\d+)/something/(\w+)
 */
public function postFoo($arg1, $arg2){
  return 'Yay!';
}
```

The API server will automatically bind the captured parameters to the first `n` arguments of the function where `n` is the number of capture groups in the regex pattern.

You can still use GET, POST, and PUT parameters in the endpoint, but they must be bound to variables after the capture groups.

For example, in order to add a POST parameter `content` to the endpoint, you can use the following:

```php
/**
 * @url foo/(\d+)/something/(\w+)
 */
public function postFoo($arg1, $arg2, $content){
  return 'Yay!';
}
```

### Access Control

In order to restrict access to endpoints, you can use the [`setCheckAccess()`](phpdoc/#serversetcheckaccess) method to supply an access control function.

The access control function will be called with the following arguments:

`function($url, $route, $method, $arguments)`

In order to deny access, simply throw an exception inside of the function.

For example, in order to deny POST access to the `/foo` endpoint if the `X-API-KEY` header is not set, use the following:

```php
->setCheckAccess(function($url, $route, $method, $args) {
    if ($method == "post" && $route == "foo" && !isset($_SERVER['HTTP_X_API_KEY'])) {
        throw new \Exception("Access Denied", 401);
    }
})
```

### Logging

You can use the [`setCheckAccess()`](phpdoc/#serversetcheckaccess) method for any sort of pre-response logic you wish to execute, such as logging:

```php
->setCheckAccess(function($url, $route, $method, $args) {
    $log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
    "URL: ".$url.PHP_EOL.
    "Method: ".$method.PHP_EOL.
    "Route: ".$route.PHP_EOL.
    "Args: ".json_encode($args).PHP_EOL.
    "---------------------------------------------".PHP_EOL;
    file_put_contents('log_'.date("j.n.Y").'.log', $log,  FILE_APPEND | LOCK_EX);
})
```

### Rate Limiting

[`setCheckAccess()`](phpdoc/#serversetcheckaccess) could also be used as a rate limiter:

```php
->setCheckAccess(function($url, $route, $method, $args) {
    $redis = new \Redis();
    $redis->connect('localhost');

    $key = "api_".$_SERVER['REMOTE_ADDR'];
    $limit = 10;
    $time = 60;
    
    $count = $redis->get($key);
    if ($count >= $limit) {
        throw new \Exception("Rate Limit Exceeded", 429);
    }
    $redis->incr($key);
    $redis->expire($key, $time);
})
```

!> Note: You may only assign one checkAccess function per controller, so if you want to execute multiple pre-response actions, you must combine them into one function.

### Response Formatting

#### Built-in Response Formatters

You can use the [`setFormat()`](phpdoc/#clientsetformat) method of the API client to specify the response format.
Available formats are `json`, `xml`, and `text`.

In order to set the format, you must first access the current client using the [`getClient()`](phpdoc/#servergetclient) method.
You may only set the format on the top level controller, as each sub controller shares the same client with the parent.

If you would like to have different formats for different endpoints, you must create a new server using the [`create()`](phpdoc/#servercreate) method, rather than creating a new controller.

```php
Server::create('test')
    ->getClient()
        ->setFormat('text')
    ->getController()
    ->addGetRoute('', function($test) {
        return "Hello ". $test;
    })
->run();
```

#### Custom Response Formatters

You can also define your own response formatters using the [`setCustomFormat()`](phpdoc/#clientsetcustomformat) method along with the [`setFormat()`](phpdoc/#clientsetformat) method. The formatting function will be called with a single argument, an associative array of the response data.

For example:
```
[
    'status' => 'success',
    'data' => 'Hello World!'
]
```

The formatter function should set the response header and return a string.

The following example shows an adapted implementation of the plain text formatter:

```php
Server::create('test')
    ->getClient()
        ->setCustomFormat(function($message) {
            if (php_sapi_name() !== 'cli' )
                header('Content-Type: text/plain; charset=utf-8');

            $text = '';
            foreach ($message as $key => $data) {
                $key = is_numeric($key) ? '' : $key.': ';
                $text .= $key.$data."\n";
            }
            return $text;
        })
        ->setFormat('custom')
    ->getController()
    ->addGetRoute('', function($test) {
        return "Hello ". $test;
    })
->run();
```

### OpenAPI Specification

PHP REST Service can automatically generate an OpenAPI specification file for your API.

To enable OpenAPI generation, simply use the [`setApiSpec()`](phpdoc/#serversetapispec) method on the desired controller, passing the name, version, description (optional), base path (optional), and whether or not to include child controllers (optional).

```php
Server::create('test')
    -->setApiSpec("My New API", "1.2.3", "This is my new API", "https://example.com/api", true)
    ->addGetRoute('', function($test) {
        return "Hello ". $test;
    })
```

This will generate an OpenAPI specification in JSON format, accessible at the `/spec` GET endpoint of your API, including all endpoints in child controllers.

!> Note: If there is another endpoint named `/spec`, it will take priority over the OpenAPI specification.

Request and response types and descriptions will be pulled from the comment blocks of your endpoint functions.
If no comment annotations are detected, the request / respone types will default to `AnyValue`.

If your URL contains regex, REST API Service will attempt to scan your string and replace any regex capture groups with the corresponding argument name, and delete any non-capture groups, however, it is unable to process regex commands outside of capture groups. In order to specify the correct URL that will be used in the specification, you can provide the `@openapiurl` annotation on your endpoint function.

For example, say you already have an endpoint at `/foo/(\d+)` and you want to add an endpoint at `/foo/\d+/(\w+)`. While you could rewrite this to use non-capture groups like `/foo/(?:\d+)/(\w+)`, you can also add the following annotation to the endpoint function:

```php
/**
 * @url /foo/\d+/(\w+)
 * @openapiurl /foo/{var1}/{var2}
 */
```

## Responses

The response body is always a JSON object containing a status code and the actual data.
If a exception has been thrown, it contains the error code, the exception class name as error and the message as message.

### Examples

```json
{
  "status": 200,
  "data": true
}
```
```json
{
  "status": 400,
  "error": "MissingRequiredArgumentException",
  "message": "Argument 'username' is missing"
}
```
```json
{
    "status": 500,
    "error": "InvalidLoginException",
    "message": "Login is invalid or no access"
}
```

### Error Handling

The API Server will automatically throw a 500 error if:
* A required argument is missing
* A requested route cannot be found
* The server cannot find a specified client
* The server cannot instantiate a specified class
* The requested method cannot be found in a specified class

Additionally, you can throw your own errors by simply calling
```php
throw new \Exception('My custom error');
```

which will result in 

```json
{
  "status": 500,
  "error": "Exception",
  "message": "My custom error"
}
```

or 

```php
throw new \Exception('Another error', 123);
```

which will result in

```json
{
  "status": 123,
  "error": "Exception",
  "message": "Another error"
}
```

Alternatively, you can provide your own error handler by using the [`setExceptionHandler()`](phpdoc/#serversetexceptionhandler) method.

```php
// Overwrite error messages based on code
$server->setExceptionHandler(function(\Exception $e) use ($server) {
    $code = $e->getCode();
    switch ($code) {
        case 400:
            $message = 'Bad Request';
            break;
        case 404:
            $message = 'Not Found';
            break;
        case 500:
            $message = 'Internal Server Error';
            break;
        default:
            $message = $e->getMessage();
    }
    
    $server->getClient()->sendResponse(array(
        'status' => $code,
        'error' => get_class($e),
        'message' => $message
    ), $code);
})
```

### Debugging

For verbose error messages, enable debug mode on the desired controller by using the [`setDebugMode(true)`](phpdoc/#serversetdebugmode)` method:

```php
Server::create('/', 'myRestApi\Admin')
    ->setDebugMode(true)
    ->collectRoutes()
->run();
```

This will result in messages like:

```json
{
    "status": 500,
    "error": "InvalidLoginException",
    "message": "Login is invalid or no access",
    "line": 10,
    "file": "libs/RestAPI/Admin.class.php",
    "trace": <debugTrace>
}
```

## License

Licensed under the MIT License. See the LICENSE file for more details.
