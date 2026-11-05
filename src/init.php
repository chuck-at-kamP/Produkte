<?php
/* init.php: Initialize all main PHP files */
require __DIR__ . '/../autoload.php';
use MK\DBT;
use MK\Error;
use MK\Helper;
use MK\Logger;
use MK\Session;
$config = include __DIR__ . '/../config.php';

Logger::$LOGFILE = $config['log'];
Logger::$DEBUG = true;

Logger::info(__LINE__ . " $app Start -------------------------------------------------------------");
$sess = new Session($app);
$sess->loggedin();
$fullname = $sess->getFullname();
$from     = $sess->getFrom();

$error = new Error("Produkte/$app");
$db    = new DBT($config['sql'],$config['fetchAll'],$config['db']);


