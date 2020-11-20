<?php declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;


/**
 * Interface IStreamReader
 * @package Flexsyscz\Universe\FileSystem
 */
interface IStreamReader
{
	/**
	 * @param string $filePath
	 * @return IStreamReader
	 */
	public function open(string $filePath);

	/**
	 * @return mixed
	 */
	public function read();

	/**
	 * @return IStreamReader
	 */
	public function close();
}