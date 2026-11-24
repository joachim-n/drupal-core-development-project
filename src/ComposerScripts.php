<?php

namespace DrupalGitCloneProject;

use Composer\Script\Event;

// Development: this makes symfony var-dumper work.
// See https://github.com/composer/composer/issues/7911
// include_once './vendor/symfony/var-dumper/Resources/functions/dump.php';

/**
 * Provides Composer scripts for setting up Drupal from a git clone.
 */
class ComposerScripts {

  /**
   * Clones Drupal core if it's not already in repos/drupal.
   *
   * Script for the 'post-root-package-install' event.
   */
  public static function postRootPackageInstall(Event $event) {
    if (!file_exists('repos/drupal/.git')) {
      $io = $event->getIO();

      if (!file_exists('repos')) {
        $io->write("Creating 'repos/' directory within the project directory.");

        mkdir('repos');
      }

      $project_root = getcwd();
      chdir('repos');

      $io->write("Cloning Drupal core into 'repos/drupal'.");

      system('git clone https://git.drupalcode.org/project/drupal.git');

      // Restore the working directory to the project root.
      chdir($project_root);
    }
  }

  /**
   * Sets up files and symlinks after installation.
   *
   * See README for details.
   */
  public static function postDrupalScaffold() {
    // Development: this makes symfony var-dumper work.
    // See https://github.com/composer/composer/issues/7911
    include './vendor/symfony/var-dumper/Resources/functions/dump.php';

    // Apply a patch to the scaffold index.php file.
    // See https://www.drupal.org/project/drupal/issues/3188703
    // See https://www.drupal.org/project/drupal/issues/1792310
    chdir('web');
    // Check the index.php file to see which patches to apply. On Drupal 12,
    // the front controllers have changed to use Symfony runtime.
    if (str_contains(file_get_contents('index.php'), 'autoload_runtime')) {
      static::applyPatch('scaffold-patch-index-php-12.patch');
      static::applyPatch('scaffold-patch-update-php-12.patch');
    }
    else {
      static::applyPatch('scaffold-patch-index-php.patch');
      static::applyPatch('scaffold-patch-update-php.patch');
    }

    chdir('..');
    // Symlink the top-level vendor folder into the Drupal core git repo.
    static::makeSymlink('../../vendor', 'repos/drupal/vendor');

    // Symlink the libraries folder into the Drupal core git repo.
    static::makeSymlink('../../web/libraries', 'repos/drupal/libraries');

    // Symlink external and custom folders into the Drupal core git repo.
    foreach (['modules', 'profiles', 'sites', 'themes'] as $dir) {
      foreach (scandir("web/$dir") as $sub) {
        if (!file_exists("repos/drupal/$dir/$sub") && is_dir("web/$dir/$sub")) {
          static::makeSymlink("../../../web/$dir/$sub", "repos/drupal/$dir/$sub");
        }
      }
    }

    // Symlink sites files and folders into the the Drupal core git repo.
    foreach (scandir('web/sites') as $file) {
      if (!file_exists("repos/drupal/sites/$file")) {
        static::makeSymlink("../../../web/sites/$file", "repos/drupal/sites/$file");
      }
    }
    foreach (scandir('web/sites/default') as $file) {
      if (!file_exists("repos/drupal/sites/default/$file")) {
        static::makeSymlink("../../../../web/sites/default/$file", "repos/drupal/sites/default/$file");
      }
    }

    // Create folders for running tests.
    if (!file_exists('web/sites/simpletest')) {
      mkdir('web/sites/simpletest', 0777, TRUE);
    }
    if (!file_exists('web/sites/simpletest/browser_output')) {
      mkdir('web/sites/simpletest/browser_output', 0777, TRUE);
    }

    // Symlink the simpletest folder into the Drupal core git repo.
    static::makeSymlink('../../../web/sites/simpletest', 'repos/drupal/sites/simpletest');
  }

  /**
   * Applies a scaffold patch.
   *
   * The working directory is expected to be '/web'.
   *
   * @param string $patch_file
   *   The name of a patch file in the '/scaffold' directory.
   *
   * @throws \LogicException
   *   Throws an exception if the patch fails to apply.
   */
  protected static function applyPatch(string $patch_file): void {
    $output = NULL;
    $result_code = NULL;
    exec('patch -p1 <../scaffold/' . $patch_file, $output, $result_code);
    if ($result_code == 1) {
      throw new \LogicException("Patch '{$patch_file}' failed.");
    }
  }

  /**
   * Creates a symlink if one is not already present.
   *
   * Prints a warning if the symlink exists but does not link to the correct
   * target.
   *
   * @param string $target
   *   The target to link to, as a relative path to the symlink.
   * @param string $link
   *   The new link to create.
   */
  protected static function makeSymlink($target, $link) {
    if (file_exists($link)) {
      if (!is_link($link)) {
        print("WARNING: {$link} exists already and is not a symlink.\n");

        return;
      }

      // Use realpath() on the target in case the symlink is absolute while the
      // expected target is relative.
      if (readlink($link) != $target) {
        $actual_target = readlink($link);
        print("WARNING: {$link} exists already and is incorrectly symlinked to {$actual_target} instead of {$target}.\n");
      }
    }
    else {
      symlink($target, $link);
    }
  }

}
