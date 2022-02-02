<?php

declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;

use Flexsyscz\Universe\FileSystem\Uploads\Container;
use Nette\Http\FileUpload;


/**
 * Class Receiver
 * @package Flexsyscz\Universe\FileSystem
 */
final class Receiver
{
	public function getContainer(FileUpload $fileUpload): Container
	{
		return new Container($fileUpload);
	}


	public function save(FileUpload $fileUpload, callable $callback): void
	{
		call_user_func($callback, $this->getContainer($fileUpload));
	}
}
