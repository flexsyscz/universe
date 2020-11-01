<?php

/**
 * Test: FileManager
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


function getMyTempDir() {
	return getTempDir() . '/storage';
}


before(function () {
	$tempDir = getMyTempDir();
	Tester\Helpers::purge($tempDir);

	Nette\Utils\FileSystem::createDir($tempDir . '/FileManager');
	Nette\Utils\FileSystem::createDir($tempDir . '/FileManager/data');
	Nette\Utils\FileSystem::createDir($tempDir . '/FileManager/images');
});


after(function () {
	Tester\Helpers::purge(getMyTempDir());
});


test('', function () {
	$tempDir = getMyTempDir();
	$dataDir = $tempDir . '/FileManager/data';
	$imagesDir = $tempDir . '/FileManager/images';

	$fileManager = new Universe\FileSystem\FileManager([
		'data' => $dataDir,
		'images' => $imagesDir,
	]);

	Assert::same($dataDir, $fileManager->getPartitionPath('data'));
	Assert::same($imagesDir, $fileManager->getPartitionPath('images'));

	Assert::exception(function() use ($fileManager) {
		$fileManager->use('null');
	}, Universe\Exceptions\PartitionNotFoundException::class);

	Assert::exception(function() use ($fileManager) {
		$fileManager->addPartition('data', __DIR__);
	}, Universe\Exceptions\DuplicatePartitionException::class);

	$file = 'text.txt';
	$text = 'Hello world';
	$fileManager->use('data')
		->write($file, $text);
	Assert::same($text, $fileManager->read($file));

	$copyFile = 'text-copy.txt';
	$fileManager->copy($file, $copyFile);
	Assert::same($text, $fileManager->read($copyFile));

	$renameFile = 'rename.txt';
	$fileManager->rename($copyFile, $renameFile);
	Assert::same($text, $fileManager->read($renameFile));

	Assert::exception(function() use ($fileManager, $copyFile) {
		$fileManager->read($copyFile);
	}, \Nette\IOException::class);

	$fileManager->delete($file);
	Assert::exception(function() use ($fileManager, $file) {
		$fileManager->read($file);
	}, \Nette\IOException::class);

	Assert::true(in_array($renameFile, $fileManager->scan('data'), true));
	Assert::equal(0, count($fileManager->scan('images')));

	$fileManager->move($renameFile, $renameFile, true, 'images');
	Assert::equal(1, count($fileManager->scan('images')));
	$fileManager->use('images');
	Assert::same($text, $fileManager->read($renameFile));
});