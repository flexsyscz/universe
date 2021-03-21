<?php
declare(strict_types=1);

namespace Flexsyscz\Universe\Model;

use Nette;
use Nextras\Orm\Entity\ImmutableValuePropertyWrapper;


/**
 * Class JsonWrapper
 * @package Flexsyscz\Universe\Model
 */
class JsonWrapper extends ImmutableValuePropertyWrapper
{
	/**
	 * @param mixed $value
	 * @return mixed|string|null
	 * @throws Nette\Utils\JsonException
	 */
	public function convertToRawValue($value)
	{
		return is_scalar($value) ? Nette\Utils\Json::encode($value) : null;
	}


	/**
	 * @param mixed $value
	 * @return mixed|null
	 * @throws Nette\Utils\JsonException
	 */
	public function convertFromRawValue($value)
	{
		return is_string($value) ? Nette\Utils\Json::decode($value) : null;
	}
}
