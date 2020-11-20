<?php declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;


/**
 * Interface IStreamWriter
 * @package Flexsyscz\Universe\FileSystem
 */
interface IStreamWriter
{
	/**
	 * @param string $filePath
	 * @return IStreamWriter
	 */
	public function open(string $filePath);

	/**
	 * @param array<string>|string $data
	 * @return IStreamWriter
	 */
	public function write($data);

	/**
	 * @return IStreamWriter
	 */
	public function close();
}