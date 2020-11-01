<?php

/**
 * Test: DateTimeProvider
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test('', function () {
	Assert::true(Universe\Utils\DateTimeProvider::now() instanceof DateTimeImmutable);
});