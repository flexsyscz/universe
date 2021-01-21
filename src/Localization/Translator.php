<?php declare(strict_types=1);

namespace Flexsyscz\Universe\Localization;

use Nette\Localization\ITranslator;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use Tracy\ILogger;


/**
 * Class Translator
 * @package Flexsyscz\Universe\Services
 */
class Translator implements ITranslator
{
	/** @var string */
	private const PLACEHOLDER = '?';

	/** @var string */
	private const DELIMITER = '.';

	/** @var string */
	private const FOLLOW_SYMBOL = '@';

	/** @var string */
	private $language;

	/** @var array<array> */
	private $dictionaries;

	/** @var ILogger */
	private $logger;


	/**
	 * Translator constructor.
	 * @param string $language
	 * @param array<string> $dictionaries
	 * @param ILogger $logger
	 */
	public function __construct(string $language, array $dictionaries, ILogger $logger)
	{
		$this->language = $language;
		$this->dictionaries = [];
		$this->logger = $logger;

		foreach($dictionaries as $_language => $dictionary) {
			try {
				$this->dictionaries[$_language] = Neon::decode(FileSystem::read($dictionary));
			} catch(\Exception $e) {
				$this->logger->log(sprintf('Unable to read language dictionary: [%s] %s', $_language, $dictionary), ILogger::ERROR);
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
			$this->logger->log(sprintf("String %s cannot be translated to the language %s because dictionary is not available.", $this->language, $message), ILogger::ERROR);
			return $message;
		}

		if(strpos($message, self::DELIMITER) === false) {
			return $message;
		}

		$path = explode(self::DELIMITER, $message);
		if(!is_array($path)) {
			$this->logger->log(sprintf('Invalid message for translation: %s', $message), ILogger::ERROR);
			return $message;
		}

		$count = isset($parameters[0]) ? $parameters[0] : null;
		$translation = $this->lookup($this->dictionaries[$this->language], $path, $count);
		if(!$translation) {
			$this->logger->log(sprintf('Message %s not found in dictionary %s.', $message, $this->language), ILogger::ERROR);
			return $message;
		}

		return $translation;
	}


	/**
	 * @param array<mixed> $node
	 * @param array<string> $path
	 * @param mixed|null $count
	 * @return string|null
	 */
	private function lookup(array $node, array $path, $count = null): ?string
	{
		if(empty($path)) {
			return isset($node[$count])
				? $node[$count]
				: (isset($node[self::PLACEHOLDER])
					? sprintf($node[self::PLACEHOLDER], $count)
					: null);
		}

		$index = array_shift($path);
		if(isset($node[$index])) {
			if(is_array($node[$index])) {
				return $this->lookup($node[$index], $path, $count);
			} else {
				return strval($node[$index]);
			}
		}

		return null;
	}
}