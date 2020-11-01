<?php

/**
 * Test: CsvStream
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


function getMyTempDir() {
	return getTempDir() . '/csv';
}


before(function () {
	Tester\Helpers::purge(getMyTempDir());
});


after(function () {
	Tester\Helpers::purge(getMyTempDir());
});


test('', function () {
	$tempDir = getMyTempDir();
	$file = $tempDir . '/test.csv';

	$rows = [];
	$rows[] = [
		'Date',
		'User',
		'Message',
	];
	$rows[] = [
		'2020-10-28',
		'John Doe',
		'Hello world!',
	];

	$writer = new Universe\FileSystem\CsvStreamWriter();
	$writer->open($file);

	foreach($rows as $row) {
		$writer->write($row);
	}
	$writer->close();

	$reader = new Universe\FileSystem\CsvStreamReader();
	$reader->open($file);

	$i = 0;
	while($data = $reader->read()) {
		foreach($rows[$i] as $key => $value) {
			Assert::equal($data[$key], $value);
		}
		$i++;
	}

	$reader->close();
});