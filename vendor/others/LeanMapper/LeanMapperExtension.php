<?php

namespace LeanMapper;

use Nette\Config\CompilerExtension;

class LeanMapperExtension extends CompilerExtension
{

  public function loadConfiguration()
  {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig();

    $dbConfig = $config['database'];

    $useProfiler = isset($dbConfig['profiler']) ? $dbConfig['profiler'] : !$builder->parameters['productionMode'];

    unset($dbConfig['profiler']);

    if (isset($dbConfig['flags'])) {
      $flags = 0;
      foreach ((array) $dbConfig['flags'] as $flag) {
        $flags |= constant($flag);
      }
      $dbConfig['flags'] = $flags;
    }

    $connection = $builder->addDefinition($this->prefix('connection'))
      ->setClass('LeanMapper\Connection', [$dbConfig]);

    if (isset($config['mapper'])) {
      $builder->addDefinition($this->prefix('mapper'))
        ->setClass($config['mapper']);
    }

    if (isset($config['entityFactory'])) {
      $builder->addDefinition($this->prefix('entityFactory'))
        ->setClass($config['entityFactory']);
    }

    if ($useProfiler) {
      $panel = $builder->addDefinition($this->prefix('panel'))
        ->setClass('DibiNettePanel')
        #->addSetup('Nette\Diagnostics\Debugger::$bar->addPanel(?)', ['@self'])
        #->addSetup('Nette\Diagnostics\Debugger::$blueScreen->addPanel(?)', ['DibiNettePanel::renderException'])
      ;

      $connection->addSetup('$service->onEvent[] = ?', [[$panel, 'logEvent']]);
    }
  }

}
