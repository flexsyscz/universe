<?php declare(strict_types = 1);

namespace Flexsyscz\Universe\Utils;

use Flexsyscz\Universe\Exceptions\InvalidDateTimeException;
use Nextras\Dbal\Utils\DateTimeImmutable;


/**
 * Class DateTimeProvider
 * @package Flexsyscz\Universe\Utils
 */
class DateTimeProvider
{
	/**
	 * @return DateTimeImmutable
	 */
	public static function now(): DateTimeImmutable
	{
		try {
			$now = new DateTimeImmutable();
		} catch (\Exception $e) {
			$now = DateTimeImmutable::createFromFormat('c', date('c'));
			if($now === false) {
				throw new InvalidDateTimeException();
			}
		}

		return $now;
	}
}