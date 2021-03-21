<?php

/**
 * Test: Uploads
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


function getMyTempDir() {
	return getTempDir() . '/uploads';
}


before(function () {
	$tempDir = getMyTempDir();
	Tester\Helpers::purge($tempDir);

	Nette\Utils\FileSystem::createDir($tempDir . '/images');
});


after(function () {
	Tester\Helpers::purge(getMyTempDir());
});


test('', function () {
	$tempDir = getMyTempDir();
	$imagesDir = $tempDir . '/images';

	$fileManager = new Universe\FileSystem\FileManager([
		'images' => $imagesDir,
	]);

	$testFile = __DIR__ . '/../resources/uploads/test.jpg';
	$upload = new \Nette\Http\FileUpload([
		'name' => basename($testFile),
		'size' => filesize($testFile),
		'tmp_name' => $testFile,
		'error' => 0,
	]);

	$receiver = new Universe\FileSystem\Receiver();
	$receiver->save($upload, function(Universe\FileSystem\Uploads\Container $container) use ($fileManager) {
		Assert::true($container->isImage());

		$fileManager->use('images');
		$image = $container->getOptimizedImage(1600);
		$path = $fileManager->absolutePath('new-image.jpg');

		$image->save($path, 80, \Nette\Utils\Image::WEBP);
		Assert::true(file_exists($path));
	});
});