<?php

/**
 * Test: Translator
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
	return getLogDir() . '/translator';
}


before(function () {
	Tester\Helpers::purge(getMyLogDir());
});


test('', function () {
	$logger = new \Tracy\Logger(getMyLogDir());
	$translator = new Universe\Localization\Translator(true, LanguageType::CZECH, [
		LanguageType::CZECH => __DIR__ . '/../resources/cs_CZ.neon',
		LanguageType::ENGLISH => __DIR__ . '/../resources/en_US.neon',
	], $logger);

	Assert::equal($translator->translate('messages.error.userNotFound'), 'Uživatel nenalezen.');
	Assert::equal($translator->translate('content.homepage.header'), 'Dobrý den!');
	Assert::equal($translator->translate('content.homepage.description.part3'), 'Dobrý den!');
	Assert::equal($translator->translate('content.homepage.description.part4.part2'), 'et dolores el simet 2');

	Assert::equal($translator->translate('content.homepage.description.part5'), 'content.homepage.description.part5');


	$translator->setLanguage(LanguageType::ENGLISH)
		->translate('content.homepage.header', 'Hello world!');

	Assert::equal($translator->translate('content.homepage.description.part3'), 'Hello world!');
	Assert::equal($translator->translate('content.homepage.description.part4.part2'), 'et dolores el simet 2');

	Assert::false(file_exists(getLogDir() . '/error.log'));
});


test('loggerOnStartup', function () {
	$logger = new \Tracy\Logger(getMyLogDir());
	$translator = new Universe\Localization\Translator(true, LanguageType::CZECH, [
		LanguageType::CZECH => __DIR__ . '/../resources/cs_CZ_wrong.neon',
		LanguageType::ENGLISH => __DIR__ . '/../resources/en_US_wrong.neon',
	], $logger);

	Assert::true(file_exists(getMyLogDir() . '/error.log'));
});


test('loggerOnTranslation', function () {
	$logger = new \Tracy\Logger(getMyLogDir());
	$translator = new Universe\Localization\Translator(true, LanguageType::CZECH, [
		LanguageType::CZECH => __DIR__ . '/../resources/cs_CZ.neon',
		LanguageType::ENGLISH => __DIR__ . '/../resources/en_US.neon',
	], $logger);

	$translator->translate('messages.error.accessDenied_');
	Assert::true(file_exists(getMyLogDir() . '/error.log'));
});


test('loggerOnMaxFollowingsExceeded', function () {
	$logger = new \Tracy\Logger(getMyLogDir());
	$translator = new Universe\Localization\Translator(true,LanguageType::CZECH, [
		LanguageType::CZECH => __DIR__ . '/../resources/cs_CZ.neon',
		LanguageType::ENGLISH => __DIR__ . '/../resources/en_US.neon',
	], $logger);

	$translator->translate('content.homepage.description.part5');
	Assert::true(file_exists(getMyLogDir() . '/error.log'));
});