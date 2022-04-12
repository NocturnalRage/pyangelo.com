<?php
/**
 * This file is part of "Modernizing Legacy Applications in PHP".
 *
 * @copyright 2014-2016 Paul M. Jones <pmjones88@gmail.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Framework;

use DomainException;
use InvalidArgumentException;

/**
 * A data structure object to encapsulate superglobal references.
 *
 */
class Request
{
    /**
     * A copy of $_COOKIE.
     *
     * @var array
     */
    public $cookie = array();

    /**
     * A copy of $_ENV.
     *
     * @var array
     */
    public $env = array();

    /**
     * A copy of $_FILES.
     *
     * @var array
     */
    public $files = array();

    /**
     * A copy of $_GET.
     *
     * @var array
     */
    public $get = array();

    /**
     * A copy of $_POST.
     *
     * @var array
     */
    public $post = array();

    /**
     * A copy of $_REQUEST.
     *
     * @var array
     */
    public $request = array();

    /**
     * A copy of $_SERVER.
     *
     * @var array
     */
    public $server = array();

    /**
     * Constructor.
     *
     * @param array $globals A reference to $GLOBALS.
     */
    public function __construct($globals)
    {
        // mention the superglobals by name to invoke auto_globals_jit, thereby
        // forcing them to be populated; cf. <http://php.net/auto-globals-jit>.
        $_COOKIE;
        $_ENV;
        $_FILES;
        $_GET;
        $_POST;
        $_REQUEST;
        $_SERVER;

        // copy superglobals into properties
        $properties = array(
            'cookie' => '_COOKIE',
            'env' => '_ENV',
            'files' => '_FILES',
            'get' => '_GET',
            'post' => '_POST',
            'request' => '_REQUEST',
            'server' => '_SERVER',
        );

        foreach ($properties as $property => $superglobal) {
            if (isset($globals[$superglobal])) {
                $this->$property = $globals[$superglobal];
            }
        }
    }
}
