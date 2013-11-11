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

/**
 * A streaming socket implementation.
 *
 * @package TechDivision\Stream
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class Server extends Client
{

    /**
     * Scheme for StreamServer
     * 
     * @var string
     */
    protected $scheme = "tcp";

    /**
     * Starts a streaming server listen to the specified IP address and port.
     *
     * @return \TechDivision\Stream\Server The server instance itself
     */
    public function start()
    {
        return $this->create()
            ->listen()
            ->setBlock();
    }

    /**
     * Returns Current Stream Server Scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Sets Stream Server Scheme
     *
     * @param
     *            $scheme
     * @return void
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }
}