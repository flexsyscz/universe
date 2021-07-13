<?php
declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;


/**
 * Interface StreamReader
 * @package Flexsyscz\Universe\FileSystem
 */
interface StreamReader
{
	/**
	 * @param string $filePath
	 * @return StreamReader
	 */
	public function open(string $filePath): self;

	/**
	 * @return mixed
	 */
	public function read();

	/**
	 * @return StreamReader
	 */
	public function close(): self;
}
