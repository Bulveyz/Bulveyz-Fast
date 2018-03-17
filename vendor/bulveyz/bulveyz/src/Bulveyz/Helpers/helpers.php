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

// Preint $_POST array
function postArray() {
  d($_POST);
}

// Preint $_GET array
function getArray() {
  d($_GET);
}

// Get base domain with HTTP or HTTPS
function siteURL()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'].'/';
    return $protocol.$domainName;
}
