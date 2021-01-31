<?php declare(strict_types=1);

namespace Flexsyscz\Universe\Localization;


/**
 * Trait TranslatedComponent
 * @package Flexsyscz\Universe\Localization
 */
trait TranslatedComponent
{
	/** @var TranslatorNamespace */
	private $translatorNamespace;


	/**
	 * @param TranslatorNamespaceFactory $factory
	 */
	public function injectTranslator(TranslatorNamespaceFactory $factory)
	{
		$reflection = new \ReflectionClass($this);
		$dir = dirname($reflection->getFileName()) . '/translations';

		$namespace = $reflection->hasConstant('TRANSLATOR_NAMESPACE') ? $reflection->getConstant('TRANSLATOR_NAMESPACE') : $reflection->getName();
		$translatorNamespace = $factory->create($namespace);
		$translatorNamespace->translator->addDirectory($dir, $namespace);

		$this->translatorNamespace = $translatorNamespace;
	}
}