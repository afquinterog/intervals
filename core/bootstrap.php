<?php
use App\Core\App;
use App\Core\Database\QueryBuilder;
use App\Core\Database\Connection;
use App\Core\Request;

/**
 * Initializae application
 */
App::bind('config', require 'config.php');

$queryBuilder = new QueryBuilder(
    Connection::make(App::get('config')['database'])
);
App::bind('database', $queryBuilder);
