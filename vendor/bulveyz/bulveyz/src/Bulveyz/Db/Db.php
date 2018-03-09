<?php

namespace Bulveyz\Db;

use RedBeanPHP\R;

class Db
{
  private $host;
  private $dbName;
  private $user;
  private $password;

  public function __construct(string $host, string $dbName, string $user, string $password)
  {
    $this->host = $host;
    $this->dbName = $dbName;
    $this->user = $user;
    $this->password = $password;

    return $this;
  }

  public function connect()
  {
    R::setup( 'mysql:host='.$this->host.';'.'dbname='.$this->dbName.'',
        $this->user, $this->password );
    session_start();
  }
}