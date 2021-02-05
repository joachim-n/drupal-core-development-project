<?php

namespace DrupalGitCloneProject;

class ComposerScripts {

  /**
   * Sets up files and symlinks after installation.
   *
   * See README for details.
   */
  public static function postDrupalScaffold() {
    // Apply a patch to the scaffold index.php file.
    // See https://www.drupal.org/project/drupal/issues/3188703
    chdir('web');
    shell_exec('patch -p1 <../scaffold/scaffold-patch-index-php.patch');

    // Symlink the top-level vendor folder into the Drupal core git repo.
    chdir('..');
    static::makeSymlink('../../vendor', 'repos/drupal/vendor');

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
      if (!is_link($link) || readlink($link) != $target) {
        print("WARNING: $link exists already and is not correctly symlinked to $target.");
      }
    }
    else {
      symlink($target, $link);
    }
  }

}