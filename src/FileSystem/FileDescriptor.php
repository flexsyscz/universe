<?php declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;


/**
 * Interface FileDescriptor
 * @package Flexsyscz\Universe\FileSystem
 */
interface FileDescriptor
{
	/**
	 * @return bool
	 */
	function isImage(): bool;
}