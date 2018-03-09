<?php

use RedBeanPHP\R;

/**
 * @param array $array
 *
 * Debug
 *
 * Mini Debug
 */
function debug(Array $array)
{
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}

/**
 * @param string $page
 *
 * Redirect
 *
 * Header location to page
 */
function redirect(string $page) {
  header('location: '. $page);
}

/**
 * @return mixed
 *
 * Token
 *
 * Create new token
 */
function token() {
  $token = microtime(true) . rand(100,10000000000000);
  return str_replace('.', '', $token);
}

function postArray() {
  d($_POST);
}

function getArray() {
  d($_GET);
}
