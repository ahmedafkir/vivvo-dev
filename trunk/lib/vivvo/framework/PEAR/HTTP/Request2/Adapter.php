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
 * Class representing a HTTP response
 */
require_once 'HTTP/Request2/Response.php';

/**
 * Base class for HTTP_Request2 adapters
 *
 * HTTP_Request2 class itself only defines methods for aggregating the request
 * data, all actual work of sending the request to the remote server and
 * receiving its response is performed by adapters.
 *
 * @category   HTTP
 * @package    HTTP_Request2
 * @author     Alexey Borzov <avb@php.net>
 * @version    Release: 0.5.1
 */
abstract class HTTP_Request2_Adapter
{
   /**
    * A list of methods that MUST NOT have a request body, per RFC 2616
    * @var  array
    */
    protected static $bodyDisallowed = array('TRACE');

   /**
    * Methods having defined semantics for request body
    *
    * Content-Length header (indicating that the body follows, section 4.3 of
    * RFC 2616) will be sent for these methods even if no body was added
    *
    * @var  array
    * @link http://pear.php.net/bugs/bug.php?id=12900
    * @link http://pear.php.net/bugs/bug.php?id=14740
    */
    protected static $bodyRequired = array('POST', 'PUT');

   /**
    * Request being sent
    * @var  HTTP_Request2
    */
    protected $request;

   /**
    * Request body
    * @var  string|resource|HTTP_Request2_MultipartBody
    * @see  HTTP_Request2::getBody()
    */
    protected $requestBody;

   /**
    * Length of the request body
    * @var  integer
    */
    protected $contentLength;

   /**
    * Sends request to the remote server and returns its response
    *
    * @param    HTTP_Request2
    * @return   HTTP_Request2_Response
    * @throws   HTTP_Request2_Exception
    */
    abstract public function sendRequest(HTTP_Request2 $request);

   /**
    * Calculates length of the request body, adds proper headers
    *
    * @param    array   associative array of request headers, this method will
    *                   add proper 'Content-Length' and 'Content-Type' headers
    *                   to this array (or remove them if not needed)
    */
    protected function calculateRequestLength(&$headers)
    {
        $this->requestBody = $this->request->getBody();

        if (is_string($this->requestBody)) {
            $this->contentLength = strlen($this->requestBody);
        } elseif (is_resource($this->requestBody)) {
            $stat = fstat($this->requestBody);
            $this->contentLength = $stat['size'];
            rewind($this->requestBody);
        } else {
            $this->contentLength = $this->requestBody->getLength();
            $headers['content-type'] = 'multipart/form-data; boundary=' .
                                       $this->requestBody->getBoundary();
            $this->requestBody->rewind();
        }

        if (in_array($this->request->getMethod(), self::$bodyDisallowed) ||
            0 == $this->contentLength
        ) {
            // No body: send a Content-Length header nonetheless (request #12900),
            // but do that only for methods that require a body (bug #14740)
            if (in_array($this->request->getMethod(), self::$bodyRequired)) {
                $headers['content-length'] = 0;
            } else {
                unset($headers['content-length']);
                // if the method doesn't require a body and doesn't have a
                // body, don't send a Content-Type header. (request #16799)
                unset($headers['content-type']);
            }
        } else {
            if (empty($headers['content-type'])) {
                $headers['content-type'] = 'application/x-www-form-urlencoded';
            }
            $headers['content-length'] = $this->contentLength;
        }
    }
}
?>
