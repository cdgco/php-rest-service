# Documentation

## Table of Contents

| Method | Description |
|--------|-------------|
| [**Client**](#Client) | This client handles API responses for a given endpoint. |
| [Client::__construct](#Client__construct) | Create a new client. |
| [Client::setController](#ClientsetController) | Attach client to different controller. |
| [Client::getController](#ClientgetController) | Return the currently attached controller. |
| [Client::setCustomFormat](#ClientsetCustomFormat) | Set the custom formatting function/method. |
| [Client::getCustomFormat](#ClientgetCustomFormat) | Returns the current formatting function/method. |
| [Client::sendResponse](#ClientsendResponse) | Sends the actual response. |
| [Client::getOutputFormatMethod](#ClientgetOutputFormatMethod) | Returns the current output format method |
| [Client::getOutputFormat](#ClientgetOutputFormat) | Returns the current output format |
| [Client::getMethod](#ClientgetMethod) | Detect the method. |
| [Client::setMethod](#ClientsetMethod) | Sets a custom http method. |
| [Client::setContentLength](#ClientsetContentLength) | Set header &quot;Content-Length&quot; from data. |
| [Client::asJSON](#ClientasJSON) | Converts data to pretty JSON. |
| [Client::asXML](#ClientasXML) | Converts data to XML. |
| [Client::asText](#ClientasText) | Converts data to text. |
| [Client::setFormat](#ClientsetFormat) | Set the current output format. |
| [Client::getURL](#ClientgetURL) | Returns the current endpoint URL. |
| [Client::setURL](#ClientsetURL) | Set the current endpoint URL. |
| [Client::setupFormats](#ClientsetupFormats) | Setup formats. |
| [**InternalClient**](#InternalClient) | This client does not send any HTTP data, instead it just returns the value. |
| [InternalClient::sendResponse](#InternalClientsendResponse) | Sends the actual response. |
| [**Server**](#Server) | A REST server class for RESTful APIs. |
| [Server::__construct](#Server__construct) | Create a new server. |
| [Server::create](#Servercreate) | Creates controller factory. User for internal testing. |
| [Server::setControllerFactory](#ServersetControllerFactory) | Change the current controller factory. |
| [Server::getControllerFactory](#ServergetControllerFactory) | Return the current controller factory. |
| [Server::setApiSpec](#ServersetApiSpec) | Enable and set parameters for OpenAPI specification generateion. |
| [Server::getApiSpec](#ServergetApiSpec) | Return the current controller factory. |
| [Server::setHttpStatusCodes](#ServersetHttpStatusCodes) | Enable / Disable sending of HTTP status codes. |
| [Server::getHttpStatusCodes](#ServergetHttpStatusCodes) | Return if HTTP status codes are sent. |
| [Server::setCheckAccess](#ServersetCheckAccess) | Set the check access function/method. |
| [Server::getCheckAccess](#ServergetCheckAccess) | Returns the current check access function/method. |
| [Server::setFallbackMethod](#ServersetFallbackMethod) | Set fallback method if no route is found. |
| [Server::getFallbackMethod](#ServergetFallbackMethod) | Returns the fallback method. |
| [Server::setDescribeRoutes](#ServersetDescribeRoutes) | Sets whether the service should serve route descriptionsthrough the OPTIONS method. |
| [Server::getDescribeRoutes](#ServergetDescribeRoutes) | Returns whether the service should serve route descriptions |
| [Server::setExceptionHandler](#ServersetExceptionHandler) | Send exception function/method. Will be fired if a route-method throws a exception. |
| [Server::getExceptionHandler](#ServergetExceptionHandler) | Returns the current exception handler function/method. |
| [Server::setDebugMode](#ServersetDebugMode) | If this is true, we send file, line and backtrace if an exception has been thrown. |
| [Server::getDebugMode](#ServergetDebugMode) | Returns if debug mode is enabled. |
| [Server::done](#Serverdone) | Alias for getParentController() |
| [Server::getParentController](#ServergetParentController) | Returns the parent controller |
| [Server::setTriggerUrl](#ServersetTriggerUrl) | Set the URL that triggers the controller. |
| [Server::getTriggerUrl](#ServergetTriggerUrl) | Gets the current trigger url. |
| [Server::setClient](#ServersetClient) | Sets the client. |
| [Server::getClient](#ServergetClient) | Get the current client. |
| [Server::sendBadRequest](#ServersendBadRequest) | Sends a &#039;Bad Request&#039; response to the client. |
| [Server::sendError](#ServersendError) | Sends a &#039;Internal Server Error&#039; response to the client. |
| [Server::sendException](#ServersendException) | Sends a exception response to the client. |
| [Server::addRoute](#ServeraddRoute) | Adds a new route for all http methods (get, post, put, delete, options, head, patch). |
| [Server::addGetRoute](#ServeraddGetRoute) | Same as addRoute, but limits to GET. |
| [Server::addPostRoute](#ServeraddPostRoute) | Same as addRoute, but limits to POST. |
| [Server::addPutRoute](#ServeraddPutRoute) | Same as addRoute, but limits to PUT. |
| [Server::addPatchRoute](#ServeraddPatchRoute) | Same as addRoute, but limits to PATCH. |
| [Server::addHeadRoute](#ServeraddHeadRoute) | Same as addRoute, but limits to HEAD. |
| [Server::addOptionsRoute](#ServeraddOptionsRoute) | Same as addRoute, but limits to OPTIONS. |
| [Server::addDeleteRoute](#ServeraddDeleteRoute) | Same as addRoute, but limits to DELETE. |
| [Server::removeRoute](#ServerremoveRoute) | Removes a route. |
| [Server::setClass](#ServersetClass) | Sets the controller class to use for function endpoints. |
| [Server::addSubController](#ServeraddSubController) | Attach a sub controller to this controller. |
| [Server::normalizeUrl](#ServernormalizeUrl) | Normalize $pUrl. Cuts of the trailing slash. |
| [Server::send](#Serversend) | Sends data to the client with 200 http code. |
| [Server::camelCase2Dashes](#ServercamelCase2Dashes) | Convert string from camel case to dashes. |
| [Server::collectRoutes](#ServercollectRoutes) | Automatically collect routes from class. |
| [Server::simulateCall](#ServersimulateCall) | Simulates a HTTP Call. |
| [Server::generateOpenApiRoutes](#ServergenerateOpenApiRoutes) | Generate route array for OpenAPI specification from current controller and URL. |
| [Server::run](#Serverrun) | Fire the magic! |
| [Server::fireMethod](#ServerfireMethod) | Fire a method. |
| [Server::describe](#Serverdescribe) | Describe a route or the whole controller with all routes. |
| [Server::getMethodMetaData](#ServergetMethodMetaData) | Fetches all meta data informations as params, return type etc. |
| [Server::parsePhpDoc](#ServerparsePhpDoc) | Parse phpDoc string and returns an array of attributes. |
| [Server::argumentName](#ServerargumentName) | Set first char to lower case. |
| [Server::findRoute](#ServerfindRoute) | Find and return the route for the URL. |

## Client

This client handles API responses for a given endpoint.

It can format the response as JSON, XML, plain text, of a custom format.

* Full name: \RestService\Client


### Client::__construct

Create a new client.

```php
Client::__construct( \RestService\Server pServerController ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pServerController` | **\RestService\Server** | The server controller. |


**Return Value:**





---
### Client::setController

Attach client to different controller.

```php
Client::setController( \RestService\Server pServerController ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pServerController` | **\RestService\Server** | Server controller. |


**Return Value:**





---
### Client::getController

Return the currently attached controller.

```php
Client::getController(  ): \RestService\Server
```





**Return Value:**

$pServerController Server controller.



---
### Client::setCustomFormat

Set the custom formatting function/method.

```php
Client::setCustomFormat( callable pFn ): \RestService\Client
```

Called with arguments: (message)


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pFn` | **callable** | The custom formatting function/method. |


**Return Value:**

$this The client instance.



---
### Client::getCustomFormat

Returns the current formatting function/method.

```php
Client::getCustomFormat(  ): callable
```





**Return Value:**

$customFormat The check access function/method.



---
### Client::sendResponse

Sends the actual response.

```php
Client::sendResponse( pMessage, string pHttpCode = '200' ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pMessage` | **** | The data to return. |
| `pHttpCode` | **string** | The HTTP code to return. |


**Return Value:**





---
### Client::getOutputFormatMethod

Returns the current output format method

```php
Client::getOutputFormatMethod( string pFormat ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pFormat` | **string** | The output format. &#039;json&#039;, &#039;xml&#039;, &#039;text&#039;, or &#039;custom&#039;. |


**Return Value:**

'asJSON', 'asXML', 'asText', or 'customFormat'.



---
### Client::getOutputFormat

Returns the current output format

```php
Client::getOutputFormat(  ): string
```





**Return Value:**

'json', 'xml', 'text', or 'custom'



---
### Client::getMethod

Detect the method.

```php
Client::getMethod(  ): string
```





**Return Value:**

'get', 'post', 'put', 'delete', 'head', 'options', or 'patch'



---
### Client::setMethod

Sets a custom http method.

```php
Client::setMethod( string pMethod ): \RestService\Client
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pMethod` | **string** | &#039;get&#039;, &#039;post&#039;, &#039;put&#039;, &#039;delete&#039;, &#039;head&#039;, &#039;options&#039;, or &#039;patch&#039; |


**Return Value:**

$this Client instance.



---
### Client::setContentLength

Set header "Content-Length" from data.

```php
Client::setContentLength( mixed pMessage ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pMessage` | **mixed** | The data to set the header from. |


**Return Value:**





---
### Client::asJSON

Converts data to pretty JSON.

```php
Client::asJSON( mixed pMessage ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pMessage` | **mixed** | The data to convert. |


**Return Value:**

JSON version of the original data.



---
### Client::asXML

Converts data to XML.

```php
Client::asXML( mixed pMessage, string pParentTagName = '', int pDepth = 1, bool pHeader = true ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pMessage` | **mixed** | The data to convert. |
| `pParentTagName` | **string** | The name of the parent tag. Default is &#039;&#039;. |
| `pDepth` | **int** | The depth of the current tag. Default is 1. |
| `pHeader` | **bool** | Whether to wrap the xml in a header. Default is true. |


**Return Value:**

XML version of the original data.



---
### Client::asText

Converts data to text.

```php
Client::asText( mixed pMessage ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pMessage` | **mixed** | The data to convert. |


**Return Value:**

JSON version of the original data.



---
### Client::setFormat

Set the current output format.

```php
Client::setFormat( string pFormat ): \RestService\Client
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pFormat` | **string** | Name of the format. |


**Return Value:**

$this Client instance.



---
### Client::getURL

Returns the current endpoint URL.

```php
Client::getURL(  ): string
```





**Return Value:**

The current endpoint URL.



---
### Client::setURL

Set the current endpoint URL.

```php
Client::setURL( string pUrl ): \RestService\Client
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUrl` | **string** | The new endpoint URL. |


**Return Value:**

$this Client instance.



---
### Client::setupFormats

Setup formats.

```php
Client::setupFormats(  ): \RestService\Client
```





**Return Value:**

$this Client instance.



---
## InternalClient

This client does not send any HTTP data, instead it just returns the value.

Good for testing purposes.

* Full name: \RestService\InternalClient
* Parent class: \RestService\Client


### InternalClient::sendResponse

Sends the actual response.

```php
InternalClient::sendResponse( mixed pMessage, string pHttpCode = '200' ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pMessage` | **mixed** | The data to process. |
| `pHttpCode` | **string** | The HTTP code to process. |


**Return Value:**

HTTP method of current request.



---
## Server

A REST server class for RESTful APIs.



* Full name: \RestService\Server


### Server::__construct

Create a new server.

```php
Server::__construct( string pTriggerUrl, string|object pControllerClass = null, \RestService\Server pParentController = null ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pTriggerUrl` | **string** | The URL that triggers the controller. |
| `pControllerClass` | **string\|object** | The default endpoint function class. |
| `pParentController` | **\RestService\Server** | The parent controller. |


**Return Value:**





---
### Server::create

Creates controller factory. User for internal testing.

```php
Server::create( string pTriggerUrl, string pControllerClass = '' ): \RestService\Server
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pTriggerUrl` | **string** | The URL that triggers the controller. |
| `pControllerClass` | **string** | The default endpoint function class. |


**Return Value:**

$this The server instance.



---
### Server::setControllerFactory

Change the current controller factory.

```php
Server::setControllerFactory( callable controllerFactory ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `controllerFactory` | **callable** |  |


**Return Value:**

$this The server instance.



---
### Server::getControllerFactory

Return the current controller factory.

```php
Server::getControllerFactory(  ): callable
```





**Return Value:**

$controllerFactory The controller factory.



---
### Server::setApiSpec

Enable and set parameters for OpenAPI specification generateion.

```php
Server::setApiSpec( string title, string version, mixed description = null, string server = null, bool recurse = true ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `title` | **string** | The name of the controller. |
| `version` | **string** | The version of the controller. |
| `description` | **mixed** |  |
| `server` | **string** | The address of the server. Default is null. |
| `recurse` | **bool** | Whether or not to recurse into the child controllers. Default is true. |


**Return Value:**

$this The server instance.



---
### Server::getApiSpec

Return the current controller factory.

```php
Server::getApiSpec(  ): array
```





**Return Value:**

$apiSpec The API specification.



---
### Server::setHttpStatusCodes

Enable / Disable sending of HTTP status codes.

```php
Server::setHttpStatusCodes( bool pWithStatusCode ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pWithStatusCode` | **bool** | If true, send HTTP status codes. |


**Return Value:**

$this The server instance.



---
### Server::getHttpStatusCodes

Return if HTTP status codes are sent.

```php
Server::getHttpStatusCodes(  ): bool
```





**Return Value:**

$withStatusCode If true, send HTTP status codes.



---
### Server::setCheckAccess

Set the check access function/method.

```php
Server::setCheckAccess( callable pFn ): \RestService\Server
```

Called with arguments: (url, route, method, arguments)


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pFn` | **callable** | The check access function/method. |


**Return Value:**

$this The server instance.



---
### Server::getCheckAccess

Returns the current check access function/method.

```php
Server::getCheckAccess(  ): callable
```





**Return Value:**

$checkAccessFn The check access function/method.



---
### Server::setFallbackMethod

Set fallback method if no route is found.

```php
Server::setFallbackMethod( string pFn ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pFn` | **string** | The fallback method. |


**Return Value:**

$this The server instance.



---
### Server::getFallbackMethod

Returns the fallback method.

```php
Server::getFallbackMethod(  ): string
```





**Return Value:**

$fallbackMethod The fallback method.



---
### Server::setDescribeRoutes

Sets whether the service should serve route descriptions
through the OPTIONS method.

```php
Server::setDescribeRoutes( bool pDescribeRoutes ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pDescribeRoutes` | **bool** | If true, serve route descriptions. |


**Return Value:**

$this The server instance.



---
### Server::getDescribeRoutes

Returns whether the service should serve route descriptions

```php
Server::getDescribeRoutes(  ): bool
```





**Return Value:**

$describeRoutes If true, serve route descriptions.



---
### Server::setExceptionHandler

Send exception function/method. Will be fired if a route-method throws a exception.

```php
Server::setExceptionHandler( callable pFn ): \RestService\Server
```

Please die/exit in your function then.
Arguments: (exception)


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pFn` | **callable** | The exception function/method. |


**Return Value:**

$this The server instance.



---
### Server::getExceptionHandler

Returns the current exception handler function/method.

```php
Server::getExceptionHandler(  ): callable
```





**Return Value:**

$sendExceptionFn The exception handler function/method.



---
### Server::setDebugMode

If this is true, we send file, line and backtrace if an exception has been thrown.

```php
Server::setDebugMode( bool pDebugMode ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pDebugMode` | **bool** | If true, send debug info. |


**Return Value:**

$this The server instance.



---
### Server::getDebugMode

Returns if debug mode is enabled.

```php
Server::getDebugMode(  ): bool
```





**Return Value:**

$debugMode If true, send debug info.



---
### Server::done

Alias for getParentController()

```php
Server::done(  ): \RestService\Server
```





**Return Value:**

$this The server instance.



---
### Server::getParentController

Returns the parent controller

```php
Server::getParentController(  ): \RestService\Server
```





**Return Value:**

$this The server instance.



---
### Server::setTriggerUrl

Set the URL that triggers the controller.

```php
Server::setTriggerUrl( pTriggerUrl ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pTriggerUrl` | **** | The URL that triggers the controller. |


**Return Value:**

$this The server instance.



---
### Server::getTriggerUrl

Gets the current trigger url.

```php
Server::getTriggerUrl(  ): string
```





**Return Value:**

$triggerUrl The trigger url.



---
### Server::setClient

Sets the client.

```php
Server::setClient( \RestService\Client|string pClient ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pClient` | **\RestService\Client\|string** | The endpoint client. |


**Return Value:**

$this The server instance.



---
### Server::getClient

Get the current client.

```php
Server::getClient(  ): \RestService\Client
```





**Return Value:**

$client The client.



---
### Server::sendBadRequest

Sends a 'Bad Request' response to the client.

```php
Server::sendBadRequest( pCode, pMessage ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pCode` | **** | The HTTP status code. |
| `pMessage` | **** | The message to send. |


**Return Value:**

The response.



---
### Server::sendError

Sends a 'Internal Server Error' response to the client.

```php
Server::sendError( pCode, pMessage ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pCode` | **** | The HTTP status code. |
| `pMessage` | **** | The message to send. |


**Return Value:**

The response.



---
### Server::sendException

Sends a exception response to the client.

```php
Server::sendException( pException ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pException` | **** | The exception to send. |


**Return Value:**





---
### Server::addRoute

Adds a new route for all http methods (get, post, put, delete, options, head, patch).

```php
Server::addRoute( string pUri, callable|string pCb, string pHttpMethod = '_all_' ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to match. |
| `pCb` | **callable\|string** | The method name of the passed controller or a php callable. |
| `pHttpMethod` | **string** | If you want to limit to a HTTP method. |


**Return Value:**

$this        The server instance.



---
### Server::addGetRoute

Same as addRoute, but limits to GET.

```php
Server::addGetRoute( string pUri, callable|string pCb ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to match. |
| `pCb` | **callable\|string** | The method name of the passed controller or a php callable. |


**Return Value:**

$this        The server instance.



---
### Server::addPostRoute

Same as addRoute, but limits to POST.

```php
Server::addPostRoute( string pUri, callable|string pCb ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to match. |
| `pCb` | **callable\|string** | The method name of the passed controller or a php callable. |


**Return Value:**

$this       The server instance.



---
### Server::addPutRoute

Same as addRoute, but limits to PUT.

```php
Server::addPutRoute( string pUri, callable|string pCb ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to match. |
| `pCb` | **callable\|string** | The method name of the passed controller or a php callable. |


**Return Value:**

$this       The server instance.



---
### Server::addPatchRoute

Same as addRoute, but limits to PATCH.

```php
Server::addPatchRoute( string pUri, callable|string pCb ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to match. |
| `pCb` | **callable\|string** | The method name of the passed controller or a php callable. |


**Return Value:**

$this       The server instance.



---
### Server::addHeadRoute

Same as addRoute, but limits to HEAD.

```php
Server::addHeadRoute( string pUri, callable|string pCb ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to match. |
| `pCb` | **callable\|string** | The method name of the passed controller or a php callable. |


**Return Value:**

$this       The server instance.



---
### Server::addOptionsRoute

Same as addRoute, but limits to OPTIONS.

```php
Server::addOptionsRoute( string pUri, callable|string pCb ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to match. |
| `pCb` | **callable\|string** | The method name of the passed controller or a php callable. |


**Return Value:**

$this       The server instance.



---
### Server::addDeleteRoute

Same as addRoute, but limits to DELETE.

```php
Server::addDeleteRoute( string pUri, callable|string pCb ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to match. |
| `pCb` | **callable\|string** | The method name of the passed controller or a php callable. |


**Return Value:**

$this       The server instance.



---
### Server::removeRoute

Removes a route.

```php
Server::removeRoute( string pUri ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to match. |


**Return Value:**

$this       The server instance.



---
### Server::setClass

Sets the controller class to use for function endpoints.

```php
Server::setClass( string|object pClass ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pClass` | **string\|object** | The class name or object. |


**Return Value:**





---
### Server::addSubController

Attach a sub controller to this controller.

```php
Server::addSubController( string pTriggerUrl, mixed pControllerClass = '' ): \RestService\Server
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pTriggerUrl` | **string** | The url to trigger the sub controller. |
| `pControllerClass` | **mixed** | A class name (autoloader required) or a instance of a class. |


**Return Value:**

$controller         new created Server. Use done() to switch the context back to the parent.



---
### Server::normalizeUrl

Normalize $pUrl. Cuts of the trailing slash.

```php
Server::normalizeUrl( string &pUrl ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUrl` | **string** | The url to normalize. |


**Return Value:**





---
### Server::send

Sends data to the client with 200 http code.

```php
Server::send( pData ): void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pData` | **** | The data to send. |


**Return Value:**





---
### Server::camelCase2Dashes

Convert string from camel case to dashes.

```php
Server::camelCase2Dashes( string pValue ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pValue` | **string** | The string to convert. |


**Return Value:**

$pValue The converted string.



---
### Server::collectRoutes

Automatically collect routes from class.

```php
Server::collectRoutes(  ): \RestService\Server
```





**Return Value:**

$this The server instance.



---
### Server::simulateCall

Simulates a HTTP Call.

```php
Server::simulateCall( string pUri, string pMethod = 'get' ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to call. |
| `pMethod` | **string** | The HTTP Method |


**Return Value:**

The response.



---
### Server::generateOpenApiRoutes

Generate route array for OpenAPI specification from current controller and URL.

```php
Server::generateOpenApiRoutes( string url ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `url` | **string** | The trigger URL for the current contorller. |


**Return Value:**

The generated route array.



---
### Server::run

Fire the magic!

```php
Server::run(  ): mixed
```

Searches the method and sends the data to the client.



**Return Value:**

The response.



---
### Server::fireMethod

Fire a method.

```php
Server::fireMethod( string pMethod, mixed pController, mixed pArguments ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pMethod` | **string** | The method to fire. |
| `pController` | **mixed** |  |
| `pArguments` | **mixed** |  |


**Return Value:**

The response.



---
### Server::describe

Describe a route or the whole controller with all routes.

```php
Server::describe( string pUri = null, bool pOnlyRoutes = false ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The uri to describe. |
| `pOnlyRoutes` | **bool** | Whether to only describe the routes. |


**Return Value:**

The description.



---
### Server::getMethodMetaData

Fetches all meta data informations as params, return type etc.

```php
Server::getMethodMetaData( \ReflectionMethod pMethod, array pRegMatches = null ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pMethod` | **\ReflectionMethod** |  |
| `pRegMatches` | **array** |  |


**Return Value:**

The meta data.



---
### Server::parsePhpDoc

Parse phpDoc string and returns an array of attributes.

```php
Server::parsePhpDoc( string pString ): array
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pString` | **string** | The phpDoc string. |


**Return Value:**

The attribute array.



---
### Server::argumentName

Set first char to lower case.

```php
Server::argumentName( string pName ): string
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pName` | **string** | The name. |


**Return Value:**

The name.



---
### Server::findRoute

Find and return the route for the URL.

```php
Server::findRoute( string pUri, string pMethod = '_all_' ): array|bool
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `pUri` | **string** | The URL. |
| `pMethod` | **string** | limit to method. |


**Return Value:**

The route or false if not found.



---
