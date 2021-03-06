<?php
/**
 * TechDivision\Stream
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_Stream
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision;

use TechDivision\StreamException;

/**
 * A streaming socket implementation.
 * 
 * @category  Appserver
 * @package   TechDivision_Stream
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class Stream
{
    
    /**
     * Stream scheme for TCP connection.
     *  
     * @var string
     */
    const STREAM_SCHEME_TCP = 'tcp';

    /**
     * Scheme for StreamServer
     * 
     * @var string
     */
    protected $scheme = self::STREAM_SCHEME_TCP;

    /**
     * The socket resource.
     *
     * @var resource
     */
    protected $resource = null;

    /**
     * The default address the socket listens to.
     *
     * @var string
     */
    protected $address = '127.0.0.1';

    /**
     * Stream Context
     *
     * @var resource
     */
    protected $context;

    /**
     * The default port the socket listens to.
     *
     * @var int
     */
    protected $port = 0;

    /**
     * TRUE if the socket should block, else FALSE.
     *
     * @var boolean
     */
    protected $blocking = false;

    /**
     * The default stream socket timeout for the accept() method in seconds.
     *
     * @var integer
     */
    protected $defaultTimeout = -1;

    /**
     * Initializes the socket instance with the socket resource.
     *
     * @param mixed $resource The socket resource
     */
    public function __construct($resource = null)
    {
        if ($resource != null) {
            $this->setResource($resource);
        }
    }

    /**
     * Set's the socket resource to use.
     *
     * @param mixed $resource The socket resource to use
     *
     * @return \TechDivision\Stream The socket instance itself
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * Returns the socket resource used to connect to the socket.
     *
     * @return mixed The socket resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set's the address the socket listens to.
     *
     * @param string $address The address the socket listens to
     *
     * @return \TechDivision\Stream The socket instance itself
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Return's the address the socket listens to.
     *
     * @return string The address the socket listens to
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the port the socket listens to.
     *
     * @param integer $port The port the socket listens to
     *
     * @return \TechDivision\Stream The socket instance itself
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Returns the port the socket listens to.
     *
     * @return integer The port the socket listens to.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the default timeout in seconds for the accept() method
     * of the socket.
     *
     * @param integer $defaultTimeout The default timeout for the accept() method
     *
     * @return \TechDivision\Stream The socket instance itself
     * @see \TechDivision\Stream::accept()
     * @link http://www.php.net/manual/de/filesystem.configuration.php#ini.default-socket-timeout
     */
    public function setDefaultTimeout($defaultTimeout)
    {
        $this->defaultTimeout = $defaultTimeout;
        return $this;
    }

    /**
     * Sets file buffering on the given stream
     *
     * @param int $buffer The number of bytes to buffer. If buffer is 0 then write operations are unbuffered.
     *                    This ensures that all writes with fwrite are completed before other processes are
     *                    allowed to write to that output stream.
     *
     * @return int 0 on success, or EOF if the request cannot be honored.
     * @see stream_set_write_buffer()
     * @link http://php.net/manual/en/function.stream-set-read-buffer.php
     */
    public function setReadBuffer($buffer)
    {
        return @stream_set_read_buffer($this->getResource(), $buffer);
    }

    /**
     * Sets the default timeout in seconds for the accept() method
     * of the socket.
     *
     * @return integer The default timeout of the accept() method
     * @see \TechDivision\Stream::accept()
     * @link http://www.php.net/manual/de/filesystem.configuration.php#ini.default-socket-timeout
     */
    public function getDefaultTimeout()
    {
        return $this->defaultTimeout;
    }

    /**
     * Sets the Context of StreamSocket
     *
     * @param mixed $context The stream context resource
     *
     * @return \TechDivision\Stream The socket instance itself
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Returns the Stream Context
     *
     * @return mixed Stream context resource
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * This method create's a streaming socket context with the function
     * {@link http://de3.php.net/stream_context_create stream_context_create()}.
     *
     * @return \TechDivision\Stream The socket instance itself
     * @throws \TechDivision\StreamException Is thrown if an failure occured
     * @link http://de3.php.net/stream_context_create
     */
    public function create()
    {
        // create new socket
        if (($context = @stream_context_create()) === false) {
            $this->newStreamException();
        }
        
        // set the conetext
        $this->setContext($context);
        
        // return the instance itself
        return $this;
    }

    /**
     * Returns TRUE if the socket is in blocking mode, else FALSE.
     *
     * @return boolean TRUE if the socket is in blocking mode, else FALSE
     */
    public function isBlocking()
    {
        return $this->blocking;
    }

    /**
     * This method set's the socket in blocking mode by calling the socket function
     * {@link http://de3.php.net/socket_set_block socket_set_block()}.
     *
     * @return \TechDivision\Stream The socket instance itself
     * @throws \TechDivision\StreamException Is thrown if an failure occured
     * @link http://de3.php.net/stream_set_blocking
     */
    public function setBlock()
    {
        
        // activate blocking mode
        $this->blocking = true;
        
        if (@stream_set_blocking($this->resource, 1) === false) {
            $this->newStreamException();
        }
        
        // return the instance itself
        return $this;
    }

    /**
     * This method set's the socket in non-blocking mode by calling the socket function
     * {@link http://de3.php.net/stream_set_blocking stream_set_blocking()}.
     *
     * @return \TechDivision\Stream The socket instance itself
     * @throws \TechDivision\StreamException Is thrown if an failure occured
     * @link http://de3.php.net/stream_set_blocking
     */
    public function setNoBlock()
    {
        
        // activate non blocking mode
        $this->blocking = false;
        
        // set the socket in non-blocking mode
        if (@stream_set_blocking($this->resource, 0) === false) {
            $this->newStreamException();
        }
        
        // return the instance itself
        return $this;
    }

    /**
     * Destroys the socket resource.
     *
     * @return Stream The socket instance itself
     */
    public function close()
    {
        unset($this->resource);
        return $this;
    }

    /**
     * Wrapper method for the original socket function {@link http://de3.php.net/stream_socket_shutdown stream_socket_shutdown()}.
     * The method shuts down a socket for receiving, sending, or both.
     *
     * @return \TechDivision\Stream The socket instance itself
     * @throws \TechDivision\StreamException Is thrown if an failure occured
     * @link http://de3.php.net/stream_socket_shutdown
     */
    public function shutdown()
    {
        
        // try to shutdown the socket
        if (is_resource($this->resource)) {
            if (@stream_socket_shutdown($this->resource, STREAM_SHUT_RDWR) === false) {
                throw $this->newStreamException();
            }
        }
        
        // return the socket instance itself
        return $this;
    }

    /**
     * Wrapper method for the original socket function {@link http://de3.php.net/fwrite fwrite()}.
     * The method sends data to a connected socket.
     *
     * @param string $data The data to send over the socket
     *
     * @return integer The number of bytes send over the socket
     * @throws \TechDivision\StreamException Is thrown if an failure occured
     * @link http://de3.php.net/fwrite
     */
    public function send($data)
    {
        
        // try to send the data to the socket
        if (($bytesSend = @fwrite($this->resource, $data)) === false) {
            throw $this->newStreamException();
        }
        
        // return the number of bytes sent
        return $bytesSend;
    }

    /**
     * Wrapper method for the original socket function {@link http://de3.php.net/stream_socket_server stream_socket_server()}.
     * The method listens for a connection on a streaming socket.
     *
     * @return \TechDivision\Stream The stream socket instance itself
     * @throws \TechDivision\StreamException Is thrown if an failure occurred
     * @link http://de3.php.net/stream_socket_server
     */
    public function listen()
    {
        
        // create a new socket connection and listen to it
        $localSocket = "{$this->getScheme()}://{$this->getAddress()}:{$this->getPort()}";
        $socket = @stream_socket_server($localSocket, $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $this->getContext());
        
        // check if a socket connection has been enabled
        if ($socket === false) {
            try {
                // try to close the socket
                $this->close();
            } catch (Exception $previous) {
                // throw a nested exception
                throw $this->newStreamException($errno, $previous);
            }
            
            // throw a normal exception if the socket has successfully been closed
            throw $this->newStreamException($errno);
        }
        
        // set the socket resource
        $this->setResource($socket);
        // return the socket instance itself
        return $this;
    }

    /**
     * Wrapper method for the original socket function {@link http://de3.php.net/stream_socket_accept stream_socket_accept()}.
     * The method accepts a new connection on the socket.
     *
     * @param int $readBuffer The buffer to read, 0 by default
     *
     * @return \TechDivision\Stream A new client socket
     * @throws \TechDivision\StreamException Is thrown if an failure occured
     * @link http://de3.php.net/stream_socket_accept
     */
    public function accept($readBuffer = 0)
    {
        
        // accept a new incoming connection
        $client = @stream_socket_accept($this->resource, $this->getDefaultTimeout());
        // set read buffer
        @stream_set_read_buffer($client, $readBuffer);

        // check if a new incoming connection has been accepted
        if ($client === false && $this->isBlocking()) {
            throw $this->newStreamException();
        } elseif ($client === false && $this->isBlocking() === false) {
            return false;
        }
        
        // return a new client socket instance
        return new Stream($client);
    }

    /**
     * OO Wrapper for PHP's {@link http://de3.php.net/fread fread()} function.
     *
     * @param integer $length The maximum number of bytes read is specified by the length parameter
     *
     * @throws \TechDivision\StreamException Is thrown if a failure occured
     * @return string The string read from the socket
     * @link http://de3.php.net/fread
     */
    public function read($length)
    {
        
        // try to read data from the socket
        if (($result = @fread($this->resource, $length)) === false) {
            throw $this->newStreamException();
        }
        
        // return the string read from the socket
        return $result;
    }

    /**
     * OO Wrapper for PHP's {@link http://de3.php.net/stream_socket_recvfrom stream_socket_recvfrom()} function 
     * that accepts data from a remote socket up to length bytes.
     *
     * @param integer $length The maximum number of bytes read is specified by the length parameter
     * @param integer $flags  The value of flags can be any combination of the following flags, joined with the binary OR (|) operator
     *
     * @throws \TechDivision\StreamException Is thrown if a failure occured
     * @return string The string read from the socket
     * @link http://de3.php.net/stream_socket_recvfrom
     */
    public function readFrom($length, $flags = 0)
    {
        
        // initialize the buffer and the remote IP/port
        $result = '';
        $remoteIpAndPort = '127.0.0.1:1234';
        
        // try to read data from the socket
        if (($result = @stream_socket_recvfrom($this->resource, $length, $flags, $remoteIpAndPort)) === false) {
            throw $this->newStreamException();
            
        }
        
        // set IP and port and return the buffer
        list ($this->address, $this->port) = explode(':', $remoteIpAndPort);
        return $result;
    }

    /**
     * Wrapper method for the original socket function {@link http://de3.php.net/stream_socket_get_name stream_socket_get_name()}.
     * The method queries the local side of the socket.
     *
     * @param string  &$address The local address
     * @param integer &$port    The local port
     *
     * @return \TechDivision\Stream The socket instance itself
     * @throws \TechDivision\StreamException Is thrown if an failure occurred
     * @link http://de3.php.net/stream_socket_get_name
     */
    public function getSockName(&$address, &$port)
    {
        list ($address, $port) = explode(':', @stream_socket_get_name($this->resource, false));
        return $this;
    }

    /**
     * Wrapper method for the original socket function {@link http://de3.php.net/stream_socket_get_name stream_socket_get_name()}.
     * The method queries the remote side of the socket.
     *
     * @param string  &$address The remote address
     * @param integer &$port    The remote port
     *
     * @return \TechDivision\Stream The socket instance itself
     * @throws \TechDivision\StreamException Is thrown if an failure occurred
     * @link http://de3.php.net/stream_socket_get_name
     */
    public function getPeerName(&$address, &$port)
    {
        list ($address, $port) = explode(':', @stream_socket_get_name($this->resource, true));
        return $this;
    }

    /**
     * Wrapper method for the original socket function {@link http://de3.php.net/stream_context_set_option stream_context_set_option()}.
     * The method sets socket options for the socket.
     *
     * @param integer $level      The option level to set
     * @param integer $optionName The option name to set
     * @param mixed   $value      The option value to set
     *
     * @return \TechDivision\Stream The socket instance itself
     * @throws \TechDivision\StreamException Is thrown if an failure occurred
     * @link http://de3.php.net/stream_context_set_option
     */
    public function setOption($level, $optionName, $value)
    {
        
        // try to set the socket's receive timeout
        if (@stream_context_set_option($this->resource, $level, $optionName, $value) === false) {
            throw $this->newStreamException();
        }
        
        // the socket instance itself
        return $this;
    }

    /**
     * This method sets the timeout value for input operations by calling the socket function {@link http://de3.php.net/stream_set_timeout stream_set_timeout()}.
     *
     * @param integer $seconds      The seconds part on the timeout
     * @param integer $microseconds The microseconds part on the timeout
     *
     * @return \TechDivision\Stream The socket instance itself
     * @throws \TechDivision\StreamException Is thrown if an failure occured
     * @link http://de3.php.net/stream_set_timeout
     */
    public function setReceiveTimeout($seconds = 0, $microseconds = 100)
    {
        stream_set_timeout($this->resource, $seconds, $microseconds);
        return $this;
    }

    /**
     * Returns the current scheme.
     *
     * @return string The current scheme to use
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Sets curren scheme to use.
     *
     * @param string $scheme The scheme e.g. "tcp" or "udp"
     *
     * @return void
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Wrapper method for the original socket function {@link http://de3.php.net/stream_get_meta_data stream_get_meta_data()}.
     * The method returns information about an existing socket.
     *
     * @return array The array with the socket meta information
     * @throws \TechDivision\StreamException Is thrown if an failure occurred
     * @link http://de3.php.net/stream_get_meta_data
     */
    public function getMetaData()
    {
        if (($metaData = @stream_get_meta_data($this->resource)) === false) {
            throw $this->newStreamException();
        }
    }

    /**
     * Returns a new socket exception initialized with the passed error message and the last
     * found socket error.
     *
     * @param integer                       $errorCode The error code to initialize the exception with
     * @param \TechDivision\StreamException $se        The previous exception if available
     *
     * @return \TechDivision\StreamException The initialized exception ready to be thrown
     */
    protected function newStreamException($errorCode = null, $se = null)
    {
        
        // initialize error code if no error code has been passed
        if ($errorCode == null) {
            $errorCode = socket_last_error();
        }
        
        // initialize the error message based on the code
        $errorMessage = socket_strerror($errorCode);
        
        // throw an exception
        return new StreamException($errorMessage, $errorCode, $se);
    }
}
