<?php

/**
 * Test: Sorting
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test('', function () {
	$a = ['Český', 'Řecký', 'Španělský'];
	$b = array_reverse($a, true);
	$c = Universe\Utils\Sorting::arsort($a);

	foreach($c as $i => $v) {
		Assert::equal($v, $b[$i]);
	}

	$d = Universe\Utils\Sorting::asort($c);
	foreach($d as $i => $v) {
		Assert::equal($v, $a[$i]);
	}

	$a = ['Řecký' => 'A', 'Český' => 'B', 'Španělský' => 'C'];
	$b = ['Český' => 'B', 'Řecký' => 'A', 'Španělský' => 'C'];
	$e = ['Španělský' => 'C', 'Řecký' => 'A', 'Český' => 'B'];

	$c = Universe\Utils\Sorting::ksort($a);
	foreach($c as $i => $v) {
		Assert::equal($v, $b[$i]);
	}

	$notEqual = false;
	for($i = 0; $i < count($c); $i++) {
		if(array_keys($c)[$i] !== array_keys($a)[$i]) {
			$notEqual = true;
			break;
		}
	}
	Assert::true($notEqual);

	$d = Universe\Utils\Sorting::krsort($c);
	for($i = 0; $i < count($d); $i++) {
		Assert::equal(array_keys($d)[$i], array_keys($e)[$i]);
	}
});