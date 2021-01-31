<?php declare(strict_types=1);

namespace Flexsyscz\Universe\Localization;


/**
 * Class TranslatorNamespace
 * @package Flexsyscz\Universe\Localization
 */
final class TranslatorNamespaceFactory
{
	/** @var Translator */
	private $translator;


	/**
	 * TranslatorNamespaceFactory constructor.
	 * @param Translator $translator
	 */
	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}


	/**
	 * @param string $namespace
	 * @return TranslatorNamespace
	 */
	public function create(string $namespace): TranslatorNamespace
	{
		return new TranslatorNamespace($namespace, $this->translator);
	}
}