<?php
/**
 * TechDivision\Stream\SecureServer
 *
 * PHP version 5
 *
 * @category  Appserver.io
 * @package   TechDivision_Socket
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\Stream;

use TechDivision\Stream;

/**
 * A secure streaming socket implementation.
 *
 * @category  Appserver.io
 * @package   TechDivision_Stream
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

class SecureServer extends Server
{
    
    /**
     * Stream scheme for SSL connection.
     *  
     * @var string
     */
    const STREAM_SCHEME_SSL = 'ssl';

    /**
     * Path to ServerCertificate
     * 
     * @var string
     */
    protected $serverCertPath = "/opt/appserver/etc/server.pem";

    /**
     * Passphrase for ServerCertificate
     * 
     * @var string
     */
    protected $serverCertPass = "";

    /**
     * Starts a streaming server listen to the specified IP address and port.
     *
     * @return \TechDivision\Stream\Server The server instance itself
     */
    public function start()
    {
        return $this->create()
            ->enableSSL()
            ->listen()
            ->setBlock();
    }

    /**
     * Enable SSL Encryption for this Stream Server
     *
     * @return \TechDivision\Stream\Server The instance itself
     */
    public function enableSSL()
    {
        // change Scheme from "tcp" to "ssl"
        $this->setScheme(self::STREAM_SCHEME_SSL);
        
        // set the SSL context
        stream_context_set_option($this->getContext(), $this->getScheme(), 'local_cert', $this->getServerCertPath());
        stream_context_set_option($this->getContext(), $this->getScheme(), 'allow_self_signed', true);
        stream_context_set_option($this->getContext(), $this->getScheme(), 'verify_peer', false);
        
        // return the instance itself
        return $this;
    }

    /**
     * Returns Path to Server Certificate
     *
     * @return string Server Certificate Path
     */
    protected function getServerCertPath()
    {
        return $this->serverCertPath;
    }

    /**
     * Returns Server Certificate Passphrase
     *
     * @return string
     */
    protected function getServerCertPass()
    {
        return $this->serverCertPass;
    }
}
