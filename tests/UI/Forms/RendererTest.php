<?php

/**
 * Test: Renderer
 */

declare(strict_types=1);

use Flexsyscz\Universe;
use Tester\Assert;


require __DIR__ . '/../../bootstrap.php';


test('', function () {
	$form = new \Nette\Forms\Form('hello');
	$form->onRender[] = [\Flexsyscz\UI\Forms\Renderer::class, 'makeBootstrap5'];

	$renderer = $form->getRenderer();
	$form->fireRenderEvents();

	Assert::equal(null, $renderer->wrappers['controls']['container']);
	Assert::equal(null, $renderer->wrappers['controls']['container']);
	Assert::equal(null, $renderer->wrappers['controls']['container']);
	Assert::equal(null, $renderer->wrappers['controls']['container']);
	Assert::equal('div class="row mb-3"', $renderer->wrappers['pair']['container']);
	Assert::equal('has-danger', $renderer->wrappers['pair']['.error']);
	Assert::equal('div class=col-sm-9', $renderer->wrappers['control']['container']);
	Assert::equal('div class="col-sm-3 col-form-label"', $renderer->wrappers['label']['container'] );
	Assert::equal('span class=form-text', $renderer->wrappers['control']['description'] );
	Assert::equal('span class=form-control-feedback', $renderer->wrappers['control']['errorcontainer']);
	Assert::equal('is-invalid', $renderer->wrappers['control']['.error']);
});