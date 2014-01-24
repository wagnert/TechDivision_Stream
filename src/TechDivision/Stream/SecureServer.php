<?php

/**
 * TechDivision\Stream\Server
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\Stream;

use TechDivision\Stream\Client;
use TechDivision\StreamException;

/**
 * A secure streaming socket implementation.
 *
 * @package TechDivision\Stream
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 * @author Johann Zelger <jz@techdivision.com>
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
     * Validate the certification file by given config path
     *
     * @throws StreamException
     * @return boolean
     */
    protected function validateCert() {
        // check if cert path exists
        if (!is_file($this->getCertPath())) {
            // throw exception
            throw new StreamException(
                sprintf(
                    'SSL certPath not valid for address "%s:%s". Certificate File not found "%s"',
                    $this->getAddress(),
                    $this->getPort(),
                    $this->getCertPath())
            );
        }
        // return successful validation result
        return true;
    }

    /**
     * Returns Path to Server Certificate
     *
     * @return string Server Certificate Path
     */
    protected function getCertPath()
    {
        return $this->certPath;
    }

    /**
     * Returns Server Certificate Passphrase
     *
     * @return string
     */
    protected function getCertPassphrase()
    {
        return $this->certPassphrase;
    }

    /**
     * Sets cert passphrase
     *
     * @param string $certPassphrase
     * @return SecureServer
     */
    public function setCertPassphrase($certPassphrase)
    {
        $this->certPassphrase = $certPassphrase;
        // return itself
        return $this;
    }

    /**
     * Sets path to cert file
     *
     * @param string $certPath
     * @return SecureServer
     */
    public function setCertPath($certPath)
    {
        $this->certPath = $certPath;
        // return itself
        return $this;
    }


}