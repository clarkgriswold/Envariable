<?php

namespace Envariable\FrameworkConfigPathLocatorCommands;

use Envariable\Util\Filesystem;

/**
 * Base class for framework config locator commands.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
abstract class BaseFrameworkConfigPathLocatorCommand
{
    /**
     * @var \Envariable\Util\Filesystem
     */
    protected $filesystem;

    /**
     * Define the Filesystem.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Locate the config path within a specific framework.
     *
     * @return string|null
     */
    abstract public function locate();
}
