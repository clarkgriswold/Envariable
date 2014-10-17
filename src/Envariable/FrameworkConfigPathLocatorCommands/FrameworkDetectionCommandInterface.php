<?php

namespace Envariable\Config\FrameworkDetectionCommands;

use Envariable\Util\Filesystem;

/**
 * Framework Detection Command Interface.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
interface FrameworkDetectionCommandInterface
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
