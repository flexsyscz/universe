<?php declare(strict_types=1);

namespace Flexsyscz\UI\Messaging;

use Flexsyscz\Universe\Exceptions\InvalidArgumentException;


/**
 * Trait PresenterFlashesTrait
 * @package Flexsyscz\UI\Messaging
 */
trait PresenterFlashesTrait
{
	/**
	 * @param $message
	 * @param string $type
	 * @return \stdClass
	 */
	public function flashMessage($message, $type = 'info'): \stdClass
	{
		if(!$message instanceof Message) {
			throw new InvalidArgumentException(sprintf('Argument $message must be instance of %s.', Message::class));
		}

		if(!in_array($type, MessageType::getConstants(), true)) {
			throw new InvalidArgumentException(sprintf('Argument $type is not valid constant of %s class.', MessageType::class));
		}

		return parent::flashMessage($message, $type);
	}


	/**
	 * @param string $message
	 * @param string|null $caption
	 * @return \stdClass
	 */
	public function flashInfo(string $message, string $caption = null): \stdClass
	{
		$message = new Message($message, $caption);
		return $this->flashMessage($message, MessageType::INFO);
	}


	/**
	 * @param string $message
	 * @param string|null $caption
	 * @return \stdClass
	 */
	public function flashWarning(string $message, string $caption = null): \stdClass
	{
		$message = new Message($message, $caption);
		return $this->flashMessage($message, MessageType::WARNING);
	}


	/**
	 * @param string $message
	 * @param string|null $caption
	 * @return \stdClass
	 */
	public function flashError(string $message, string $caption = null): \stdClass
	{
		$message = new Message($message, $caption);
		return $this->flashMessage($message, MessageType::ERROR);
	}


	/**
	 * @param string $message
	 * @param string|null $caption
	 * @return \stdClass
	 */
	public function flashSuccess(string $message, string $caption = null): \stdClass
	{
		$message = new Message($message, $caption);
		return $this->flashMessage($message, MessageType::SUCCESS);
	}
}