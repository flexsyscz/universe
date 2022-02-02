<?php

declare(strict_types=1);

namespace Flexsyscz\UI\Messaging;

use Flexsyscz\Universe\Utils\DateTimeProvider;
use Nette\SmartObject;
use Nextras\Dbal\Utils\DateTimeImmutable;


/**
 * Class Message
 * @package Flexsyscz\UI\Messaging
 *
 * @property-read string 				$text
 * @property-read string|null 			$name
 * @property-read DateTimeImmutable 	$created
 */
class Message
{
	use SmartObject;

	/** @var string */
	private $text;

	/** @var string|null */
	private $name;

	/** @var DateTimeImmutable */
	private $created;


	/**
	 * Message constructor.
	 * @param string $text
	 * @param string|null $name
	 */
	public function __construct(string $text, string $name = null)
	{
		$this->text = $text;
		$this->name = $name;
		$this->created = DateTimeProvider::now();
	}


	public function getText(): string
	{
		return $this->text;
	}


	public function getName(): ?string
	{
		return $this->name;
	}


	public function getCreated(): DateTimeImmutable
	{
		return $this->created;
	}
}
