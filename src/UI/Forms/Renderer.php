<?php declare(strict_types=1);

namespace Flexsyscz\UI\Forms;

use Nette;
use Nette\Forms\Form;


/**
 * Class Renderer
 * @package Flexsyscz\UI\Forms
 */
class Renderer
{
	/**
	 * @param Form $form
	 */
	public static function makeBootstrap5(Form $form): void
	{
		$renderer = $form->getRenderer();
		if(isset($renderer->wrappers)) {
			$renderer->wrappers['controls']['container'] = null;
			$renderer->wrappers['pair']['container'] = 'div class="row mb-3"';
			$renderer->wrappers['pair']['.error'] = 'has-danger';
			$renderer->wrappers['control']['container'] = 'div class=col-sm-9';
			$renderer->wrappers['label']['container'] = 'div class="col-sm-3 col-form-label"';
			$renderer->wrappers['control']['description'] = 'span class=form-text';
			$renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
			$renderer->wrappers['control']['.error'] = 'is-invalid';
		}

		foreach ($form->getControls() as $control) {
			$type = $control->getOption('type');
			if ($type === 'button') {
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-secondary');
				$usedPrimary = true;

			} elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
				$control->getControlPrototype()->addClass('form-control');

			} elseif ($type === 'file') {
				$control->getControlPrototype()->addClass('form-control-file');

			} elseif (in_array($type, ['checkbox', 'radio'], true)) {
				if ($control instanceof Nette\Forms\Controls\Checkbox) {
					$control->getLabelPrototype()->setAttribute('class', 'form-check-label');
				} else {
					$control->getItemLabelPrototype()->addClass('form-check-label');
				}
				$control->getControlPrototype()->setAttribute('class', 'form-check-input');
				$control->getSeparatorPrototype()->setName('div')->setAttribute('class', 'form-check');
			}
		}
	}
}