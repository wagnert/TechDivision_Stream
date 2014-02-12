<?php
/**
 * TechDivision\Stream\Client
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

namespace TechDivision\Stream;

use TechDivision\Stream;

/**
 * The client socket implementation. This implementation can be used for creating a client socket implementation
 * only. To create a socket server that listens to a address/port use the {@link \TechDivision\Socket\Server Server} class.
 * 
 * @category   Appserver
 * @package    TechDivision_Stream
 * @subpackage Stream
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

class Client extends Stream
{

    /**
     *  The number of bytes to send/receive.
     * @var integer
     */
    protected $lineLength = 2048;

    /**
     * New line character.
     * @var string
     */
    protected $newLine = "\n";

    /**
     * Initialize the client socket with the IP address and port.
     *
     * @param string  $address The IP address to initialize the socket with
     * @param integer $port    The port to initialize the socket with
     */
    public function __construct($address = '0.0.0.0', $port = 0)
    {
        $this->setAddress($address);
        $this->setPort($port);
    }

    /**
     * Return the number of bytes to send/receive.
     *
     * @return integer The number of bytes to send/receive
     */
    public function getLineLength()
    {
        return $this->lineLength;
    }

    /**
     * Return's the new line character.
     *
     * @return string The new line character
     */
    public function getNewLine()
    {
        return $this->newLine;
    }

    /**
     * Connects the client socket instance.
     *
     * @return \TechDivision\Stream The socket instance itself
     */
    public function start()
    {
        return $this->create()->connect();
    }

    /**
     * Sends a line (ends with the new line character) over the socket.
     *
     * @param string $data The data to send
     *
     * @return integer The number of bytes sent
     */
    public function sendLine($data)
    {
        return $this->send($data . $this->getNewLine());
    }

    /**
     * Reads a line (ends with the new line character) from the socket.
     *
     * @return string The data read from the socket
     */
    public function readLine()
    {
            
        // initialize the buffer
        $buffer = '';

        // set the new line character
        $newLine = $this->getNewLine();

        // read a chunk from the socket
        while ($buffer .= $this->read($this->getLineLength())) {
            // check if a new line character was found
            if (substr($buffer, -1) === $newLine) {
                // if yes, trim and return the data
                return rtrim($buffer, $newLine);
            }
        }
    }
}
