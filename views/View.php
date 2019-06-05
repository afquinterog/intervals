<?php

namespace App\Views;

/**
 * Basic view implementation
 */
class View
{

    /**
     * Display a view
     *
     * @param  string $view
     * @param  array $params
     * @return string html included file
     */
    public static function open($view, $params=[])
    {
        require $view . ".view.php";
    }
}
