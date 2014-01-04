<?php
/**
 * TechDivision\Stream\Server
 *
 * PHP version 5
 *
 * @category  Appserver.io
 * @package   TechDivision_Stream
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\Stream;

use TechDivision\Stream\Client;

/**
 * A streaming socket implementation.
 *
 * @category  Appserver.io
 * @package   TechDivision_Stream
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

class Server extends Client
{

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
}
