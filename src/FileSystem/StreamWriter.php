<?php

declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;


/**
 * Interface StreamWriter
 * @package Flexsyscz\Universe\FileSystem
 */
interface StreamWriter
{
	/**
	 * @param string $filePath
	 * @return StreamWriter
	 */
	public function open(string $filePath): self;

	/**
	 * @param array<string>|string $data
	 * @return StreamWriter
	 */
	public function write($data): self;

	/**
	 * @return StreamWriter
	 */
	public function close(): self;
}
