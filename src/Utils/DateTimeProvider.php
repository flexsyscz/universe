<?php

declare(strict_types=1);

namespace Flexsyscz\Universe\Utils;

use Flexsyscz\Universe\Exceptions\InvalidDateTimeException;
use Flexsyscz\Universe\Localization\TranslatedComponent;
use Nextras\Dbal\Utils\DateTimeImmutable;


/**
 * Class DateTimeProvider
 * @package Flexsyscz\Universe\Utils
 */
class DateTimeProvider
{
	use TranslatedComponent;

	public static function now(): DateTimeImmutable
	{
		try {
			$now = new DateTimeImmutable;
		} catch (\Exception $e) {
			$now = DateTimeImmutable::createFromFormat('c', date('c'));
			if ($now === false) {
				throw new InvalidDateTimeException;
			}
		}

		return $now;
	}


	public function ago(DateTimeImmutable $ago): string
	{
		$params = [
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		];

		$diff = self::now()->diff($ago);
		$values = [];
		foreach ($params as $k => $n) {
			if ($k === 'w') {
				$values[$n] = (int) (floor($diff->d / 7));
			} else {
				$values[$n] = $diff->$k ?? 0;
			}
		}

		foreach ($values as $n => $v) {
			if ($v > 0) {
				$count = $v >= 5 ? $v : ($v === 1 ? 1 : 2);
				return $this->translatorNamespace->translate(sprintf('ago.%s', $n), $count);
			}
		}

		return $this->translatorNamespace->translate('ago.justNow');
	}
}
