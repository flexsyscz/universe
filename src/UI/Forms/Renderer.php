<?php

declare(strict_types=1);

namespace Flexsyscz\UI\Forms;

use Nette;
use Nette\Forms\Form;


/**
 * Class Renderer
 * @package Flexsyscz\UI\Forms
 */
class Renderer
{
	public const OPTION_BTN_CLASS = 'btnClass';

	public const OPTION_FORM_SWITCH = 'formSwitch';

	/** @var string */
	private static $primarySubmitBtnClass = 'btn btn-primary';

	/** @var string */
	private static $secondarySubmitBtnClass = 'btn btn-secondary';


	public static function setPrimarySubmitBtnClass(string $primarySubmitBtnClass): void
	{
		self::$primarySubmitBtnClass = $primarySubmitBtnClass;
	}


	public static function setSecondarySubmitBtnClass(string $secondarySubmitBtnClass): void
	{
		self::$secondarySubmitBtnClass = $secondarySubmitBtnClass;
	}


	public static function makeBootstrap5(Form $form): void
	{
		$renderer = $form->getRenderer();
		if (isset($renderer->wrappers)) {
			$renderer->wrappers['controls']['container'] = 'div class="container form-controls"';
			$renderer->wrappers['pair']['container'] = 'div class="row mb-3"';
			$renderer->wrappers['pair']['.error'] = 'has-danger';
			$renderer->wrappers['control']['container'] = 'div class="col-sm-9"';
			$renderer->wrappers['label']['container'] = 'div class="col-sm-3 col-form-label"';
			$renderer->wrappers['control']['description'] = 'span class="form-text"';
			$renderer->wrappers['control']['errorcontainer'] = 'span class="form-control-feedback"';
			$renderer->wrappers['control']['erroritem'] = 'span class="invalid-feedback"';
			$renderer->wrappers['control']['.error'] = 'is-invalid';
			$renderer->wrappers['hidden']['container'] = 'div class="container form-hidden-controls"';
		}

		foreach ($form->getControls() as $control) {
			$type = $control->getOption('type');
			if ($type === 'button') {
				$btnClass = $control->getOption(self::OPTION_BTN_CLASS);
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? self::$primarySubmitBtnClass : $btnClass ?? self::$secondarySubmitBtnClass);
				$usedPrimary = true;

			} elseif (in_array($type, ['text', 'textarea'], true)) {
				$control->getControlPrototype()->addClass('form-control');

			} elseif ($type === 'select') {
				$control->getControlPrototype()->addClass('form-select');

			} elseif ($type === 'file') {
				$control->getControlPrototype()->addClass('form-control form-control-file');

			} elseif (in_array($type, ['checkbox', 'radio'], true)) {
				if ($control instanceof Nette\Forms\Controls\Checkbox) {
					$control->getLabelPrototype()->setAttribute('class', 'form-check-label');
				} else {
					$control->getItemLabelPrototype()->addClass('form-check-label');
				}
				$control->getControlPrototype()->setAttribute('class', 'form-check-input');
				if ($control->getOption(self::OPTION_FORM_SWITCH)) {
					$control->getContainerPrototype()->setName('div')->setAttribute('class', 'form-check form-switch');

				} else {
					$control->getContainerPrototype()->setName('div')->setAttribute('class', 'form-check');
				}
			}
		}
	}
}
