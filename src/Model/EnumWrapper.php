<?php
declare(strict_types=1);

namespace Flexsyscz\Universe\Model;

use Flexsyscz\Universe\Exceptions\InvalidArgumentException;
use MabeEnum\Enum;
use Nette\Utils\Callback;
use Nextras\Orm\Entity\ImmutableValuePropertyWrapper;
use Nextras\Orm\Entity\Reflection\PropertyMetadata;


/**
 * Class EnumWrapper
 * @package Flexsys\Universe\Model
 */
class EnumWrapper extends ImmutableValuePropertyWrapper
{
	/** @var string */
	private $enumClass;


	/**
	 * EnumWrapper constructor.
	 * @param PropertyMetadata $propertyMetadata
	 */
	public function __construct(PropertyMetadata $propertyMetadata)
	{
		parent::__construct($propertyMetadata);

		if (count($propertyMetadata->types) !== 1) {
			throw new InvalidArgumentException('Invalid count of types.');
		}

		$this->enumClass = key($propertyMetadata->types);
		if (!class_exists($this->enumClass)) {
			throw new InvalidArgumentException(sprintf('Class %s not found.', $this->enumClass));
		}
	}


	/**
	 * @param mixed $value
	 * @return array|bool|float|int|mixed|string|null
	 */
	public function convertToRawValue($value)
	{
		if ($value instanceof Enum) {
			return $value->getValue();
		}

		return null;
	}


	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function convertFromRawValue($value)
	{
		$enumClass = $this->enumClass;
		return $value === null
			? null
			: forward_static_call(Callback::check([$enumClass, 'byValue']), $value);
	}
}
