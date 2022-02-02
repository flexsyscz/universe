<?php

declare(strict_types=1);

namespace Flexsyscz\UI\Messaging;


/**
 * Class Flash
 * @package Flexsyscz\UI\Messaging
 */
abstract class Flash extends \stdClass
{
	/** @var Message */
	public $message;

	/** @var string */
	public $type;
}
