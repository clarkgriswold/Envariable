<?php

namespace Envariable;

use Envariable\EnvironmentValidationStrategyInterface;
use Envariable\Util\Server;

/**
 * Hostname and Servername validation strategy.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class HostnameServernameStrategy implements EnvironmentValidationStrategyInterface
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
        $validHostname = $this->server->getHostname() === $configMap['hostname'];

        return $validHostname && (strpos($_SERVER['SERVER_NAME'], $configMap['servername']) === 0);
    }
}
