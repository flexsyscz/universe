<?php

/**
 * Test: Convertor
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test('', function () {
	$accents = 'ěščřžýáíéĚŠČŘŽÝÁÍÉ';

	$converted = Universe\Localization\Convertor::convert([
		'Hello world',
		$accents,
		'Lorem ipsum',
		'There is 1 story',
		$accents,
	]);

	$expected = iconv('utf-8', 'windows-1250', $accents);

	Assert::equal($converted[1], $expected);
	Assert::equal($converted[4], $expected);
	Assert::equal(Universe\Localization\Convertor::convert($accents), $expected);

	$convertedIn = Universe\Localization\Convertor::convertIn($converted);
	$accentsIn = iconv('windows-1250', 'utf-8', $expected);
	Assert::equal($convertedIn[1], $accentsIn);
	Assert::equal($convertedIn[4], $accentsIn);
	Assert::equal(Universe\Localization\Convertor::convertIn($expected), $accentsIn);
});