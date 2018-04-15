<?php

namespace Classes\app;

/**
 * Class Core
 * @package Classes
 *
 * You can add new classes and methods
 */
class Core
{

    private static $instance;
    private function __construct(){}
    private function __sleep(){}
    private function __wakeup(){}
    private function __clone(){}

    public static function run()
    {
        self::$instance = new self();
    }
}