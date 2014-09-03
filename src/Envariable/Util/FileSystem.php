<?php

namespace Envariable\Util;

/**
 * File System Class.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class FileSystem
{
    /**
     * Create the Envariable config file within the app's config directory from the config template file.
     *
     * @param string $configTemplateFilePath
     * @param string $applicationConfigFolderPath
     */
    public function createConfigFile($configTemplateFilePath, $applicationConfigFolderPath)
    {
        if ( ! mkdir($applicationConfigFolderPath . '/Envariable', 0755)) {
            throw new \Exception('Could not create Envariable config folder within application config folder.');
        }

        if ( ! copy($configTemplateFilePath, $applicationConfigFolderPath . '/Envariable/config.php')) {
            throw new \Exception('Could not copy config file to destination.');
        }
    }
}
