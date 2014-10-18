<?php

namespace Envariable\FrameworkConfigPathLocatorCommands;

use Envariable\Util\Filesystem;

/**
 * Framework Detection Command Interface.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
interface FrameworkConfigPathLocatorCommandInterface
{
    /**
     * Define the Filesystem.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem);

    /**
     * Locate the config path within a specific framework.
     *
     * @return mixed
     */
    public function locate();
}
