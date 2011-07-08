<?php

// Configure DI Container
$container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
$loader = new \Symfony\Component\DependencyInjection\Loader\XmlFileLoader($container);
$loader->load( realpath(APPLICATION_PATH . '/../config') . '/di/services.xml');

// Get Entity Manager from DI Container
$entityManager = $container->get('doctrine.orm');

// Configure Helpers
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db'		=> new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($entityManager->getConnection()),
    'dialog'	=> new \Symfony\Component\Console\Helper\DialogHelper(),
	'em'		=> new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager),
));
