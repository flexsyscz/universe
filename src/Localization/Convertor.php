<?php

declare(strict_types=1);

namespace Flexsyscz\Universe\Localization;


/**
 * Class Convertor
 * @package Flexsyscz\Universe\Localization
 */
class Convertor
{
	public const IN = 'in';

	public const OUT = 'out';


	/**
	 * @param string|array<string> $input
	 * @param string $charset
	 * @param bool $transliteration
	 * @param string $direction
	 * @return array<string|false>|false|string
	 */
	public static function convert(
		$input,
		string $charset = 'windows-1250',
		bool $transliteration = true,
		string $direction = self::OUT
	) {
		if ($direction === self::IN) {
			$from = sprintf('%s%s', $charset, $transliteration ? '//TRANSLIT' : '');
			$to = 'utf-8';
		} else {
			$from = 'utf-8';
			$to = sprintf('%s%s', $charset, $transliteration ? '//TRANSLIT' : '');
		}

		if (is_array($input)) {
			foreach ($input as $key => $value) {
				$input[$key] = iconv($from, $to, is_string($value) ? $value : '');
			}

			return $input;
		}

		return iconv($from, $to, $input);
	}


	/**
	 * @param string|array<string> $input
	 * @param string $charset
	 * @param bool $transliteration
	 * @return array<string|false>|false|string
	 */
	public static function convertIn($input, string $charset = 'windows-1250', bool $transliteration = true)
	{
		return self::convert($input, $charset, $transliteration, self::IN);
	}
}
