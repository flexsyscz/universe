<?php
declare(strict_types=1);

namespace Flexsyscz\UI\Messaging;

use Flexsyscz\Universe\Exceptions\InvalidArgumentException;


/**
 * Trait Messages
 * @package Flexsyscz\UI\Messaging
 */
trait Messages
{
	/**
	 * @param $message
	 * @param string $type
	 * @return \stdClass
	 */
	public function flashMessage($message, $type = 'info'): \stdClass
	{
		if (!$message instanceof Message) {
			throw new InvalidArgumentException(sprintf('Argument $message must be instance of %s.', Message::class));
		}

		if (!in_array($type, MessageType::getConstants(), true)) {
			throw new InvalidArgumentException(sprintf('Argument $type is not valid constant of %s class.', MessageType::class));
		}

		if ($this->isAjax()) {
			$this->redrawControl('flashes');
		}

		return parent::flashMessage($message, $type);
	}


	public function flashInfo(string $message, string $caption = null): \stdClass
	{
		if (method_exists($this, 'translate')) {
			$message = $this->translate($message);
		}

		$message = new Message($message, $caption);
		return $this->flashMessage($message, MessageType::INFO);
	}


	public function flashWarning(string $message, string $caption = null): \stdClass
	{
		if (method_exists($this, 'translate')) {
			$message = $this->translate($message);
		}

		$message = new Message($message, $caption);
		return $this->flashMessage($message, MessageType::WARNING);
	}


	public function flashError(string $message, string $caption = null): \stdClass
	{
		if (method_exists($this, 'translate')) {
			$message = $this->translate($message);
		}

		$message = new Message($message, $caption);
		return $this->flashMessage($message, MessageType::ERROR);
	}


	public function flashSuccess(string $message, string $caption = null): \stdClass
	{
		if (method_exists($this, 'translate')) {
			$message = $this->translate($message);
		}

		$message = new Message($message, $caption);
		return $this->flashMessage($message, MessageType::SUCCESS);
	}
}
