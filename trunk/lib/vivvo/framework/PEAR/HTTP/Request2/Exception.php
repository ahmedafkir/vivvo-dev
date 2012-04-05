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
 * Base class for exceptions in PEAR
 */
require_once 'PEAR/Exception.php';

/**
 * Exception class for HTTP_Request2 package
 *
 * Such a class is required by the Exception RFC:
 * http://pear.php.net/pepr/pepr-proposal-show.php?id=132
 *
 * @category   HTTP
 * @package    HTTP_Request2
 * @version    Release: 0.5.1
 */
class HTTP_Request2_Exception extends PEAR_Exception
{
}
?>