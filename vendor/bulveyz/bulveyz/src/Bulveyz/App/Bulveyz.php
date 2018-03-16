<?php

namespace Bulveyz\App;

use Dotenv\Dotenv;
use Bulveyz\Db\Db;
use Bulveyz\Auth\Authorization;
use Bulveyz\Routing\RouterCollection;
use Bulveyz\Middleware\CsrfSecurity;

class Bulveyz
{
  private static $instance;
  private function __construct(){}
  private function __wakeup(){}
  private function __sleep(){}
  private function __clone(){}

  /** @var Dotenv */
  public $dotenv;

  /** @var CsrfSecurity */
  public $csrfWather;

  /** @var Db */
  public $db;

  /** @var Authorization */
  public $authorization;

  /** @var RouterCollection */
  public $router;

  /**
   * Run
   *
   * Star Bulveyz FrameWork
   */
  public static function run()
  {
    self::$instance = new self();

    self::$instance->csrfWather = new CsrfSecurity();
    self::$instance->csrfWather->methodWather();
    
    self::$instance->dotenv = new Dotenv('./');
    self::$instance->dotenv->load();

    self::$instance->db = new Db(getenv('DB_HOST'), getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWORD'));
    self::$instance->db->connect();
  
    self::$instance->router = new RouterCollection();
    self::$instance->router->start();
  }
}