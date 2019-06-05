<?php

namespace App\Core;

/**
 * Basic http router implementation
 */
class Router
{
    protected $getRoutes = [];
    protected $postRoutes = [];
    protected $putRoutes = [];
    protected $deleteRoutes = [];

    protected $routesLink = [
      'GET' => 'getRoutes', 'POST' => 'postRoutes',
      'PUT' => 'putRoutes', 'DELETE' => 'deleteRoutes'
    ];

    /**
     * Create a router from a configuration file
     *
     * @param  string $file
     * @return Router
     */
    public static function load($file)
    {
        $router = new static;

        require $file;

        return $router;
    }

    /**
     * Add a resource to the router
     *
     * @param string $name
     */
    public function addResource($name)
    {
        $this->addGetRoute($name, 'ResourcesController@getResource');
        $this->addPostRoute($name, 'ResourcesController@postResource');
        $this->addPutRoute($name, 'ResourcesController@putResource');
        $this->addDeleteRoute($name, 'ResourcesController@deleteResource');
    }

    /**
     * Add a get route
     *
     * @param string $route
     * @param string $controller
     */
    public function addGetRoute($route, $controller)
    {
        $this->getRoutes[$route] = $controller;
    }

    /**
     * Add a post route
     *
     * @param string $route
     * @param string $controller
     */
    public function addPostRoute($route, $controller)
    {
        $this->postRoutes[$route] = $controller;
    }

    /**
     * Add a put route
     *
     * @param string $route
     * @param string $controller
     */
    public function addPutRoute($route, $controller)
    {
        $this->putRoutes[$route] = $controller;
    }

    /**
     * Add a delete route
     *
     * @param string $route
     * @param string $controller
     */
    public function addDeleteRoute($route, $controller)
    {
        $this->deleteRoutes[$route] = $controller;
    }

    /**
     * Execute a route
     *
     * @param  string $uri
     * @return [type]
     */
    public function direct($uri)
    {
        $actualRoutes = $this->routesLink[Request::method()];

        if (array_key_exists($uri, $this->$actualRoutes)) {
            return $this->callAction(... explode("@", $this->$actualRoutes[$uri]));
        }

        throw new Exception('No route defined for this URI.');
    }

    /**
     * Call the controller action
     *
     * @param  string $controller
     * @param  string $action
     * @return void
     */
    protected function callAction($controller, $action)
    {
        $controller = "App\Controllers\\{$controller}";
        $instance = new $controller;

        if (! method_exists($instance, $action)) {
            throw new Exception("{$controller} doesn't support {$action}");
        }

        $instance->$action();
    }
}
