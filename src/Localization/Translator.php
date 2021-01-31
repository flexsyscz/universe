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
final class Translator implements Localization\Translator
{
	/** @var string */
	public const DEFAULT_NAMESPACE = 'default';

	/** @var string */
	private const IMPORT_SYMBOL = '+';

	/** @var string */
	private const DELIMITER = '.';

	/** @var string */
	private const PLACEHOLDER = '?';

	/** @var string */
	private const FOLLOW_SYMBOL = '@';

	/** @var int */
	private const MAX_FOLLOWINGS = 5;

	/** @var bool */
	private $debugMode;

	/** @var string */
	private $language;

	/** @var string */
	private $fallback;

	/** @var array<array> */
	private $dictionaries;

	/** @var ILogger */
	private $logger;

	/** @var int */
	private $followings;

	/** @var string */
	private $namespace;


	/**
	 * Translator constructor.
	 * @param array $parameters
	 * @param ILogger $logger
	 */
	public function __construct(array $parameters, ILogger $logger)
	{
		if(!isset($parameters['default'])) {
			throw new InvalidArgumentException('Default language is not defined.');
		}

		$this->debugMode = isset($parameters['debugMode']) && is_bool($parameters['debugMode']) ? $parameters['debugMode'] : false;
		$this->namespace = isset($parameters['namespace']) ? $parameters['namespace'] : self::DEFAULT_NAMESPACE;
		$this->language = $parameters['default'];
		$this->fallback = isset($parameters['fallback']) ? $parameters['fallback'] : $this->language;
		$this->logger = $logger;

		$this->dictionaries = [];
		if(isset($parameters['languages']) && is_array($parameters['languages'])) {
			foreach ($parameters['languages'] as $language => $filePath) {
				$this->install($language, $filePath, $this->namespace);
			}
		}
	}


	/**
	 * @param string $language
	 * @param string $filePath
	 * @param string $namespace
	 * @return $this
	 */
	private function install(string $language, string $filePath, string $namespace): self
	{
		try {
			if(!isset($this->dictionaries[$language])) {
				$this->dictionaries[$language] = [];
			}

			$dictionary = Neon::decode(FileSystem::read($filePath));
			if(is_array($dictionary)) {
				$importMask = '#^\\' . self::IMPORT_SYMBOL . '#';
				foreach ($dictionary as $key => $value) {
					if (preg_match($importMask, $key)) {
						if (is_string($value)) {
							$import = dirname($filePath) . DIRECTORY_SEPARATOR . $value;
							if (file_exists($import)) {
								unset($dictionary[$key]);

								$key = strval(preg_replace($importMask, '', $key));
								$dictionary[$key] = Neon::decode(FileSystem::read($import));
								$this->info(sprintf('Dictionary imported as key %s: [%s]:%s from path %s', $key, $language, $namespace, $import));
							} else {
								$this->error(sprintf('File to import not found %s.', $import));
							}
						} else {
							$this->error(sprintf('Invalid path to import under key %s.', $key));
						}
					}
				}

				$this->dictionaries[$language][$namespace] = $dictionary;
				$this->info(sprintf('Dictionary installed: [%s]:%s from path %s', $language, $namespace, $filePath));
			}

		} catch(\Exception $e) {
			$this->error(sprintf('Unable to read language dictionary: [%s] %s', $language, $filePath));
		}

		return $this;
	}


	/**
	 * @param string $path
	 * @param string $namespace
	 * @return $this
	 */
	public function addDirectory(string $path, string $namespace): self
	{
		if(is_dir($path)) {
			$codes = array_keys($this->dictionaries);
			foreach(scandir($path) as $item) {
				$language = strval(preg_replace('#\.neon$#', '', $item));
				if(in_array($language, $codes, true)) {
					$filePath = $path . DIRECTORY_SEPARATOR . $item;
					$this->info(sprintf('Component\'s dictionary accepted to install: [%s]:%s from path %s', $language, $namespace, $filePath));
					$this->install($language, $filePath, $namespace);
				}
			}

		} else {
			$this->error(sprintf('Unable to read directory: %s', $path));
		}

		return $this;
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
	 * @param string $namespace
	 * @return $this
	 */
	public function setNamespace(string $namespace): self
	{
		$this->namespace = $namespace;

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
			$this->error(sprintf("String %s cannot be translated to the language %s because dictionary is not available.", $this->language, $message));
			return $message;
		}

		$path = explode(self::DELIMITER, $message);
		if(!is_array($path)) {
			$this->error(sprintf('Invalid message for translation: %s', $message));
			return $message;
		}

		try {
			$this->followings = 0;

			$count = isset($parameters[0]) ? $parameters[0] : null;
			$namespace = $this->namespace;
			$mask = '#^!#';
			if(preg_match($mask, $path[0])) {
				$namespace = preg_replace($mask, '', $path[0]);
				unset($path[0]);
			}
			$entryNode = isset($this->dictionaries[$this->language][$namespace]) ? $this->dictionaries[$this->language][$namespace] : [];

			return $this->lookup($entryNode, $path, $count);

		} catch(InvalidArgumentException $e) {
			$this->error(sprintf('Message %s not found in dictionary [%s]:%s.', $message, $this->language, $this->namespace));

		} catch(InvalidStateException $e) {
			$this->error(sprintf('Message %s exceeds max. followings in dictionary [%s]:%s.', $message, $this->language, $this->namespace));
		}

		if($this->fallback !== $this->language) {
			$this->info(sprintf('Trying fallback language %s for message %s', $this->fallback, $message));

			$tmp = $this->language;
			$this->language = $this->fallback;
			$message = $this->translate($message, ...$parameters);

			$this->language = $tmp;
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
					return $this->lookup($this->dictionaries[$this->language][$this->namespace], $path, $count, $tail);
				}
				return strval($node[$index]);
			}
		}

		throw new InvalidArgumentException();
	}


	/**
	 * @param string $message
	 */
	private function info(string $message)
	{
		if($this->debugMode) {
			$this->logger->log($message, ILogger::INFO);
		}
	}


	/**
	 * @param string $message
	 */
	private function error(string $message)
	{
		if($this->debugMode) {
			$this->logger->log($message, ILogger::ERROR);
		}
	}
}