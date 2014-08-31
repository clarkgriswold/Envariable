<?php
/**
 * @copyright 2014
 */

namespace Envariable\Helpers;

/**
 * Server API Helper Class
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class ServerInterfaceHelper
{
    /**
     * Fetch the server interface type.
     *
     * @return string
     */
    public function getType()
    {
        return php_sapi_name();
    }
}
