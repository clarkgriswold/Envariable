<?php
/**
 * @copyright 2014
 */

namespace Envariable\Util;

/**
 * Path Helper
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class PathHelper
{
    /**
     * @var string
     */
    private $applicationRootPath;

    public function __construct()
    {
        $backtrace                 = debug_backtrace();
        $backtrace                 = end($backtrace);
        $this->applicationRootPath = substr($backtrace['file'], 0, strrpos($backtrace['file'], '/'));
    }

    /**
     * Retrieve the application root path.
     *
     * @return string
     */
    public function getApplicationRootPath()
    {
        return $this->applicationRootPath;
    }

    /**
     * Determine the path to the application folder.
     *
     * @return string
     */
    public function determineApplicationConfigFolderPath()
    {
        $fileAndFolderList = scandir($this->applicationRootPath);

        foreach ($fileAndFolderList as $element) {
            if (strpos($element, '.') === 0) {
                continue;
            }

            $configFolderPathCandidate      = sprintf('%s/%s/config', $this->applicationRootPath, $element);
            $controllersFolderPathCandidate = sprintf('%s/%s/controllers', $this->applicationRootPath, $element);

            if (file_exists($configFolderPathCandidate) && file_exists($controllersFolderPathCandidate)) {
                return $configFolderPathCandidate;
            }
        }

        throw new \Exception('Could not determine the path to the config folder.');
    }
}
