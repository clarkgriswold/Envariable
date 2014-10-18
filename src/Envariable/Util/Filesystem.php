<?php

namespace Envariable\Util;

/**
 * File System Utility.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class Filesystem
{
    /**
     * @var string
     */
    private $applicationRootPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $backtrace                 = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $backtrace                 = end($backtrace);
        $this->applicationRootPath = substr($backtrace['file'], 0, strrpos($backtrace['file'], DIRECTORY_SEPARATOR));
    }

    /**
     * Retrieve the application root path.
     *
     * @return string
     */
    public function getApplicationRootPath()
    {
        return 'crap';//$this->applicationRootPath;
    }

    /**
     * Wrapper for file_get_contents().
     *
     * @param string $filename
     *
     * @return string
     */
    public function fileGetContents($filename)
    {
        return file_get_contents($filename);
    }

    /**
     * Retrieve the config file from the given config file path.
     *
     * @param string $configFilePath
     *
     * @return array
     */
    public function loadConfigFile($configFilePath)
    {
        return require($configFilePath);
    }

    /**
     * Create directory from given path.
     *
     * @param string $path
     *
     * @return boolean
     */
    public function createDirectory($path)
    {
        return mkdir($path, 755);
    }

    /**
     * Copy target file to destination.
     *
     * @param string $target
     * @param string $destination
     *
     * @return boolean
     */
    public function copyFile($target, $destination)
    {
        return copy($target, $destination);
    }

    /**
     * Wrapper for file_exists().
     *
     * @param string $filePath
     *
     * @return boolean
     */
    public function fileExists($filePath)
    {
        return file_exists($filePath);
    }
}
