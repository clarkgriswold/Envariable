<?php

namespace Envariable\Util;

/**
 * Server Utility Class
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class ServerUtil
{
    /**
     * Retrieve the server interface type.
     *
     * @return string
     */
    public function getInterfaceType()
    {
        return php_sapi_name();
    }

    /**
     * Fetch the machine name.
     *
     * @return string
     */
    public function getHostname()
    {
        return gethostname();
    }
}
