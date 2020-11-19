<?php declare(strict_types=1);

namespace Flexsyscz\UI\Messaging;

use MabeEnum\Enum;


/**
 * Class MessageType
 * @package Flexsyscz\UI\Messaging
 */
class MessageType extends Enum
{
	const INFO = 'primary';
	const WARNING = 'warning';
	const ERROR = 'danger';
	const SUCCESS = 'success';
}