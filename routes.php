<?php
/**
 * Define routes for the application
 */

$router->addGetRoute('', 'ResourcesController@index');
$router->addGetRoute('intervals', 'IntervalsController@index');
$router->addPostRoute('intervals', 'IntervalsController@saveInterval');

//$router->addResource('intervals');
