<?php

namespace DrupalGitCloneProject;

use Composer\Package\Link;
use Composer\Package\CompletePackage;
use Composer\Package\PackageInterface;
use Composer\Plugin\PrePoolCreateEvent;
use Composer\Semver\VersionParser;

// Development: this makes symfony var-dumper work.
// See https://github.com/composer/composer/issues/7911
// include_once './vendor/symfony/var-dumper/Resources/functions/dump.php';

/**
 * Composer script to change dependencies in drupal core to be lenient.
 *
 * This is to allow `composer install` to work when the drupal core repository
 * is checked out to an issue branch.
 */
class ComposerCoreDependenciesLeniency {

  /**
   * Act on pre-pool-create event.
   *
   * Tweaks the drupal/core requirement of packages, so that Composer will
   * install them when the Drupal core git clone is checked to an issue branch.
   *
   * @param \Composer\Plugin\PrePoolCreateEvent $event
   *   The event.
   */
  public static function prePoolCreate(PrePoolCreateEvent $event) {
    $packages = $event->getPackages();

    $packages = $event->getPackages();

    /** @var \Composer\Package\PackageInterface $package */
    foreach ($packages as $package) {
      $name = $package->getName();

      if ($name == 'drupal/core-recommended') {
        static::makeDrupalCoreRequireWild($package);
      }

      $type = $package->getType();
      if ($type === 'drupal-core') {
        continue;
      }
    }
  }

  /**
   * Changes a package's requirement for drupal/core to be '*'.
   *
   * @param \Composer\Package\PackageInterface $package
   *   The package to change.
   */
  protected static function makeDrupalCoreRequireWild(PackageInterface $package): void {
    $version_parser = new VersionParser();

    $requires = $package->getRequires();

    if (!isset($requires['drupal/core'])) {
      return;
    }

    $drupal_core_require = $requires['drupal/core'];

    // Replace the requirement with a lenient one.
    $requires['drupal/core'] = new Link(
      $drupal_core_require->getSource(),
      $drupal_core_require->getTarget(),
      $version_parser->parseConstraints('*'),
      $drupal_core_require->getDescription(),
      '*',
    );

    // No idea why we need to check CompletePackage, just aping
    // mglaman/composer-drupal-lenient.
    if ($package instanceof CompletePackage) {
      // @note `setRequires` is on Package but not PackageInterface.
      $package->setRequires($requires);
    }
  }

}
