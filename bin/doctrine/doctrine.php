<?php

use	Symfony\Component\HttpFoundation\UniversalClassLoader,
	Doctrine\DBAL\Migrations\Tools\Console as MigrationsConsole;

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

/**
 * Autoloaders
 */

// This line is needed for Zend Framework to work correctly, and for Symfony Universal Class Loader to be found correctly
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

// http://docs.symfony-reloaded.org/guides/tools/autoloader.html
require_once 'Symfony/Component/HttpFoundation/UniversalClassLoader.php';
$uLoader = new UniversalClassLoader();
$uLoader->registerNamespaces(array(
	'Symfony'				=> realpath(APPLICATION_PATH . '/../library'),
	'Doctrine'				=> realpath(APPLICATION_PATH . '/../library'),
));
$uLoader->register();

$configFile = getcwd() . DIRECTORY_SEPARATOR . 'cli-config.php';

$helperSet = null;
if (file_exists($configFile)) {
    if ( ! is_readable($configFile)) {
        trigger_error(
            'Configuration file [' . $configFile . '] does not have read permission.', E_ERROR
        );
    }

    require $configFile;
    
    foreach ($GLOBALS as $helperSetCandidate) {
        if ($helperSetCandidate instanceof \Symfony\Component\Console\Helper\HelperSet) {
            $helperSet = $helperSetCandidate;
            break;
        }
    }
}

$helperSet = ($helperSet) ?: new \Symfony\Component\Console\Helper\HelperSet();

$cli = new \Symfony\Component\Console\Application('Doctrine Command Line Interface', Doctrine\ORM\Version::VERSION);
$cli->setCatchExceptions(true);
$cli->setHelperSet($helperSet);
$cli->addCommands(array(
    // DBAL Commands
    new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
    new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),

    // ORM Commands
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
    new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand(),
    new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand(),

    // Migrations Commands
    new MigrationsConsole\Command\DiffCommand(),
    new MigrationsConsole\Command\ExecuteCommand(),
    new MigrationsConsole\Command\GenerateCommand(),
    new MigrationsConsole\Command\MigrateCommand(),
    new MigrationsConsole\Command\StatusCommand(),
    new MigrationsConsole\Command\VersionCommand()

));
$cli->run();
