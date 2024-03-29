<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(is_file(__DIR__ . '/config/dev'));
$configurator->enableDebugger(__DIR__ . '/../log', 'lawondyss@gmail.com');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
  ->addDirectory(__DIR__)
  ->addDirectory(__DIR__ . '/../vendor/others')
  ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
if (is_file($localConfig = __DIR__ . '/config/config.local.neon')) {
  $configurator->addConfig($localConfig);
}

\Kdyby\Autowired\DI\AutowiredExtension::register($configurator);

$container = $configurator->createContainer();

return $container;
