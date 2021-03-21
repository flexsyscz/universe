<?php declare(strict_types=1);

namespace Flexsyscz\Universe\Localization;

use Flexsyscz\Universe\Exceptions\InvalidArgumentException;
use Flexsyscz\Universe\Exceptions\InvalidStateException;
use Latte\Runtime\FilterExecutor;
use Nette\Localization;
use Nette\Neon\Neon;
use Nette\SmartObject;
use Nette\Utils\FileSystem;
use Tracy\ILogger;


/**
 * Class Translator
 * @package Flexsyscz\Universe\Services
 *
 * @property-read string		$language
 * @property-read array			$debugStack
 */
final class Translator implements Localization\Translator
{
	use SmartObject;

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

	/** @var bool */
	private $logging;

	/** @var false|string */
	private $appDir;

	/** @var string */
	private $language;

	/** @var string */
	private $fallback;

	/** @var array<array> */
	private $dictionaries;

	/** @var ILogger */
	private $logger;

	/** @var array<array>|null */
	private $debugStack = null;

	/** @var array<array|string> */
	private $sniffer;

	/** @var int */
	private $followings;

	/** @var string */
	private $namespace;


	/**
	 * Translator constructor.
	 * @param array<array|string|bool> $parameters
	 * @param ILogger $logger
	 */
	public function __construct(array $parameters, ILogger $logger)
	{
		if(!isset($parameters['default'])) {
			throw new InvalidArgumentException('Default language is not defined.');
		}

		$this->debugMode = isset($parameters['debugMode']) && is_bool($parameters['debugMode']) ? $parameters['debugMode'] : false;
		$this->logging = isset($parameters['logging']) && is_bool($parameters['logging']) ? $parameters['logging'] : false;
		$this->appDir = isset($parameters['appDir']) && is_string($parameters['appDir']) ? FileSystem::normalizePath($parameters['appDir'] . '/../') : false;
		$this->namespace = isset($parameters['namespace']) && is_string($parameters['namespace']) ? $parameters['namespace'] : self::DEFAULT_NAMESPACE;
		$this->language = strval($parameters['default']);
		$this->fallback = isset($parameters['fallback']) && is_string($parameters['fallback']) ? $parameters['fallback'] : $this->language;
		$this->logger = $logger;

		if($this->debugMode) {
			$this->debugStack = [
				'dictionaries' => [],
			];
		}

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

				if($this->debugMode && is_array($this->debugStack)) {
					if(!isset($this->debugStack['dictionaries'][$language])) {
						$this->debugStack['dictionaries'][$language] = [];
					}

					$this->debugStack['dictionaries'][$language][$namespace] = $this->normalizeFilePath($filePath);
				}
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
			$items = scandir($path);
			if(is_array($items)) {
				foreach ($items as $item) {
					$language = strval(preg_replace('#\.neon$#', '', $item));
					if (in_array($language, $codes, true)) {
						$filePath = $path . DIRECTORY_SEPARATOR . $item;
						$this->info(sprintf('Component\'s dictionary accepted to install: [%s]:%s from path %s', $language, $namespace, $filePath));
						$this->install($language, $filePath, $namespace);
					}
				}
			} else {
				$this->error(sprintf('Unable to read items of directory: %s', $path));
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
	 * @return string
	 */
	public function getLanguage(): string
	{
		return $this->language;
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

			if($this->debugMode) {
				$backtrace = [];
				foreach(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 5) as $level) {
					$className = isset($level['object']) ? get_class($level['object']) : null;
					if($className === FilterExecutor::class) {
						$backtrace[] = $this->normalizeFilePath($level['file']);
						break;
					}

					$backtrace[] = $className ?: $level['file'];
				}

				$this->sniffer = [
					'message' => $message,
					'backtrace' => array_reverse($backtrace),
					'lookup' => [
						'namespace' => $namespace,
					],
				];
			}

			$translation = $this->lookup($entryNode, $path, $count);
			if($this->debugMode) {
				$this->sniffer['translation'] = $translation;
				if(is_array($this->debugStack)) {
					$this->debugStack[] = $this->sniffer;
				}
			}

			return $translation;

		} catch(InvalidArgumentException $e) {
			$error = sprintf('Message %s not found in dictionary [%s]:%s.', $message, $this->language, $this->namespace);
			if($this->debugMode) {
				$this->sniffer['error'] = $error;
			}

			$this->error($error);

		} catch(InvalidStateException $e) {
			$error = sprintf('Message %s exceeds max. followings in dictionary [%s]:%s.', $message, $this->language, $this->namespace);
			if($this->debugMode) {
				$this->sniffer['error'] = $error;
			}

			$this->error($error);
		}

		if($this->fallback !== $this->language) {
			$info = sprintf('Trying fallback language %s for message %s', $this->fallback, $message);
			$this->info($info);

			if($this->debugMode) {
				$this->sniffer['info'] = $error;
			}

			$tmp = $this->language;
			$this->language = $this->fallback;
			$message = $this->translate($message, ...$parameters);

			$this->language = $tmp;
		}

		if($this->debugMode) {
			$this->debugStack[] = $this->sniffer;
		}
		return $message;
	}


	/**
	 * @param array<array|string> $node
	 * @param array<array|string> $path
	 * @param null $count
	 * @param array<array|string>|null $tail
	 * @return string
	 */
	private function lookup(array $node, array $path, $count = null, array $tail = null): string
	{
		if(empty($path)) {
			if(isset($node[$count]) && is_string($node[$count])) {
				return $node[$count];
			} else if(isset($node[self::PLACEHOLDER]) && is_string($node[self::PLACEHOLDER])) {
				return sprintf($node[self::PLACEHOLDER], $count);
			}

			throw new InvalidArgumentException();
		}

		$index = array_shift($path);
		if(is_string($index) && isset($node[$index])) {
			if($this->debugMode && is_array($this->sniffer['lookup'])) {
				$this->sniffer['lookup'][] = $index;
			}

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
					$path = strval(preg_replace('#^' . self::FOLLOW_SYMBOL . '#', '', $node[$index]));
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
	private function info(string $message): void
	{
		if($this->logging) {
			$this->logger->log($message, ILogger::INFO);
		}
	}


	/**
	 * @param string $message
	 */
	private function error(string $message): void
	{
		if($this->logging) {
			$this->logger->log($message, ILogger::ERROR);
		}
	}


	/**
	 * @return array<array>
	 */
	public function getDebugStack(): array
	{
		return is_array($this->debugStack) ? $this->debugStack : [];
	}


	/**
	 * @param string $filePath
	 * @return string
	 */
	private function normalizeFilePath(string $filePath): string
	{
		return $this->appDir ? strval(preg_replace('#^' . $this->appDir . '#', '', $filePath)) : $filePath;
	}
}