<?php

namespace DrupalGitCloneProject;

use Composer\Autoload\ClassLoader;
use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;

/**
 * Replaces the core cache rebuild command.
 *
 * This is necessary because the core command finds the wrong Drupal root and
 * so can't find the settings.php file.
 *
 * This class's code needs to be kept in sync with the class it replaces.
 *
 * @see \Drupal\Core\Command\CacheRebuildCommand
 */
#[AsCommand(
  name: 'cache:rebuild',
  description: 'Rebuild all caches.',
  aliases: [
    'cr',
    'rebuild',
  ],
)]
class DrupalDevCacheRebuildCommand {

  public function __construct(
    protected ClassLoader $classLoader,
  ) {}

  /**
   * Rebuild all caches.
   */
  public function __invoke(InputInterface $input, OutputInterface $output): int {
    $io = new SymfonyStyle($input, $output);

    $cwd = getcwd();

    // Get the Drupal root instead of relying on the DRUPAL_ROOT constant.
    // We can use the location of this file, since it's part of the project
    // template and its location is fixed.
    $project_root = dirname(__FILE__, 2);
    $drupal_root = $project_root . '/web';
    chdir($drupal_root);

    $request = Request::createFromGlobals();
    // Manually resemble early bootstrap of DrupalKernel::boot().
    DrupalKernel::bootEnvironment();
    Settings::initialize($drupal_root, DrupalKernel::findSitePath($request), $this->classLoader);
    require_once $drupal_root . '/core/includes/utility.inc';
    drupal_rebuild($this->classLoader, $request);
    $io->success('All caches have been rebuilt.');

    // Restores the current working directory of the command to the app root.
    chdir($cwd);

    return Command::SUCCESS;
  }

}
