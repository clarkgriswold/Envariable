<?php

namespace Envariable\FrameworkConfigPathLocatorCommands;

use Envariable\FrameworkConfigPathLocatorCommands\BaseFrameworkConfigPathLocatorCommand;
use Envariable\FrameworkConfigPathLocatorCommands\FrameworkConfigPathLocatorCommandInterface;
use Envariable\Util\Filesystem;

/**
 * CodeIgniter Config Path Locator.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class CodeIgniterConfigPathLocatorCommand extends BaseFrameworkConfigPathLocatorCommand implements FrameworkConfigPathLocatorCommandInterface
{
    /**
     * {@inheritdoc}
     */
    public function locate()
    {
        $applicationRootPath = $this->filesystem->getApplicationRootPath();
        $frontControllerPath = sprintf('%s%s%s', $applicationRootPath, DIRECTORY_SEPARATOR, 'index.php');
        $contents            = $this->filesystem->fileGetContents($frontControllerPath);

        if (preg_match('/CodeIgniter/', $contents) === 0) {
            return null;
        }

        if (preg_match('/\$application_folder\s*=\s*\'(.*)\';/', $contents, $matches) === 0) {
            throw new \RuntimeException('Application path could not be found.');
        }

        return sprintf('%s%s%s%sconfig', $applicationRootPath, DIRECTORY_SEPARATOR, $matches[1], DIRECTORY_SEPARATOR);
    }
}
