<?php

namespace Flexsyscz\Universe\Utils;


/**
 * Class Sorting
 * @package Flexsys\Universe\Utils
 */
class Sorting
{
	/**
	 * @param array<mixed> $arr
	 * @param bool $numeric
	 * @return array<mixed>
	 */
	public static function asort(array $arr, bool $numeric = false): array
	{
		uasort($arr, function($a, $b) use ($numeric) {
			return $numeric ? strnatcmp($a, $b) : strcoll($a, $b);
		});

		return $arr;
	}


	/**
	 * @param array<mixed> $arr
	 * @param bool $numeric
	 * @return array<mixed>
	 */
	public static function ksort(array $arr, bool $numeric = false): array
	{
		uksort($arr, function($a, $b) use ($numeric) {
			return $numeric ? strnatcmp($a, $b) : strcoll($a, $b);
		});

		return $arr;
	}


	/**
	 * @param array<mixed> $arr
	 * @param bool $numeric
	 * @return array<mixed>
	 */
	public static function arsort(array $arr, bool $numeric = false): array
	{
		return array_reverse(self::asort($arr, $numeric), true);
	}


	/**
	 * @param array<mixed> $arr
	 * @param bool $numeric
	 * @return array<mixed>
	 */
	public static function krsort(array $arr, bool $numeric = false): array
	{
		return array_reverse(self::ksort($arr, $numeric), true);
	}
}