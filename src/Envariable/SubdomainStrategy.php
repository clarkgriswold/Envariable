<?php

namespace Envariable;

use Envariable\EnvironmentValidationStrategyInterface;

/**
 * Subdomain validation strategy.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class SubdomainStrategy implements EnvironmentValidationStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $configMap)
    {
        return strpos($_SERVER['SERVER_NAME'], $configMap['subdomain']) === 0;
    }
}
