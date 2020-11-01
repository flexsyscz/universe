<?php

/**
 * Test: SerializationWrapper
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/Users/UserType.php';
require __DIR__ . '/Users/User.php';
require __DIR__ . '/Users/UsersMapper.php';
require __DIR__ . '/Users/UsersRepository.php';
require __DIR__ . '/Orm.php';


function getMyTempDir() {
	return getTempDir() . '/model/serialization';
}


before(function () {
	if(file_exists(getTempDir() . '/model')) {
		Tester\Helpers::purge(getMyTempDir());
	}
});


after(function () {
	Tester\Helpers::purge(getMyTempDir());
});


test('', function () {
	$configurator = new \Nette\Configurator();
	$configurator->addConfig(__DIR__ . '/config/dbal.neon')
		->addConfig(__DIR__ . '/config/local.neon');

	$configurator->setTempDirectory(getMyTempDir());
	$container = $configurator->createContainer();
	$orm = $container->getByType(\Tests\Model\Orm::class);

	$users = $orm->users->findAll();
	foreach($users->fetchAll() as $user) {
		if($user instanceof \Tests\Model\User) {
			Assert::equal($user->metadata['created'], '2020-10-28 12:04');
			Assert::equal($user->metadata['updated'], '2020-11-01 11:21');
			Assert::equal($user->metadata['photo'], null);
		}
	}
});