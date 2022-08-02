<?php

namespace RestService;

/**
 * \RestService\Server - A REST server class for RESTful APIs.
 */

class Client
{
    /**
     * Current output format.
     *
     * @var string
     */
    private $outputFormat = 'json';

    /**
     * List of possible output formats.
     *
     * @var array
     */
    private $outputFormats = array(
        'json' => 'asJSON',
        'xml' => 'asXML'
    );

    /**
     * List of possible methods.
     * @var array
     */
    public $methods = array('get', 'post', 'put', 'delete', 'head', 'options', 'patch');

    /**
     * Current URL.
     *
     * @var string
     */
    private $url;

    /**
     * @var Server
     *
     */
    private $controller;

    /**
     * Custom set http method.
     *
     * @var string
     */
    private $method;


    private static $statusCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    /**
     * @param Server $pServerController
     */
    public function __construct($pServerController)
    {
        $this->controller = $pServerController;
        if (isset($_SERVER['PATH_INFO']))
            $this->setUrl($_SERVER['PATH_INFO']);

        $this->setupFormats();
    }

    /**
     * @param \RestService\Server $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return \RestService\Server
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Sends the actual response.
     *
     * @param string $pHttpCode
     * @param $pMessage
     */
    public function sendResponse($pMessage, $pHttpCode = '200')
    {
        $suppressStatusCode = isset($_GET['_suppress_status_code']) ? $_GET['_suppress_status_code'] : false;
        if ($this->controller->getHttpStatusCodes() &&
            !$suppressStatusCode &&
            php_sapi_name() !== 'cli') {

            $status = self::$statusCodes[intval($pHttpCode)];
            header('HTTP/1.0 ' . ($status ? $pHttpCode . ' ' . $status : $pHttpCode), true, $pHttpCode);
        } elseif (php_sapi_name() !== 'cli') {
            header('HTTP/1.0 200 OK');
        }

        $pMessage = array_reverse($pMessage, true);
        $pMessage['status'] = intval($pHttpCode);
        $pMessage = array_reverse($pMessage, true);

        $method = $this->getOutputFormatMethod($this->getOutputFormat());
        echo $this->$method($pMessage);
        exit;
    }

    /**
     * @param  string $pFormat
     * @return string
     */
    public function getOutputFormatMethod($pFormat)
    {
        return $this->outputFormats[$pFormat];
    }

    /**
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * Detect the method.
     *
     * @return string
     */
    public function getMethod()
    {
        if ($this->method) {
            return $this->method;
        }

        $method = @$_SERVER['REQUEST_METHOD'];
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']))
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];

        if (isset($_GET['_method']))
            $method = $_GET['_method'];
        else if (isset($_POST['_method']))
            $method = $_POST['_method'];

        $method = strtolower($method);

        if (!in_array($method, $this->methods))
            $method = 'get';

        return $method;

    }

    /**
     * Sets a custom http method. It does then not check against
     * SERVER['REQUEST_METHOD'], $_GET['_method'] etc anymore.
     *
     * @param  string $pMethod
     * @return Client
     */
    public function setMethod($pMethod)
    {
        $this->method = $pMethod;

        return $this;
    }

    /**
     * Set header Content-Length $pMessage.
     *
     * @param $pMessage
     */
    public function setContentLength($pMessage)
    {
        if (php_sapi_name() !== 'cli' )
            header('Content-Length: '.strlen($pMessage));
    }

    /**
     * Converts $pMessage to pretty json.
     *
     * @param $pMessage
     * @return string
     */
    public function asJSON($pMessage)
    {
        if (php_sapi_name() !== 'cli' )
            header('Content-Type: application/json; charset=utf-8');

        $result = $this->jsonFormat($pMessage);
        $this->setContentLength($result);

        return $result;
    }

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * Original at http://recursive-design.com/blog/2008/03/11/format-json-with-php/
     *
     * @param string $json The original JSON string to process.
     *
     * @return string Indented version of the original JSON string.
     */
    public function jsonFormat($json)
    {
        if (!is_string($json)) $json = json_encode($json);

        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '    ';
        $newLine     = "\n";
        $inEscapeMode = false; //if the last char is a valid \ char.
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && !$inEscapeMode) {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } elseif (($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            } elseif ($char == ':' && $outOfQuotes) {
                $char .= ' ';
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            if ($char == '\\' && !$inEscapeMode)
                $inEscapeMode = true;
            else
                $inEscapeMode = false;
        }

        return $result;
    }

    /**
     * Converts $pMessage to xml.
     *
     * @param $pMessage
     * @return string
     */
    public function asXML($pMessage)
    {
        $xml = $this->toXml($pMessage);
        $xml = "<?xml version=\"1.0\"?>\n<response>\n$xml</response>\n";

        $this->setContentLength($xml);
        return $xml;

    }

    /**
     * @param  mixed  $pData
     * @param  string $pParentTagName
     * @param  int    $pDepth
     * @return string XML
     */
    public function toXml($pData, $pParentTagName = '', $pDepth = 1)
    {
        if (is_array($pData)) {
            $content = '';

            foreach ($pData as $key => $data) {
                $key = is_numeric($key) ? $pParentTagName.'-item' : $key;
                $content .= str_repeat('  ', $pDepth)
                    .'<'.htmlspecialchars($key).'>'.
                    $this->toXml($data, $key, $pDepth+1)
                    .'</'.htmlspecialchars($key).">\n";
            }

            return $content;
        } else {
            return htmlspecialchars($pData);
        }

    }

    /**
     * Add a additional output format.
     *
     * @param  string $pCode
     * @param  string $pMethod
     * @return Client $this
     */
    public function addOutputFormat($pCode, $pMethod)
    {
        $this->outputFormats[$pCode] = $pMethod;

        return $this;
    }

    /**
     * Set the current output format.
     *
     * @param  string         $pFormat a key of $outputForms
     * @return Client
     */
    public function setFormat($pFormat)
    {
        $this->outputFormat = $pFormat;

        return $this;
    }

    /**
     * Returns the url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the url.
     *
     * @param  string $pUrl
     * @return Client $this
     */
    public function setUrl($pUrl)
    {
        $this->url = $pUrl;

        return $this;
    }

    /**
     * Setup formats.
     *
     * @return Client
     */
    public function setupFormats()
    {
        //through HTTP_ACCEPT
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], '*/*') === false) {
            foreach ($this->outputFormats as $formatCode => $formatMethod) {
                if (strpos($_SERVER['HTTP_ACCEPT'], $formatCode) !== false) {
                    $this->outputFormat = $formatCode;
                    break;
                }
            }
        }

        // If URL is null, set to ''
        $urlFormat = $this->getUrl();
        if ($urlFormat == null) {
            $urlFormat = '';
        }

        //through uri suffix
        if (preg_match('/\.(\w+)$/i', $urlFormat, $matches)) {
            if (isset($this->outputFormats[$matches[1]])) {
                $this->outputFormat = $matches[1];
                $url = $this->getUrl();
                $this->setUrl(substr($url, 0, (strlen($this->outputFormat)*-1)-1));
            }
        }

        return $this;
    }

}

/**
 * This client does not send any HTTP data,
 * instead it just returns the value.
 *
 * Good for testing purposes.
 */
class InternalClient extends Client
{
    public function sendResponse($pMessage, $pHttpCode = '200')
    {
        $pMessage = array_reverse($pMessage, true);
        $pMessage['status'] = $pHttpCode+0;
        $pMessage = array_reverse($pMessage, true);

        $method = $this->getOutputFormatMethod($this->getOutputFormat());

        return $this->$method($pMessage);
    }

}

class Server
{
    /**
     * Current routes.
     *
     * structure:
     *  array(
     *    '<uri>' => <callable>
     *  )
     *
     * @var array
     */
    protected $routes = array();

    /**
     * Blacklisted http get arguments.
     *
     * @var array
     */
    protected $blacklistedGetParameters = array('_method', '_suppress_status_code');

    /**
     * Current URL that triggers the controller.
     *
     * @var string
     */
    protected $triggerUrl = '';

    /**
     * Contains the controller object.
     *
     * @var string|object
     */
    protected $controller = '';

    /**
     * List of sub controllers.
     *
     * @var array
     */
    protected $controllers = array();

    /**
     * Parent controller.
     *
     * @var \RestService\Server
     */
    protected $parentController;

    /**
     * The client
     *
     * @var Client
     */
    protected $client;

    /**
     * List of excluded methods.
     *
     * @var array|string array('methodOne', 'methodTwo') or * for all methods
     */
    protected $collectRoutesExclude = array('__construct');

    /**
     * List of possible methods.
     * @var array
     */
    public $methods = array('get', 'post', 'put', 'delete', 'head', 'options', 'patch');

    /**
     * Check access function/method. Will be fired after the route has been found.
     * Arguments: (url, route)
     *
     * @var callable
     */
    protected $checkAccessFn;

    /**
     * Send exception function/method. Will be fired if a route-method throws a exception.
     * Please die/exit in your function then.
     * Arguments: (exception)
     *
     * @var callable
     */
    protected $sendExceptionFn;

    /**
     * If this is true, we send file, line and backtrace if an exception has been thrown.
     *
     * @var boolean
     */
    protected $debugMode = false;

    /**
     * Sets whether the service should serve route descriptions
     * through the OPTIONS method.
     *
     * @var boolean
     */
    protected $describeRoutes = true;

    /**
     * If this controller can not find a route,
     * we fire this method and send the result.
     *
     * @var string
     */
    protected $fallbackMethod = '';

    /**
     * If the lib should send HTTP status codes.
     * Some Client libs does not support this, you can deactivate it via
     * ->setHttpStatusCodes(false);
     *
     * @var boolean
     */
    protected $withStatusCode = true;

    /**
     * @var callable
     */
    protected $controllerFactory;

    /**
     * @var callable
     */
    private function declaresArray(\ReflectionParameter $reflectionParameter): bool
    {
        $reflectionType = $reflectionParameter->getType();
    
        if (!$reflectionType) return false;
    
        $types = $reflectionType instanceof \ReflectionUnionType
            ? $reflectionType->getTypes()
            : [$reflectionType];
    
       return in_array('array', array_map(fn(\ReflectionNamedType $t) => $t->getName(), $types));
    }

    /**
     * Constructor
     *
     * @param string              $pTriggerUrl
     * @param string|object       $pControllerClass
     * @param \RestService\Server $pParentController
     */
    public function __construct($pTriggerUrl, $pControllerClass = null, $pParentController = null)
    {
        $this->normalizeUrl($pTriggerUrl);

        if ($pParentController) {
            $this->parentController = $pParentController;
            $this->setClient($pParentController->getClient());

            if ($pParentController->getCheckAccess())
                $this->setCheckAccess($pParentController->getCheckAccess());

            if ($pParentController->getExceptionHandler())
                $this->setExceptionHandler($pParentController->getExceptionHandler());

            if ($pParentController->getDebugMode())
                $this->setDebugMode($pParentController->getDebugMode());

            if ($pParentController->getDescribeRoutes())
                $this->setDescribeRoutes($pParentController->getDescribeRoutes());

            if ($pParentController->getControllerFactory())
                $this->setControllerFactory($pParentController->getControllerFactory());

            $this->setHttpStatusCodes($pParentController->getHttpStatusCodes());

        } else {
            $this->setClient(new Client($this));
        }

        $this->setClass($pControllerClass);
        $this->setTriggerUrl($pTriggerUrl);
    }

    /**
     * Factory.
     *
     * @param string $pTriggerUrl
     * @param string $pControllerClass
     *
     * @return Server $this
     */
    public static function create($pTriggerUrl, $pControllerClass = '')
    {
        $clazz = get_called_class();

        return new $clazz($pTriggerUrl, $pControllerClass);
    }

    /**
     * @param callable $controllerFactory
     *
     * @return Server $this
     */
    public function setControllerFactory(callable $controllerFactory)
    {
        $this->controllerFactory = $controllerFactory;

        return $this;
    }

    /**
     * @return callable
     */
    public function getControllerFactory()
    {
        return $this->controllerFactory;
    }

    /**
     * If the lib should send HTTP status codes.
     * Some Client libs does not support it.
     *
     * @param  boolean $pWithStatusCode
     * @return Server  $this
     */
    public function setHttpStatusCodes($pWithStatusCode)
    {
        $this->withStatusCode = $pWithStatusCode;

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getHttpStatusCodes()
    {
        return $this->withStatusCode;
    }

    /**
     * Set the check access function/method.
     * Will fired with arguments: (url, route)
     *
     * @param  callable $pFn
     * @return Server   $this
     */
    public function setCheckAccess($pFn)
    {
        $this->checkAccessFn = $pFn;

        return $this;
    }

    /**
     * Getter for checkAccess
     * @return callable
     */
    public function getCheckAccess()
    {
        return $this->checkAccessFn;
    }

    /**
     * If this controller can not find a route,
     * we fire this method and send the result.
     *
     * @param  string $pFn Methodname of current attached class
     * @return Server $this
     */
    public function setFallbackMethod($pFn)
    {
        $this->fallbackMethod = $pFn;

        return $this;
    }

    /**
     * Getter for fallbackMethod
     * @return string
     */
    public function fallbackMethod()
    {
        return $this->fallbackMethod;
    }

    /**
     * Sets whether the service should serve route descriptions
     * through the OPTIONS method.
     *
     * @param  boolean $pDescribeRoutes
     * @return Server  $this
     */
    public function setDescribeRoutes($pDescribeRoutes)
    {
        $this->describeRoutes = $pDescribeRoutes;

        return $this;
    }

    /**
     * Getter for describeRoutes.
     *
     * @return boolean
     */
    public function getDescribeRoutes()
    {
        return $this->describeRoutes;
    }

    /**
     * Send exception function/method. Will be fired if a route-method throws a exception.
     * Please die/exit in your function then.
     * Arguments: (exception)
     *
     * @param  callable $pFn
     * @return Server   $this
     */
    public function setExceptionHandler($pFn)
    {
        $this->sendExceptionFn = $pFn;

        return $this;
    }

    /**
     * Getter for checkAccess
     * @return callable
     */
    public function getExceptionHandler()
    {
        return $this->sendExceptionFn;
    }

    /**
     * If this is true, we send file, line and backtrace if an exception has been thrown.
     *
     * @param  boolean $pDebugMode
     * @return Server  $this
     */
    public function setDebugMode($pDebugMode)
    {
        $this->debugMode = $pDebugMode;

        return $this;
    }

    /**
     * Getter for checkAccess
     * @return boolean
     */
    public function getDebugMode()
    {
        return $this->debugMode;
    }

    /**
     * Alias for getParentController()
     *
     * @return Server
     */
    public function done()
    {
        return $this->getParentController();
    }

    /**
     * Returns the parent controller
     *
     * @return Server $this
     */
    public function getParentController()
    {
        return $this->parentController;
    }

    /**
     * Set the URL that triggers the controller.
     *
     * @param $pTriggerUrl
     * @return Server
     */
    public function setTriggerUrl($pTriggerUrl)
    {
        $this->triggerUrl = $pTriggerUrl;

        return $this;
    }

    /**
     * Gets the current trigger url.
     *
     * @return string
     */
    public function getTriggerUrl()
    {
        return $this->triggerUrl;
    }

    /**
     * Sets the client.
     *
     * @param  Client|string $pClient
     * @return Server        $this
     */
    public function setClient($pClient)
    {
        if (is_string($pClient)) {
            $pClient = new $pClient($this);
        }

        $this->client = $pClient;
        $this->client->setupFormats();

        return $this;
    }

    /**
     * Get the current client.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sends a 'Bad Request' response to the client.
     *
     * @param $pCode
     * @param $pMessage
     * @throws \Exception
     * @return string
     */
    public function sendBadRequest($pCode, $pMessage)
    {
        if (is_object($pMessage) && $pMessage->xdebug_message) $pMessage = $pMessage->xdebug_message;
        $msg = array('error' => $pCode, 'message' => $pMessage);
        if (!$this->getClient()) throw new \Exception('client_not_found_in_ServerController');
        return $this->getClient()->sendResponse($msg, '400');
    }

    /**
     * Sends a 'Internal Server Error' response to the client.
     * @param $pCode
     * @param $pMessage
     * @throws \Exception
     * @return string
     */
    public function sendError($pCode, $pMessage)
    {
        if (is_object($pMessage) && $pMessage->xdebug_message) $pMessage = $pMessage->xdebug_message;
        $msg = array('error' => $pCode, 'message' => $pMessage);
        if (!$this->getClient()) throw new \Exception('client_not_found_in_ServerController');
        return $this->getClient()->sendResponse($msg, '500');
    }

    /**
     * Sends a exception response to the client.
     * @param $pException
     * @throws \Exception
     */
    public function sendException($pException)
    {
        if ($this->sendExceptionFn) {
            call_user_func_array($this->sendExceptionFn, array($pException));
        }

        $message = $pException->getMessage();
        if (is_object($message) && $message->xdebug_message) $message = $message->xdebug_message;

        $msg = array('error' => get_class($pException), 'message' => $message);

        if ($this->debugMode) {
            $msg['file'] = $pException->getFile();
            $msg['line'] = $pException->getLine();
            $msg['trace'] = $pException->getTraceAsString();
        }

        if (!$this->getClient()) throw new \Exception('Client not found in ServerController');
        return $this->getClient()->sendResponse($msg, '500');

    }

    /**
     * Adds a new route for all http methods (get, post, put, delete, options, head, patch).
     *
     * @param  string          $pUri
     * @param  callable|string $pCb         The method name of the passed controller or a php callable.
     * @param  string          $pHttpMethod If you want to limit to a HTTP method.
     * @return Server
     */
    public function addRoute($pUri, $pCb, $pHttpMethod = '_all_')
    {
        $this->routes[$pUri][ $pHttpMethod ] = $pCb;

        return $this;
    }

    /**
     * Same as addRoute, but limits to GET.
     *
     * @param  string          $pUri
     * @param  callable|string $pCb  The method name of the passed controller or a php callable.
     * @return Server
     */
    public function addGetRoute($pUri, $pCb)
    {
        $this->addRoute($pUri, $pCb, 'get');

        return $this;
    }

    /**
     * Same as addRoute, but limits to POST.
     *
     * @param  string          $pUri
     * @param  callable|string $pCb  The method name of the passed controller or a php callable.
     * @return Server
     */
    public function addPostRoute($pUri, $pCb)
    {
        $this->addRoute($pUri, $pCb, 'post');

        return $this;
    }

    /**
     * Same as addRoute, but limits to PUT.
     *
     * @param  string          $pUri
     * @param  callable|string $pCb  The method name of the passed controller or a php callable.
     * @return Server
     */
    public function addPutRoute($pUri, $pCb)
    {
        $this->addRoute($pUri, $pCb, 'put');

        return $this;
    }

    /**
     * Same as addRoute, but limits to PATCH.
     *
     * @param  string          $pUri
     * @param  callable|string $pCb  The method name of the passed controller or a php callable.
     * @return Server
     */
    public function addPatchRoute($pUri, $pCb)
    {
        $this->addRoute($pUri, $pCb, 'patch');

        return $this;
    }

    /**
     * Same as addRoute, but limits to HEAD.
     *
     * @param  string          $pUri
     * @param  callable|string $pCb  The method name of the passed controller or a php callable.
     * @return Server
     */
    public function addHeadRoute($pUri, $pCb)
    {
        $this->addRoute($pUri, $pCb, 'head');

        return $this;
    }

    /**
     * Same as addRoute, but limits to OPTIONS.
     *
     * @param  string          $pUri
     * @param  callable|string $pCb  The method name of the passed controller or a php callable.
     * @return Server
     */
    public function addOptionsRoute($pUri, $pCb)
    {
        $this->addRoute($pUri, $pCb, 'options');

        return $this;
    }

    /**
     * Same as addRoute, but limits to DELETE.
     *
     * @param  string          $pUri
     * @param  callable|string $pCb  The method name of the passed controller or a php callable.
     * @return Server
     */
    public function addDeleteRoute($pUri, $pCb)
    {
        $this->addRoute($pUri, $pCb, 'delete');

        return $this;
    }

    /**
     * Removes a route.
     *
     * @param  string $pUri
     * @return Server
     */
    public function removeRoute($pUri)
    {
        unset($this->routes[$pUri]);

        return $this;
    }

    /**
     * Sets the controller class.
     *
     * @param string|object $pClass
     */
    public function setClass($pClass)
    {
        if (is_string($pClass)) {
            $this->createControllerClass($pClass);
        } elseif (is_object($pClass)) {
            $this->controller = $pClass;
        } else {
            $this->controller = $this;
        }
    }

    /**
     * Setup the controller class.
     *
     * @param  string    $pClassName
     * @throws \Exception
     */
    protected function createControllerClass($pClassName)
    {
        if ($pClassName != '') {
            try {
                if ($this->controllerFactory) {
                    $this->controller = call_user_func_array($this->controllerFactory, array(
                        $pClassName,
                        $this
                    ));
                } else {
                    $this->controller = new $pClassName($this);
                }
                if (get_parent_class($this->controller) == '\RestService\Server') {
                    $this->controller->setClient($this->getClient());
                }
            } catch (\Exception $e) {
                throw new \Exception('Error during initialisation of '.$pClassName.': '.$e, 0, $e);
            }
        } else {
            $this->controller = $this;
        }
    }

    /**
     * Attach a sub controller.
     *
     * @param string $pTriggerUrl
     * @param mixed  $pControllerClass A class name (autoloader required) or a instance of a class.
     *
     * @return Server new created Server. Use done() to switch the context back to the parent.
     */
    public function addSubController($pTriggerUrl, $pControllerClass = '')
    {
        $this->normalizeUrl($pTriggerUrl);

        $base = $this->triggerUrl;
        if ($base == '/') $base = '';

        $controller = new Server($base . $pTriggerUrl, $pControllerClass, $this);

        $this->controllers[] = $controller;

        return $controller;
    }

    /**
     * Normalize $pUrl. Cuts of the trailing slash.
     *
     * @param string $pUrl
     */
    public function normalizeUrl(&$pUrl)
    {
        if ('/' === $pUrl) return;
        if ($pUrl != null) {
            if (substr($pUrl, -1) == '/') $pUrl = substr($pUrl, 0, -1);
            if (substr($pUrl, 0, 1) != '/') $pUrl = '/' . $pUrl;
        }
    }

    /**
     * Sends data to the client with 200 http code.
     *
     * @param $pData
     */
    public function send($pData)
    {
        return $this->getClient()->sendResponse(array('data' => $pData), 200);
    }

    /**
     * @param  string $pValue
     * @return string
     */
    public function camelCase2Dashes($pValue)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $pValue));
    }

    /**
     * Setup automatic routes.
     *
     * @return Server
     */
    public function collectRoutes()
    {
        if ($this->collectRoutesExclude == '*') return $this;

        $methods = get_class_methods($this->controller);
        foreach ($methods as $method) {
            if (in_array($method, $this->collectRoutesExclude)) continue;

            $info = explode('/', preg_replace('/([a-z]*)(([A-Z]+)([a-zA-Z0-9_]*))/', '$1/$2', $method));
            $uri  = $this->camelCase2Dashes((empty($info[1]) ? '' : $info[1]));

            $httpMethod  = $info[0];
            if ($httpMethod == 'all') {
                $httpMethod = '_all_';
            }

            $reflectionMethod = new \ReflectionMethod($this->controller, $method);
            if ($reflectionMethod->isPrivate()) continue;

            $phpDocs = $this->getMethodMetaData($reflectionMethod);
            if (isset($phpDocs['url'])) {
                if (isset($phpDocs['url']['url'])) {
                    //only one route
                    $this->routes[$phpDocs['url']['url']][$httpMethod] = $method;
                } else {
                    foreach($phpDocs['url'] as $urlAnnotation) {
                        $this->routes[$urlAnnotation['url']][$httpMethod] = $method;
                    }
                }
            } else {
                $this->routes[$uri][$httpMethod] = $method;
            }

        }

        return $this;
    }

    /**
     * Simulates a HTTP Call.
     *
     * @param  string $pUri
     * @param  string $pMethod The HTTP Method
     * @return string
     */
    public function simulateCall($pUri, $pMethod = 'get')
    {
        if (($idx = strpos($pUri, '?')) !== false) {
            parse_str(substr($pUri, $idx+1), $_GET);
            $pUri = substr($pUri, 0, $idx);
        }
        $this->getClient()->setUrl($pUri);
        $this->getClient()->setMethod($pMethod);

        return $this->run();
    }

    /**
     * Fire the magic!
     *
     * Searches the method and sends the data to the client.
     *
     * @return mixed
     */
    public function run()
    {
        //check sub controller
        foreach ($this->controllers as $controller) {
            if ($result = $controller->run()) {
                return $result;
            }
        }

        $requestedUrl = $this->getClient()->getUrl();
        $this->normalizeUrl($requestedUrl);
        //check if its in our area

        if ($requestedUrl != null) {
            if (strpos($requestedUrl, $this->triggerUrl) !== 0) return;
        }

        $endPos = $this->triggerUrl === '/' ? 1 : strlen($this->triggerUrl) + 1;

        if ($requestedUrl != null) {
            $uri = substr($requestedUrl, $endPos);
        }
        else {
            $uri = '';
        }

        if (!$uri) $uri = '';

        $route = false;
        $arguments = array();
        $requiredMethod = $this->getClient()->getMethod();

        //does the requested uri exist?
        list($callableMethod, $regexArguments, $method, $routeUri) = $this->findRoute($uri, $requiredMethod);

        if ((!$callableMethod || $method != 'options') && $requiredMethod == 'options') {
            $description = $this->describe($uri);
            $this->send($description);
        }

        if (!$callableMethod) {
            if (!$this->getParentController()) {
                if ($this->fallbackMethod) {
                    $m = $this->fallbackMethod;
                    $this->send($this->controller->$m());
                } else {
                    return $this->sendBadRequest('RouteNotFoundException', "There is no route for '$uri'.");
                }
            } else {
                return false;
            }
        }

        if ($method == '_all_')
            $arguments[] = $method;

        if (is_array($regexArguments)) {
            $arguments = array_merge($arguments, $regexArguments);
        }

        //open class and scan method
        if ($this->controller && is_string($callableMethod)) {
            $ref = new \ReflectionClass($this->controller);

            if (!method_exists($this->controller, $callableMethod)) {
                $this->sendBadRequest('MethodNotFoundException', "There is no method '$callableMethod' in ".
                    get_class($this->controller).".");
            }

            $reflectionMethod = $ref->getMethod($callableMethod);
        } else if (is_callable($callableMethod)) {
            $reflectionMethod = new \ReflectionFunction($callableMethod);
        }

        $params = $reflectionMethod->getParameters();

        if ($method == '_all_') {
            //first parameter is $pMethod
            array_shift($params);
        }

        //remove regex arguments
        for ($i=0; $i<count($regexArguments); $i++) {
            array_shift($params);
        }

        //collect arguments
        foreach ($params as $param) {
            $name = $this->argumentName($param->getName());

            if ($name == '_') {
                $thisArgs = array();
                foreach ($_GET as $k => $v) {
                    if (substr($k, 0, 1) == '_' && $k != '_suppress_status_code')
                        $thisArgs[$k] = $v;
                }
                $arguments[] = $thisArgs;
            } else {
                $_PUT = null;
                if (isset($_SERVER['REQUEST_METHOD'])) {
                    $method = $_SERVER['REQUEST_METHOD'];
                    if ('PUT' === $method) {
                        parse_str(file_get_contents('php://input'), $_PUT);
                    }
                }

                if (!$param->isOptional() && !isset($_GET[$name]) && !isset($_POST[$name]) && !isset($_PUT[$name])) {
                    return $this->sendBadRequest('MissingRequiredArgumentException', sprintf("Argument '%s' is missing.", $name));
                }

                $arguments[] = isset($_GET[$name]) ? ($_GET[$name]) : (isset($_POST[$name]) ? $_POST[$name] : (isset($_PUT[$name]) ? $_PUT[$name] : $param->getDefaultValue()));
            }
        }

        if ($this->checkAccessFn) {
            $args[] = $this->getClient()->getUrl();
            $args[] = $route;
            $args[] = $arguments;
            try {
                call_user_func_array($this->checkAccessFn, $args);
            } catch (\Exception $e) {
                $this->sendException($e);
            }
        }

        //fire method
        $object = $this->controller;

        return $this->fireMethod($callableMethod, $object, $arguments);

    }

    public function fireMethod($pMethod, $pController, $pArguments)
    {
        $callable = false;

        if ($pController && is_string($pMethod)) {
            if (!method_exists($pController, $pMethod)) {
                return $this->sendError('MethodNotFoundException', sprintf('Method %s in class %s not found.', $pMethod, get_class($pController)));
            } else {
                $callable = array($pController, $pMethod);
            }
        } elseif (is_callable($pMethod)) {
            $callable = $pMethod;
        }

        if ($callable) {
            try {
                return $this->send(call_user_func_array($callable, $pArguments));
            } catch (\Exception $e) {
                return $this->sendException($e);
            }
        }
    }

    /**
     * Describe a route or the whole controller with all routes.
     *
     * @param  string  $pUri
     * @param  boolean $pOnlyRoutes
     * @return array
     */
    public function describe($pUri = null, $pOnlyRoutes = false)
    {
        $definition = array();

        if (!$pOnlyRoutes) {
            $definition['parameters'] = array(
                '_method' => array('description' => 'Can be used as HTTP METHOD if the client does not support HTTP methods.', 'type' => 'string',
                                   'values' => 'GET, POST, PUT, DELETE, HEAD, OPTIONS, PATCH'),
                '_suppress_status_code' => array('description' => 'Suppress the HTTP status code.', 'type' => 'boolean', 'values' => '1, 0'),
                '_format' => array('description' => 'Format of generated data. Can be added as suffix .json .xml', 'type' => 'string', 'values' => 'json, xml'),
            );
        }

        $definition['controller'] = array(
            'entryPoint' => $this->getTriggerUrl()
        );

        foreach ($this->routes as $routeUri => $routeMethods) {

            $matches = array();
            if (!$pUri || ($pUri && preg_match('|^'.$routeUri.'$|', $pUri, $matches))) {

                if ($matches) {
                    array_shift($matches);
                }
                $def = array();
                $def['uri'] = $this->getTriggerUrl().'/'.$routeUri;

                foreach ($routeMethods as $method => $phpMethod) {

                    if (is_string($phpMethod)) {
                        $ref = new \ReflectionClass($this->controller);
                        $refMethod = $ref->getMethod($phpMethod);
                    } else {
                        $refMethod = new \ReflectionFunction($phpMethod);
                    }

                    $def['methods'][strtoupper($method)] = $this->getMethodMetaData($refMethod, $matches);

                }
                $definition['controller']['routes'][$routeUri] = $def;
            }
        }

        if (!$pUri) {
            foreach ($this->controllers as $controller) {
                $definition['subController'][$controller->getTriggerUrl()] = $controller->describe(false, true);
            }
        }

        return $definition;
    }

    /**
     * Fetches all meta data informations as params, return type etc.
     *
     * @param  \ReflectionMethod $pMethod
     * @param  array             $pRegMatches
     * @return array
     */
    public function getMethodMetaData(\ReflectionFunctionAbstract $pMethod, $pRegMatches = null)
    {
        $file = $pMethod->getFileName();
        $startLine = $pMethod->getStartLine();

        $fh = fopen($file, 'r');
        if (!$fh) return false;

        $lineNr = 1;
        $lines = array();
        while (($buffer = fgets($fh)) !== false) {
            if ($lineNr == $startLine) break;
            $lines[$lineNr] = $buffer;
            $lineNr++;
        }
        fclose($fh);

        $phpDoc = '';
        $blockStarted = false;
        while ($line = array_pop($lines)) {

            if ($blockStarted) {
                $phpDoc = $line.$phpDoc;

                //if start comment block: /*
                if (preg_match('/\s*\t*\/\*/', $line)) {
                    break;
                }
                continue;
            } else {
                //we are not in a comment block.
                //if class def, array def or close bracked from fn comes above
                //then we dont have phpdoc
                if (preg_match('/^\s*\t*[a-zA-Z_&\s]*(\$|{|})/', $line)) {
                    break;
                }
            }

            $trimmed = trim($line);
            if ($trimmed == '') continue;

            //if end comment block: */
            if (preg_match('/\*\//', $line)) {
                $phpDoc = $line.$phpDoc;
                $blockStarted = true;
                //one line php doc?
                if (preg_match('/\s*\t*\/\*/', $line)) {
                    break;
                }
            }
        }

        $phpDoc = $this->parsePhpDoc($phpDoc);

        $refParams = $pMethod->getParameters();
        $params = array();

        $fillPhpDocParam = !isset($phpDoc['param']);

        foreach ($refParams as $param) {
            $params[$param->getName()] = $param;
            if ($fillPhpDocParam) {
                $phpDoc['param'][] = array(
                    'name' => $param->getName(),
                    'type' => $this->declaresArray($param)?'array':'mixed'
                );
            }
        }

        $parameters = array();

        if (isset($phpDoc['param'])) {
            if (is_array($phpDoc['param']) && is_string(key($phpDoc['param'])))
                $phpDoc['param'] = array($phpDoc['param']);

            $c = 0;
            foreach ($phpDoc['param'] as $phpDocParam) {
                if (isset($params[$phpDocParam['name']]))
                    $param = $params[$phpDocParam['name']];
                if (!$param) continue;
                $parameter = array(
                    'type' => $phpDocParam['type']
                );

                if ($pRegMatches && is_array($pRegMatches) && $pRegMatches[$c]) {
                    $parameter['fromRegex'] = '$'.($c+1);
                }

                $parameter['required'] = !$param->isOptional();

                if ($param->isDefaultValueAvailable()) {
                    $parameter['default'] = str_replace(array("\n", ' '), '', var_export($param->getDefaultValue(), true));
                }
                $parameters[$this->argumentName($phpDocParam['name'])] = $parameter;
                $c++;
            }
        }

        if (!isset($phpDoc['return']))
            $phpDoc['return'] = array('type' => 'mixed');

        $result = array(
            'parameters' => $parameters,
            'return' => $phpDoc['return']
        );

        if (isset($phpDoc['description']))
            $result['description'] = $phpDoc['description'];

        if (isset($phpDoc['url']))
            $result['url'] = $phpDoc['url'];

        return $result;
    }

    /**
     * Parse phpDoc string and returns an array.
     *
     * @param  string $pString
     * @return array
     */
    public function parsePhpDoc($pString)
    {
        preg_match('#^/\*\*(.*)\*/#s', trim($pString), $comment);

        if (0 === count($comment)) return array();

        $comment = trim($comment[1]);

        preg_match_all('/^\s*\*(.*)/m', $comment, $lines);
        $lines = $lines[1];

        $tags = array();
        $currentTag = '';
        $currentData = '';

        foreach ($lines as $line) {
            $line = trim($line);

            if (substr($line, 0, 1) == '@') {

                if ($currentTag)
                    $tags[$currentTag][] = $currentData;
                else
                    $tags['description'] = $currentData;

                $currentData = '';
                preg_match('/@([a-zA-Z_]*)/', $line, $match);
                $currentTag = $match[1];
            }

            $currentData = trim($currentData.' '.$line);

        }
        if ($currentTag)
            $tags[$currentTag][] = $currentData;
        else
            $tags['description'] = $currentData;

        //parse tags
        $regex = array(
            'param' => array('/^@param\s*\t*([a-zA-Z_\\\[\]]*)\s*\t*\$([a-zA-Z_]*)\s*\t*(.*)/', array('type', 'name', 'description')),
            'url' => array('/^@url\s*\t*(.+)/', array('url')),
            'return' => array('/^@return\s*\t*([a-zA-Z_\\\[\]]*)\s*\t*(.*)/', array('type', 'description')),
        );
        foreach ($tags as $tag => &$data) {
            if ($tag == 'description') continue;
            foreach ($data as &$item) {
                if (isset($regex[$tag])) {
                    preg_match($regex[$tag][0], $item, $match);
                    $item = array();
                    $c = count($match);
                    for ($i =1; $i < $c; $i++) {
                        if (isset($regex[$tag][1][$i-1])) {
                            $item[$regex[$tag][1][$i-1]] = $match[$i];
                        }
                    }
                }
            }
            if (count($data) == 1)
                $data = $data[0];
        }

        return $tags;
    }

    /**
     * If the name is a camelcased one whereas the first char is lowercased,
     * then we remove the first char and set first char to lower case.
     *
     * @param  string $pName
     * @return string
     */
    public function argumentName($pName)
    {
        if (ctype_lower(substr($pName, 0, 1)) && ctype_upper(substr($pName, 1, 1))) {
            return strtolower(substr($pName, 1, 1)).substr($pName, 2);
        } return $pName;
    }

    /**
     * Find and return the route for $pUri.
     *
     * @param  string        $pUri
     * @param  string        $pMethod limit to method.
     * @return array|boolean
     */
    public function findRoute($pUri, $pMethod = '_all_')
    {
        if (isset($this->routes[$pUri][$pMethod]) && $method = $this->routes[$pUri][$pMethod]) {
            return array($method, array(), $pMethod, $pUri);
        } elseif ($pMethod != '_all_' && isset($this->routes[$pUri]['_all_']) && $method = $this->routes[$pUri]['_all_']) {
            return array($method, array(), $pMethod, $pUri);
        } else {
            //maybe we have a regex uri
            foreach ($this->routes as $routeUri => $routeMethods) {

                if (preg_match('|^'.$routeUri.'$|', $pUri, $matches)) {

                    if (!isset($routeMethods[$pMethod])) {
                        if (isset($routeMethods['_all_']))
                            $pMethod = '_all_';
                        else
                            continue;
                    }

                    array_shift($matches);
                    foreach ($matches as $match) {
                        $arguments[] = $match;
                    }

                    return array($routeMethods[$pMethod], $arguments, $pMethod, $routeUri);
                }

            }
        }

        return false;
    }

}
