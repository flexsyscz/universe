<?php

/**
 * Test: Message
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../../bootstrap.php';


test('', function () {
	$text = 'Hello world!';
	$name = 'Alert';
	$message = new \Flexsyscz\UI\Messaging\Message($text, $name);

	Assert::equal($text, $message->text);
	Assert::equal($name, $message->name);
});