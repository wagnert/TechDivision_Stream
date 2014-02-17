<?php

/**
 * TechDivision\Stream\Server
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
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
namespace TechDivision\Stream;

use TechDivision\StreamException;

/**
 * A secure streaming socket implementation.
 * 
 * @category   Appserver
 * @package    TechDivision_Stream
 * @subpackage Stream
 * @author     Tim Wagner <tw@techdivision.com>
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
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
     * Path to cert file
     * 
     * @var string
     */
    protected $certPath;

    /**
     * Passphrase for cert
     * 
     * @var string
     */
    protected $certPassphrase;

    /**
     * Starts a streaming server listen to the specified IP address and port.
     *
     * @return \TechDivision\Stream\Server|null The server instance itself
     */
    public function start()
    {
        // validate given ssl cert
        if ($this->validateCert()) {
            // start socket listen and bind in blocking mode
            return $this->create()
                ->enableSSL()
                ->listen()
                ->setBlock();
        }
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
        stream_context_set_option($this->getContext(), $this->getScheme(), 'local_cert', $this->getCertPath());
        stream_context_set_option($this->getContext(), $this->getScheme(), 'allow_self_signed', true);
        stream_context_set_option($this->getContext(), $this->getScheme(), 'verify_peer', false);

        // check if passphrase is given and set it to context options
        if ($this->getCertPassphrase()) {
            stream_context_set_option(
                $this->getContext(),
                $this->getScheme(),
                'passphrase',
                $this->getCertPassphrase()
            );
        }
        
        // return the instance itself
        return $this;
    }

    /**
     * Validate the certification file by given config path.
     *
     * @return boolean TRUE if the validation was successfull, else FALSE
     * @throws \TechDivision\StreamException Is thrown if the certificate file is not available
     */
    protected function validateCert()
    {
        
        // check if cert path exists
        if (!is_file($this->getCertPath())) {
            // throw exception
            throw new StreamException(
                sprintf(
                    'SSL certPath not valid for address "%s:%s". Certificate File not found "%s"',
                    $this->getAddress(),
                    $this->getPort(),
                    $this->getCertPath()
                )
            );
        }
        
        // return successful validation result
        return true;
    }

    /**
     * Returns path to server certificate.
     *
     * @return string|null The server certificate path
     */
    protected function getCertPath()
    {
        return $this->certPath;
    }

    /**
     * Returns server certificate passphrase
     *
     * @return string|null The server certificate passphrase
     */
    protected function getCertPassphrase()
    {
        return $this->certPassphrase;
    }

    /**
     * Sets server cert passphrase.
     *
     * @param string|null $certPassphrase The server certificate passphrase
     * 
     * @return \TechDivision\Stream\SecureServer The instance itself
     */
    public function setCertPassphrase($certPassphrase)
    {
        $this->certPassphrase = $certPassphrase;
        return $this;
    }

    /**
     * Sets path to cert file
     *
     * @param string|null $certPath The path to the cert file
     * 
     * @return \TechDivision\Stream\SecureServer The instance itself
     */
    public function setCertPath($certPath)
    {
        $this->certPath = $certPath;
        return $this;
    }
}
