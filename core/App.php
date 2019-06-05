<?php

namespace App\Core;

/**
 * Application service provider
 */
class App
{
    protected static $registry;

    /**
     * Store an element on the registry
     *
     * @param  string $key
     * @param  Object $value
     */
    public static function bind($key, $value)
    {
        static::$registry[$key] = $value;
    }


    /**
     * Return the key on the registry
     *
     * @param  string $key
     * @return Object
     */
    public static function get($key)
    {
        if (!array_key_exists($key, static::$registry)) {
            throw new Exception("Element doesn't exist in the container");
        }

        return static::$registry[$key];
    }
}
