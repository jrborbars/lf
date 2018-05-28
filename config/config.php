<?php

$appname = "MyApp";
#######################################################################
# FLIGHT config												ROUTER
#######################################################################
Flight::set('flight.log_errors', true);
Flight::set('flight.base_url', '');
#######################################################################

#######################################################################
# PARIS / Idiorm config									   DATABASE ORM
#######################################################################
ORM::configure('mysql:host=localhost;dbname=myapp');
ORM::configure('username', ''); // colocar no environment
ORM::configure('password', '');  // colocar no environment
ORM::configure('logging', true);
ORM::configure('caching', true);
ORM::configure('caching_auto_clear', true); // clear memory cache on SAVE method
#######################################################################
ORM::configure('mysql:host=localhost;dbname=information_schema', null, 'alternate');
ORM::configure('username', '', 'alternate'); // colocar no environment
ORM::configure('password', '', 'alternate');  // colocar no environment
ORM::configure('logging', true, 'alternate');
ORM::configure('caching', true, 'alternate');
ORM::configure('caching_auto_clear', true, 'alternate'); // clear memory cache on SAVE method
#######################################################################
# TWIG config											  TEMPLATES
#######################################################################
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment( $loader, array(
	//'cache' => 'templates/cache',
	'autoescape' => true,
	'debug' => true,
	'strict_variables' => false
));
$twig->addExtension(new Twig_Extension_Debug());
#$twig->addFilter('print_r', new Twig_Filter_Function('print_r'));
#######################################################################

#######################################################################
# MONOLOG config									  LOG SITE ACTIVITY
#######################################################################
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
// create a log channel
$log = new Logger('myapp');
$log->pushHandler(new StreamHandler('logs/myapp.log', Logger::DEBUG));
#$secLog = $log->withName('security');
#######################################################################
$gump = new GumpValidator();
$gump->set_error_messages(
	array("validate_match" => "{field} is different. MUST be equal...",
));
#$gump = new GUMP();
#$gump->add_validator("match", function($field, $input, $param = NULL) {
#    return is_object($input[$field]);
#});
#######################################################################
# WHOOPS config								   BEAUTYFUL ERROR MESSAGES
#######################################################################
$whoops = new Whoops\Run();
$whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
// Set Whoops as the default error and exception handler used by PHP:
$whoops->register();
#######################################################################

#######################################################################
# FLASH-MESSAGES config							MESSAGES BETWEEN PAGES
#######################################################################
$msg = new \Plasticbrain\FlashMessages\FlashMessages();
#######################################################################

#Flight::register( 'log', 'log' );

