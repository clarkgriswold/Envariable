<?php

namespace Envariable;

use Envariable\EnvironmentValidationStrategyInterface;
use Envariable\Util\Server;

/**
 * Hostname validation strategy.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class HostnameStrategy implements EnvironmentValidationStrategyInterface
{
    /**
     * @var \Envariable\Util\Server
     */
    private $server;

    /**
     * Define the Server utility.
     *
     * @param \Envariable\Util\Server $server
     */
    public function setServer(Server $server)
    {
        $this->server = $server;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $configMap)
    {
        return $this->server->getHostname() === $configMap['hostname'];
    }
}
