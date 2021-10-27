<?php

/**
 * Test: Directories
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';

function getMyTempDir() {
	return getTempDir() . '/dirs';
}


before(function () {
	Tester\Helpers::purge(getMyTempDir());
});


after(function () {
	Tester\Helpers::purge(getMyTempDir());
});


test('', function () {
	$tempDir = getMyTempDir();
	$fileManager = new Universe\FileSystem\FileManager();

	$_appDir = new Universe\FileSystem\Directories\AppDirectory($tempDir, $tempDir . 'app', $fileManager);
	$_tempDir = new Universe\FileSystem\Directories\TempDirectory($tempDir, $tempDir . 'temp', $fileManager);
	$_wwwDir = new Universe\FileSystem\Directories\WwwDirectory($tempDir, $tempDir . 'www', $fileManager);
	$_dataDir = new Universe\FileSystem\Directories\DataDirectory($tempDir, $tempDir . '/www/data', $fileManager);

	Assert::equal($tempDir . '/www/data', $_dataDir->getAbsolutePath());
	Assert::equal('www/data', $_dataDir->getRelativePath());
});
