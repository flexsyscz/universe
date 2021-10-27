<?php

declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem\Directories;

use Flexsyscz\Universe\FileSystem\FileManager;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;


abstract class AbstractDirectory
{
	/** @var FileManager */
	protected $fileManager;

	/** @var string */
	protected $relativePath;


	public function __construct(string $rootDir, string $path, FileManager $fileManager)
	{
		$this->fileManager = $fileManager;
		$this->fileManager->addPartition(static::class, $path);

		$relativePath = Strings::replace($path, sprintf('~%s~', preg_quote(FileSystem::normalizePath($rootDir), DIRECTORY_SEPARATOR)), '');
		$relativePath = Strings::replace($relativePath, sprintf('~^%s~', DIRECTORY_SEPARATOR), '');
		$this->relativePath = $relativePath;
	}


	public function getAbsolutePath(): string
	{
		return $this->fileManager->getPartitionPath(static::class);
	}


	public function getRelativePath(): string
	{
		return $this->relativePath;
	}
}
