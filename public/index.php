<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define( 'APPLICATION_ENV', ( getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development' ) );

/**
 * Autoloaders
 */

// This line is needed for Zend Framework to work correctly, and for Symfony Universal Class Loader to be found correctly
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH . '/../library'),
	get_include_path(),
)));

use Symfony\Component\HttpFoundation\UniversalClassLoader;

// http://docs.symfony-reloaded.org/guides/tools/autoloader.html
require_once 'Symfony/Component/HttpFoundation/UniversalClassLoader.php';
$uLoader = new UniversalClassLoader();
$uLoader->registerNamespaces(array(
	'Symfony'				=> '../library/',
	'Doctrine'				=> '../library/',
	'DoctrineExtensions'	=> '../library/Doctrine/Extensions/',
	'App'					=> '../library/',
));
$uLoader->registerPrefix('Zend_', '../library/');
$uLoader->register();

/**
 * Zend_Application
 */

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    realpath(APPLICATION_PATH . '/../config') . '/application.ini'
);
$application->bootstrap()
            ->run();