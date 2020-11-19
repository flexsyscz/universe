<?php

/**
 * Test: DateTimeProvider
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class LanguageType extends \MabeEnum\Enum
{
	const CZECH = 'cs_CZ';
	const ENGLISH = 'en_US';
}


function getMyLogDir() {
	return getLogDir() . '/dateTimeProvider';
}


before(function () {
	Tester\Helpers::purge(getMyLogDir());
});


test('', function () {
	$logger = new \Tracy\Logger(getMyLogDir());
	$translator = new Universe\Localization\Translator(LanguageType::CZECH, [
		LanguageType::CZECH => __DIR__ . '/../resources/cs_CZ.neon',
		LanguageType::ENGLISH => __DIR__ . '/../resources/en_US.neon',
	], $logger);

	$dateTimeProvider = new Universe\Utils\DateTimeProvider($translator);
	$now = Universe\Utils\DateTimeProvider::now();

	Assert::true($now instanceof DateTimeImmutable);
	Assert::equal('před chvílí', $dateTimeProvider->ago($now->modify('-1 seconds')));
	Assert::equal('před chvílí', $dateTimeProvider->ago($now->modify('-4 seconds')));
	Assert::equal('před 5 vteřinami', $dateTimeProvider->ago($now->modify('-5 seconds')));

	$translator->setLanguage(LanguageType::ENGLISH);
	Assert::equal('few seconds ago', $dateTimeProvider->ago($now->modify('-1 seconds')));
	Assert::equal('few seconds ago', $dateTimeProvider->ago($now->modify('-3 seconds')));
	Assert::equal('7 seconds ago', $dateTimeProvider->ago($now->modify('-7 seconds')));
});