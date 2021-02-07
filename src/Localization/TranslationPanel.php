<?php

namespace Flexsyscz\Universe\Localization;

use Latte\Engine;
use Tracy\IBarPanel;


/**
 * Class TranslationPanel
 * @package Flexsyscz\Universe\Localization
 */
class TranslationPanel implements IBarPanel
{
	/** @var Translator */
	private $translator;


	/**
	 * TranslationPanel constructor.
	 * @param Translator $translator
	 */
	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @return string
	 */
	public function getTab(): string
	{
		$template = new Engine();
		return $template->renderToString(__DIR__ . '/templates/tab.latte');
	}


	/**
	 * @return string
	 */
	public function getPanel(): string
	{
		$template = new Engine();
		return $template->renderToString(__DIR__ . '/templates/panel.latte', [
			'translator' => $this->translator
		]);
	}
}