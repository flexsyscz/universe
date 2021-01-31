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


function getConfig() {
	return [
		'debugMode' => true,
		'default' => LanguageType::CZECH,
		'fallback' => LanguageType::ENGLISH,
		'languages' => [
			LanguageType::CZECH => __DIR__ . '/../resources/cs_CZ.neon',
			LanguageType::ENGLISH => __DIR__ . '/../resources/en_US.neon',
		],
	];
}


before(function () {
	Tester\Helpers::purge(getMyLogDir());
});


test('', function () {
	$config = getConfig();
	$logger = new \Tracy\Logger(getMyLogDir());
	$translator = new Universe\Localization\Translator($config, $logger);

	Assert::equal($translator->translate('messages.error.userNotFound'), 'Uživatel nenalezen.');
	Assert::equal($translator->translate('content.homepage.header'), 'Dobrý den!');
	Assert::equal($translator->translate('content.homepage.description.part3'), 'Dobrý den!');
	Assert::equal($translator->translate('content.homepage.description.part4.part2'), 'et dolores el simet 2');

	Assert::equal($translator->translate('content.homepage.description.part5'), 'content.homepage.description.part5');

	Assert::equal($translator->translate('parent.title'), 'Titulek předka');
	Assert::equal($translator->translate('!default.parent.title'), 'Titulek předka');


	$translator->setLanguage(LanguageType::ENGLISH)
		->translate('content.homepage.header', 'Hello world!');

	Assert::equal($translator->translate('content.homepage.description.part3'), 'Hello world!');
	Assert::equal($translator->translate('content.homepage.description.part4.part2'), 'et dolores el simet 2');

	Assert::equal($translator->translate('parent.title'), 'Parent title');

	Assert::false(file_exists(getLogDir() . '/error.log'));
});


test('translatedComponent', function () {
	$config = getConfig();
	$logger = new \Tracy\Logger(getMyLogDir());
	$translator = new Universe\Localization\Translator($config, $logger);

	$ns = 'myComponent';
	$translator->addDirectory(__DIR__ . '/../resources/someComponentTranslations', $ns)
		->setNamespace($ns);

	Assert::equal($translator->translate('hello'), 'ahoj');
	Assert::equal($translator->translate('componentName'), 'toto je název komponenty!');

	$translator->setLanguage(LanguageType::ENGLISH);
	Assert::equal($translator->translate('hello'), 'hello world!');
	Assert::equal($translator->translate('componentName'), 'my component name!');
});


test('loggerOnStartup', function () {
	$config = getConfig();
	$config['languages'][LanguageType::CZECH] = __DIR__ . '/../resources/cs_CZ_wrong.neon';
	$config['languages'][LanguageType::ENGLISH] = __DIR__ . '/../resources/en_US_wrong.neon';

	$logger = new \Tracy\Logger(getMyLogDir());
	new Universe\Localization\Translator($config, $logger);

	Assert::true(file_exists(getMyLogDir() . '/error.log'));
});


test('loggerOnTranslation', function () {
	$config = getConfig();
	$logger = new \Tracy\Logger(getMyLogDir());
	$translator = new Universe\Localization\Translator($config, $logger);

	$translator->translate('messages.error.accessDenied_');
	Assert::true(file_exists(getMyLogDir() . '/error.log'));
});


test('loggerOnMaxFollowingsExceeded', function () {
	$config = getConfig();
	$logger = new \Tracy\Logger(getMyLogDir());
	$translator = new Universe\Localization\Translator($config, $logger);

	$translator->translate('content.homepage.description.part5');
	Assert::true(file_exists(getMyLogDir() . '/error.log'));
});