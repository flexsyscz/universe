<?php declare(strict_types=1);

namespace Flexsyscz\Universe\Model;

use Nextras\Orm\Entity\ImmutableValuePropertyWrapper;


/**
 * Class SerializationWrapper
 * @package Flexsyscz\Universe\Model
 */
class SerializationWrapper extends ImmutableValuePropertyWrapper
{
	/**
	 * @param mixed $value
	 * @return string|null
	 */
	public function convertToRawValue($value)
	{
		return is_array($value) ? serialize($value) : null;
	}


	/**
	 * @param mixed $value
	 * @return array<mixed>
	 */
	public function convertFromRawValue($value)
	{
		return is_string($value) ? @unserialize($value) : null;
	}
}