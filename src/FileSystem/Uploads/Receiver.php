<?php declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;

use Flexsyscz\Universe\FileSystem\Uploads\Container;
use Nette\Http\FileUpload;


/**
 * Class Receiver
 * @package Flexsyscz\Universe\FileSystem
 */
final class Receiver
{
	/**
	 * @param FileUpload $fileUpload
	 * @return Container
	 */
	public function getContainer(FileUpload $fileUpload): Container
	{
		return new Container($fileUpload);
	}


	/**
	 * @param FileUpload $fileUpload
	 * @param callable $callback
	 */
	public function save(FileUpload $fileUpload, callable $callback): void
	{
		call_user_func($callback, $this->getContainer($fileUpload));
	}
}