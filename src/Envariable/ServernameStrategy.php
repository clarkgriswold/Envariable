<?php

namespace Envariable;

use Envariable\EnvironmentValidationStrategyInterface;

/**
 * Servername validation strategy.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class ServernameStrategy implements EnvironmentValidationStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $configMap)
    {
        return strpos($_SERVER['SERVER_NAME'], $configMap['servername']) !== false;
    }
}
