<?php

namespace App\Core\Database;

/**
 * Handle connection to the persistence layer
 */
class Connection
{
    /**
     * Connect the application to persistence layer
     *
     * @param  array $config
     * @return [type]
     */
    public static function make($config)
    {
        try {
            return new \PDO(
                $config['connection'] . ';dbname=' . $config['name'],
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}
