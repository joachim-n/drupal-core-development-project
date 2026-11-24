<?php

namespace DrupalGitCloneProject;

use Drupal\Core\Runtime\DrupalRuntime;
use Symfony\Component\Runtime\Runner\Symfony\ConsoleApplicationRunner;
use Symfony\Component\Runtime\RunnerInterface;

/**
 * Replacement runtime.
 *
 * This adds a replacements 'cr' command.
 */
class DrupalDevRuntime extends DrupalRuntime {

  public function getRunner(?object $application): RunnerInterface {
    $runner = parent::getRunner($application);

    if ($runner instanceof ConsoleApplicationRunner) {
      // The rebuild command needs the autoloader, which we don't have here, so
      // we need to get it all over again.
      $autoload_path = $GLOBALS['_composer_autoload_path'] ?? __DIR__ . '/../autoload.php';
      $classloader = require $autoload_path;

      /** @var \Symfony\Component\Console\Application $application */
      // Add our replacement command. This overrides the default 'cache:rebuild'
      // command that is added by \Drupal\Core\Command\DrupalApplication, but
      // FKW.
      $application->addCommand(new DrupalDevCacheRebuildCommand($classloader));
    }

    return $runner;
  }

}
