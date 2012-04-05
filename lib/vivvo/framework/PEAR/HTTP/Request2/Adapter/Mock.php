<?php
/* =============================================================================
 * $Revision: 4214 $
 * $Date: 2010-01-13 22:42:44 +0100 (Mon, 13 Jan 2010) $
 *
 * Vivvo CMS ${X_VERSION} (build ${X_REVISION})
 *
 * ${X_CODE_COPYRIGHT:m}
 *
 * ${X_CODE_LICENSE:m}
 * =============================================================================
 */

/**
 * Base class for HTTP_Request2 adapters
 */
require_once 'HTTP/Request2/Adapter.php';

/**
 * Mock adapter intended for testing
 *
 * Can be used to test applications depending on HTTP_Request2 package without
 * actually performing any HTTP requests. This adapter will return responses
 * previously added via addResponse()
 * <code>
 * $mock = new HTTP_Request2_Adapter_Mock();
 * $mock->addResponse("HTTP/1.1 ... ");
 *
 * $request = new HTTP_Request2();
 * $request->setAdapter($mock);
 *
 * // This will return the response set above
 * $response = $req->send();
 * </code>
 *
 * @category   HTTP
 * @package    HTTP_Request2
 * @author     Alexey Borzov <avb@php.net>
 * @version    Release: 0.5.1
 */
class HTTP_Request2_Adapter_Mock extends HTTP_Request2_Adapter
{
   /**
    * A queue of responses to be returned by sendRequest()
    * @var  array
    */
    protected $responses = array();

   /**
    * Returns the next response from the queue built by addResponse()
    *
    * If the queue is empty it will return default empty response with status 400,
    * if an Exception object was added to the queue it will be thrown.
    *
    * @param    HTTP_Request2
    * @return   HTTP_Request2_Response
    * @throws   Exception
    */
    public function sendRequest(HTTP_Request2 $request)
    {
        if (count($this->responses) > 0) {
            $response = array_shift($this->responses);
            if ($response instanceof HTTP_Request2_Response) {
                return $response;
            } else {
                // rethrow the exception
                $class   = get_class($response);
                $message = $response->getMessage();
                $code    = $response->getCode();
                throw new $class($message, $code);
            }
        } else {
            return self::createResponseFromString("HTTP/1.1 400 Bad Request\r\n\r\n");
        }
    }

   /**
    * Adds response to the queue
    *
    * @param    mixed   either a string, a pointer to an open file,
    *                   an instance of HTTP_Request2_Response or Exception
    * @throws   HTTP_Request2_Exception
    */
    public function addResponse($response)
    {
        if (is_string($response)) {
            $response = self::createResponseFromString($response);
        } elseif (is_resource($response)) {
            $response = self::createResponseFromFile($response);
        } elseif (!$response instanceof HTTP_Request2_Response &&
                  !$response instanceof Exception
        ) {
            throw new HTTP_Request2_Exception('Parameter is not a valid response');
        }
        $this->responses[] = $response;
    }

   /**
    * Creates a new HTTP_Request2_Response object from a string
    *
    * @param    string
    * @return   HTTP_Request2_Response
    * @throws   HTTP_Request2_Exception
    */
    public static function createResponseFromString($str)
    {
        $parts       = preg_split('!(\r?\n){2}!m', $str, 2);
        $headerLines = explode("\n", $parts[0]);
        $response    = new HTTP_Request2_Response(array_shift($headerLines));
        foreach ($headerLines as $headerLine) {
            $response->parseHeaderLine($headerLine);
        }
        $response->parseHeaderLine('');
        if (isset($parts[1])) {
            $response->appendBody($parts[1]);
        }
        return $response;
    }

   /**
    * Creates a new HTTP_Request2_Response object from a file
    *
    * @param    resource    file pointer returned by fopen()
    * @return   HTTP_Request2_Response
    * @throws   HTTP_Request2_Exception
    */
    public static function createResponseFromFile($fp)
    {
        $response = new HTTP_Request2_Response(fgets($fp));
        do {
            $headerLine = fgets($fp);
            $response->parseHeaderLine($headerLine);
        } while ('' != trim($headerLine));

        while (!feof($fp)) {
            $response->appendBody(fread($fp, 8192));
        }
        return $response;
    }
}
?>