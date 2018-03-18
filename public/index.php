<?php

error_reporting(E_ALL);

chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';

use Bulveyz\App\Bulveyz;
Bulveyz::run();