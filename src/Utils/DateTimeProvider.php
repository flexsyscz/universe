<?php declare(strict_types = 1);

namespace Flexsyscz\Universe\Utils;

use Flexsyscz\Universe\Exceptions\InvalidDateTimeException;
use Flexsyscz\Universe\Localization\Translator;
use Nextras\Dbal\Utils\DateTimeImmutable;


/**
 * Class DateTimeProvider
 * @package Flexsyscz\Universe\Utils
 */
class DateTimeProvider
{
	/** @var Translator */
	private $translator;


	/**
	 * DateTimeProvider constructor.
	 * @param Translator $translator
	 */
	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}


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


	/**
	 * @param DateTimeImmutable $ago
	 * @return string
	 */
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
		foreach($params as $k => $n) {
			$values[$n] = isset($diff->$k) ? $diff->$k : 0;
			if($k === 'w') {
				$values[$n] = intval(floor($diff->d / 7));
			}
		}

		foreach($values as $n => $v) {
			if ($v > 0) {
				$count = $v >= 5 ? $v : ($v === 1 ? 1 : 2);
				return $this->translator->translate(sprintf('flexsyscz.DateTimeProvider.ago.%s', $n), $count);
			}
		}

		return $this->translator->translate('flexsyscz.DateTimeProvider.ago.justNow');
	}
}