<?php declare(strict_types=1);

namespace Flexsyscz\Universe\Localization;


/**
 * Class Convertor
 * @package Flexsyscz\Universe\Localization
 */
class Convertor
{
	/**
	 * @param $input
	 * @param string $charset
	 * @param bool $transliteration
	 * @return array|false|string
	 */
	public static function convert($input, string $charset = 'windows-1250', bool $transliteration = true)
	{
		if(is_array($input)) {
			foreach ($input as $key => $value) {
				$input[$key] = iconv('utf-8', sprintf('%s%s', $charset, $transliteration ? '//TRANSLIT' : ''), $value);
			}

			return $input;
		}

		return iconv('utf-8', sprintf('%s%s', $charset, $transliteration ? '//TRANSLIT' : ''), $input);
	}
}