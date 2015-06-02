<?php
$uri = $_SERVER["REQUEST_URI"];

if (is_file('.' . $uri)) return false;

$stdout = fopen('php://stdout', 'w');

fwrite($stdout, 'XXX ' . $uri . "\n");

fclose($stdout);

$_SERVER['SCRIPT_NAME'] = '/app.php';
require 'app.php';
