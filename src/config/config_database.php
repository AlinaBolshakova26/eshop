<?php

function option(string $name, mixed $defaultValue = null): mixed
{

    /** @var array $config */

    static $config = null;

    if ($config === null)
    {
        $config = require_once __DIR__ . '/database.php';
    }

    if (array_key_exists($name, $config))
    {
        return $config[$name];
    }

    if ($defaultValue !== null)
    {
        return $defaultValue;
    }

    throw new Exception("Configuration option '{$name}' not found.");
    
}
