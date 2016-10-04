<?php

namespace Database;

use Config\Config;

class Manager
{

    /**
     * @var \MongoDB\Driver\Manager
     */
    protected static $instance;


    private function __construct()
    {

    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * @return \MongoDB\Driver\Manager
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new \MongoDB\Driver\Manager(self::getUri());
        }
        return self::$instance;
    }

    /**
     * @return string
     */
    private static function getUri()
    {
        $uri = 'mongodb://';
        if (Config::has('user')) {
            $uri .= Config::get('user');
        }
        if (Config::has('password')) {
            $uri .= ':' . Config::get('password') . '@';
        }
        if (Config::has('host')) {
            $uri .= Config::get('host');
        }
        if (Config::has('port')) {
            $uri .= ':' . Config::get('port');
        }
        return $uri;
    }

}