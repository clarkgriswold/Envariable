<?php
/**
 * @copyright 2014
 */

namespace Envariable;

use Envariable\Envariable;
use Envariable\Environment;
use Envariable\Util\PathHelper;
use Envariable\Util\ServerInterfaceHelper;

/**
 * Bootstrap Envariable
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class Bootstrap
{
    /**
     * @param \Envariable\Envariable|null                 $envariable
     * @param \Envariable\Environment|null                $environment
     * @param \Envariable\Util\PathHelper|null            $pathHelper
     * @param \Envariable\Util\ServerinterfaceHelper|null $serverInterfaceHelper
     */
    public function __construct(
        Envariable $envariable = null,
        Environment $environment = null,
        PathHelper $pathHelper = null,
        ServerInterfaceHelper $serverInterfaceHelper = null
    ) {
        $pathHelper                  = $pathHelper ?: new PathHelper();
        $applicationRootPath         = $pathHelper->getApplicationRootPath();
        $applicationConfigFolderPath = $applicationRootPath . '/application/config';

        if ( ! file_exists($applicationConfigFolderPath)) {
            $applicationConfigFolderPath = $pathHelper->determineApplicationConfigFolderPath($applicationRootPath);
        }

        $configFilePath = $applicationConfigFolderPath . '/Envariable/config.php';

        if ( ! file_exists($configFilePath)) {
            $this->createConfigFile($applicationConfigFolderPath);
        }

        $configMap = require($configFilePath);

        $serverInterfaceHelper = $serverInterfaceHelper ?: new ServerInterfaceHelper();
        $environment           = $environment ?: new Environment($configMap);
        $envariable            = $envariable ?: new Envariable($configMap);

        $environment->setServerInterfaceHelper($serverInterfaceHelper);
        $environment->detect();
        $envariable->putEnv();
    }

    /**
     * Copy the config file template to the application config file location.
     *
     * @param string $applicationConfigFolderPath
     */
    private function createConfigFile($applicationConfigFolderPath)
    {
        $configTemplateFilePath = __DIR__ . '/Config/config.php';

        if ( ! mkdir($applicationConfigFolderPath . '/Envariable', 0755)) {
            throw new \Exception('Could not create Envariable config folder within application config folder.');
        }

        if ( ! copy($configTemplateFilePath, $applicationConfigFolderPath . '/Envariable/config.php')) {
            throw new \Exception('Could not copy config file to destination.');
        }
    }
}
