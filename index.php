<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors','on');
date_default_timezone_set('America/Sao_Paulo');

require_once('vendor/autoload.php');
require_once('lib/gump.class.php');
require_once('config/config.php');
require_once('lib/0classes.php');
require_once('lib/auth.class.php');
require_once('lib/0functions.php');

$app = new flight\Engine();
require_once('routes/route.php');
require_once('models/model.php');

$app->start();