<?php

namespace Envariable\Config\FrameworkCommand;

use Envariable\Util\Filesystem;

/**
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
interface FrameworkCommandInterface
{
    /**
     * Define the Filesystem utility.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem);

    /**
     * Load the Envariable config file within a specific framework.
     * If it doesn't exist, create it, then load it.
     *
     * @return mixed
     */
    public function loadConfigFile();
}
