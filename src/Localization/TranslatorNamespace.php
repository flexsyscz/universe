<?php declare(strict_types=1);

namespace Flexsyscz\Universe\Localization;

use Nette\Localization;
use Nette\SmartObject;


/**
 * Class TranslatorNamespace
 * @package Flexsyscz\Universe\Localization
 *
 * @property-read string			$namespace
 * @property-read Translator		$translator
 */
final class TranslatorNamespace implements Localization\Translator
{
	use SmartObject;

	/** @var string */
	private $namespace;

	/** @var Translator */
	private $translator;


	/**
	 * TranslatorNamespace constructor.
	 * @param string $namespace
	 * @param Translator $translator
	 */
	public function __construct(string $namespace, Translator $translator)
	{
		$this->namespace = $namespace;
		$this->translator = $translator;
	}


	/**
	 * @return string
	 */
	public function getNamespace(): string
	{
		return $this->namespace;
	}


	/**
	 * @return Translator
	 */
	public function getTranslator(): Translator
	{
		return $this->translator;
	}


	/**
	 * @param mixed $message
	 * @param mixed ...$parameters
	 * @return string
	 */
	public function translate($message, ...$parameters): string
	{
		return $this->translator->setNamespace($this->namespace)
			->translate($message, ...$parameters);
	}
}