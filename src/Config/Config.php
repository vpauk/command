<?php

namespace Config;


class Config
{
    private static $params = [
        'name' => 'daenerys',  // Database name
        'host' => '127.0.0.1', // Database host
        'port' => '27017',     // Database pot
        'user' => null,        // Database user
        'password' => null     // Database user password
    ];

    /**
     * @param string $id
     * @return mixed
     */
    public static function get($id)
    {
        if (!isset(self::$params[$id])) {
            throw new \InvalidArgumentException(sprintf('Parameter "%s" does not exists!'), $id);
        }
        return self::$params[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function has($id)
    {
        return isset(self::$params[$id]);
    }
}