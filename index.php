<?php
require 'vendor/autoload.php';
require 'core/bootstrap.php';

use App\Core\Router;
use App\Core\Request;

/**
 * Load the router and process the request URI
 */
Router::load('routes.php')
    ->direct(Request::uri());
