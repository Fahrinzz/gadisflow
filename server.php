<?php

/**
 * Laravel front controller for PHP's built-in web server.
 *
 * Usage: php -S 127.0.0.1:8123 -t public server.php
 * (Serves static files from /public and routes everything else to index.php.)
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
