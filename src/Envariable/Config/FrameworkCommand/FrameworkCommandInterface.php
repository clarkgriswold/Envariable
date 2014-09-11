<?php

namespace Envariable\Config\FrameworkCommand;

/**
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
interface FrameworkCommandInterface
{
    /**
     * Load the Envariable config file within a specific framework.
     * If it doesn't exist, create it, then load it.
     *
     * @return mixed
     */
    public function loadConfig();
}
