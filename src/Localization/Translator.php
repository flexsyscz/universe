<?php declare(strict_types=1);

namespace Flexsyscz\Universe\Localization;

use Flexsyscz\Universe\Exceptions\InvalidArgumentException;
use Flexsyscz\Universe\Exceptions\InvalidStateException;
use Nette\Localization;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use Tracy\ILogger;


/**
 * Class Translator
 * @package Flexsyscz\Universe\Services
 */
class Translator implements Localization\Translator
{
	/** @var string */
	private const PLACEHOLDER = '?';

	/** @var string */
	private const DELIMITER = '.';

	/** @var string */
	private const FOLLOW_SYMBOL = '@';

	/** @var int */
	private const MAX_FOLLOWINGS = 5;

	/** @var bool */
	private $debugMode;

	/** @var string */
	private $language;

	/** @var array<array> */
	private $dictionaries;

	/** @var ILogger */
	private $logger;

	/** @var int */
	private $followings;


	/**
	 * Translator constructor.
	 * @param bool $debugMode
	 * @param string $language
	 * @param array $dictionaries
	 * @param ILogger $logger
	 */
	public function __construct(bool $debugMode, string $language, array $dictionaries, ILogger $logger)
	{
		$this->debugMode = $debugMode;
		$this->language = $language;
		$this->dictionaries = [];
		$this->logger = $logger;

		foreach($dictionaries as $_language => $dictionary) {
			try {
				$this->dictionaries[$_language] = Neon::decode(FileSystem::read($dictionary));
			} catch(\Exception $e) {
				$this->logError(sprintf('Unable to read language dictionary: [%s] %s', $_language, $dictionary));
			}
		}
	}


	/**
	 * @param string $language
	 * @return $this
	 */
	public function setLanguage(string $language): self
	{
		$this->language = $language;

		return $this;
	}


	/**
	 * @param mixed $message
	 * @param mixed ...$parameters
	 * @return string
	 */
	public function translate($message, ...$parameters): string
	{
		if(!isset($this->dictionaries[$this->language])) {
			$this->logError(sprintf("String %s cannot be translated to the language %s because dictionary is not available.", $this->language, $message));
			return $message;
		}

		if(strpos($message, self::DELIMITER) === false) {
			return $message;
		}

		$path = explode(self::DELIMITER, $message);
		if(!is_array($path)) {
			$this->logError(sprintf('Invalid message for translation: %s', $message));
			return $message;
		}

		try {
			$this->followings = 0;

			$count = isset($parameters[0]) ? $parameters[0] : null;
			return $this->lookup($this->dictionaries[$this->language], $path, $count);

		} catch(InvalidArgumentException $e) {
			$this->logError(sprintf('Message %s not found in dictionary %s.', $message, $this->language));

		} catch(InvalidStateException $e) {
			$this->logError(sprintf('Message %s exceeds max. followings in dictionary %s.', $message, $this->language));
		}

		return $message;
	}


	/**
	 * @param array $node
	 * @param array $path
	 * @param null $count
	 * @param array|null $tail
	 * @return string
	 */
	private function lookup(array $node, array $path, $count = null, array $tail = null): string
	{
		if(empty($path)) {
			if(isset($node[$count])) {
				return $node[$count];
			} else if(isset($node[self::PLACEHOLDER])) {
				return sprintf($node[self::PLACEHOLDER], $count);
			}

			throw new InvalidArgumentException();
		}

		$index = array_shift($path);
		if(isset($node[$index])) {
			if(is_array($node[$index])) {
				if(count($path) === 0 && $tail) {
					return $this->lookup($node[$index], $tail, $count);
				}
				return $this->lookup($node[$index], $path, $count, $tail);
			} else {
				if(preg_match('#^' . self::FOLLOW_SYMBOL . '#', $node[$index])) {
					$this->followings++;

					if($this->followings > self::MAX_FOLLOWINGS) {
						throw new InvalidStateException();
					}

					$tail = count($path) > 0 ? $path : null;
					$path = preg_replace('#^' . self::FOLLOW_SYMBOL . '#', '', $node[$index]);
					$path = explode(self::DELIMITER, $path);
					return $this->lookup($this->dictionaries[$this->language], $path, $count, $tail);
				}
				return strval($node[$index]);
			}
		}

		throw new InvalidArgumentException();
	}


	/**
	 * @param string $message
	 */
	private function logError(string $message)
	{
		if($this->debugMode) {
			$this->logger->log($message, ILogger::ERROR);
		}
	}
}