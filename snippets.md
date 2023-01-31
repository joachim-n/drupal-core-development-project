
# DDEV Guide

php /var/www/html/core/scripts/run-tests.sh --color --keep-results --types "PHPUnit-Build" --concurrency "15" --repeat "1" --sqlite "/var/lib/drupalci/workdir/run_tests.build/simpletest.sqlite" --dburl "mysql://drupaltestbot:drupaltestbotpw@172.18.0.4/jenkins_drupal_patches_156878" --url "http://php-apache-jenkins-drupal-patches-156878/subdirectory" --all

php /var/www/html/repos/drupal/core/scripts/run-tests.sh --color --keep-results --types "PHPUnit-Build" --concurrency "15" --repeat "1" --sqlite "/var/www/html/web/sites/simpletest/browser_output/simpletest.sqlite" --dburl "mysql://drupaltestbot:drupaltestbotpw@172.18.0.4/jenkins_drupal_patches_156878" --url "http://php-apache-jenkins-drupal-patches-156878/subdirectory" --all

================================================================================

# Snippets

/**
* Tests that deprecations are raised for missing constructor arguments.
*
* @group legacy
*/
public function testConstructorDeprecations(): void {

$container = new ContainerBuilder();
$container->set('current_user', $this->createMock(AccountProxy::class));
\Drupal::setContainer($container);

$this->expectDeprecation('Calling ' . ForumManager::class . '::__construct() without the $current_user argument is deprecated in drupal:10.1.0 and will be required before drupal:11.0.0. See https://www.drupal.org/node/145353.');

}


/**
* Tests getLastPost() method is deprecated.
*
* @covers \Drupal\forum\ForumManager::getLastPost()
* @group legacy
*/
public function testgetLastPostMethodDeprecation(): void {

$this->expectDeprecation(ForumManager::class . '::getLastPost() is deprecated in drupal:10.1.0 and is removed from drupal:11.0.0. Use getLastPostData() instead. See https://www.drupal.org/node/145353.');

$this->assertIsObject($this->container->get('forum_manager')->getLastPost(1));

}


7643161492..adfa35d41f  10.1.x     -> origin/10.1.x
Updating 7643161492..adfa35d41f
