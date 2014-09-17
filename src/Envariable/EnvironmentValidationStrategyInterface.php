<?php

namespace Envariable;

/**
 * Environment Validation Strategy Interfade
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
interface EnvironmentValidationStrategyInterface
{
    /**
     * Validate the current environment against the given config map.
     *
     * @param array $configMap
     *
     * @return boolean
     */
    public function validate(array $configMap);
}
