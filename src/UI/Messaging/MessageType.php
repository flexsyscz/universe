<?php

declare(strict_types=1);

namespace Flexsyscz\UI\Messaging;

use MabeEnum\Enum;


/**
 * Class MessageType
 * @package Flexsyscz\UI\Messaging
 */
class MessageType extends Enum
{
	public const INFO = 'primary';
	public const WARNING = 'warning';
	public const ERROR = 'danger';
	public const SUCCESS = 'success';
}
