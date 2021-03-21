<?php
declare(strict_types=1);

namespace Flexsyscz\Universe\Localization;

use Flexsyscz\Universe\Exceptions\InvalidStateException;


/**
 * Trait TranslatedComponent
 * @package Flexsyscz\Universe\Localization
 */
trait TranslatedComponent
{
	/** @var TranslatorNamespace */
	private $translatorNamespace;

	/** @var \ReflectionClass */
	private $reflection;


	public function injectTranslator(TranslatorNamespaceFactory $factory): void
	{
		$this->reflection = new \ReflectionClass($this);
		$dir = dirname((string) ($this->reflection->getFileName())) . '/translations';
		if (!file_exists($dir) || !is_dir($dir)) {
			throw new InvalidStateException(sprintf('Directory of translations not found in path %s', $dir));
		}

		$namespace = self::ns();
		$translatorNamespace = $factory->create($namespace);
		$translatorNamespace->translator->addDirectory($dir, $namespace);

		$this->translatorNamespace = $translatorNamespace;
	}


	public function ns(string $name = null): string
	{
		$ns = $this->reflection->hasConstant('TRANSLATOR_NAMESPACE')
			? $this->reflection->getConstant('TRANSLATOR_NAMESPACE')
			: $this->reflection->getName();
		return $name ? sprintf('!%s.%s', $ns, $name) : $ns;
	}
}
