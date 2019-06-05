<?php

namespace App\Core;

/**
 * Handles http request information
 */
class Request
{
    public static function uri()
    {
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }

    public function queryString()
    {
        return $_GET;
    }

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getResourceName()
    {
        return Request::uri();
    }

    public static function getResourceId()
    {
        $tokens = explode("/", Request::uri());

        if (Request::isNestedResource()) {
            return $tokens[3];
        } else {
            return $tokens[1] ?? "";
        }
    }

    public static function getParent()
    {
        $tokens = explode("/", Request::uri());

        return $tokens[0];
    }

    public static function getParentId()
    {
        $tokens = explode("/", Request::uri());

        return $tokens[1];
    }

    public static function isNestedResource()
    {
        $tokens = explode("/", Request::uri());

        return count($tokens) >= 3;
    }

    public static function post()
    {
        return $_POST;
    }

    public static function put()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            parse_str(file_get_contents("php://input"), $_PUT);

            foreach ($_PUT as $key => $value) {
                unset($_PUT[$key]);
                $_PUT[str_replace('amp;', '', $key)] = $value;
            }

            $_REQUEST = array_merge($_REQUEST, $_PUT);
        }

        return $_PUT ?? [];
    }

    public static function redirect($uri)
    {
        header('Location: '.$uri);
        exit;
    }
}
