<?php declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;

use Flexsyscz\Universe\Exceptions\DuplicatePartitionException;
use Flexsyscz\Universe\Exceptions\FileManagerException;
use Flexsyscz\Universe\Exceptions\InvalidArgumentException;
use Flexsyscz\Universe\Exceptions\PartitionNotFoundException;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;


/**
 * Class FileManager
 * @package Flexsyscz\Universe\FileSystem
 */
class FileManager
{
	/** @var ArrayHash<string> */
	private $partitions;

	/** @var string|null */
	private $workingDirectory = null;


	/**
	 * FileManager constructor.
	 * @param array<string> $partitions
	 */
	public function __construct(array $partitions)
	{
		$this->partitions = ArrayHash::from($partitions, false);
	}


	/**
	 * @param string $name
	 * @param string $path
	 * @return $this
	 */
	public function addPartition(string $name, string $path): self
	{
		if($this->partitions->offsetExists($name)) {
			throw new DuplicatePartitionException(sprintf('Partition %s is already exist.', $name));
		}

		$this->partitions->offsetSet($name, $path);

		return $this;
	}


	/**
	 * @param string $name
	 * @return $this
	 */
	public function removePartition(string $name): self
	{
		if(!$this->partitions->offsetExists($name)) {
			throw new PartitionNotFoundException(sprintf('Partition %s not found.', $name));
		}

		$this->partitions->offsetUnset($name);

		return $this;
	}


	/**
	 * @param string $name
	 * @return string
	 */
	public function getPartitionPath(string $name): string
	{
		if(!$this->partitions->offsetExists($name)) {
			throw new PartitionNotFoundException(sprintf('Partition %s not found.', $name));
		}

		return $this->partitions->offsetGet($name);
	}


	/**
	 * @param string $name
	 * @param string|null $filePath
	 * @return array<string>
	 */
	public function scan(string $name, string $filePath = null): array
	{
		$path = $this->getPartitionPath($name);
		if(!file_exists($path)) {
			throw new InvalidArgumentException(sprintf('Partition\'s path is invalid: %s', $path));
		}

		if($filePath) {
			$path = $this->absolutePath($filePath);
		}

		if(!is_dir($path)) {
			throw new InvalidArgumentException(sprintf('Destination is not a directory: %s', $path));
		}

		$scan = scandir($path);
		if(!$scan) {
			throw new FileManagerException('Unknown error occurred.');
		}

		foreach(['.', '..'] as $ignored) {
			if(($key = array_search($ignored, $scan)) !== false) {
				unset($scan[$key]);
			}
		}

		return $scan;
	}


	/**
	 * @param string $name
	 * @return $this
	 */
	public function use(string $name): self
	{
		if(!$this->partitions->offsetExists($name)) {
			throw new PartitionNotFoundException(sprintf('Partition %s not found.', $name));
		}

		$this->workingDirectory = $this->partitions->offsetGet($name);

		return $this;
	}


	/**
	 * @param string $file
	 * @param string|null $partition
	 * @return string
	 */
	public function absolutePath(string $file, string $partition = null): string
	{
		$path = $partition ? $this->getPartitionPath($partition) : $this->workingDirectory;
		if(!$path) {
			throw new InvalidArgumentException('Path not found.');
		}

		return FileSystem::joinPaths($path, $file);
	}


	/**
	 * @param string $file
	 * @return string
	 */
	public function read(string $file): string
	{
		if(!$this->workingDirectory) {
			throw new FileManagerException('Working directory is not set.');
		}

		return FileSystem::read($this->absolutePath($file));
	}


	/**
	 * @param string $file
	 * @param string $content
	 * @param int|null $mode
	 * @return $this
	 */
	public function write(string $file, string $content, ?int $mode = 0666): self
	{
		if(!$this->workingDirectory) {
			throw new FileManagerException('Working directory is not set.');
		}

		FileSystem::write($this->absolutePath($file), $content, $mode);

		return $this;
	}


	/**
	 * @param string $origin
	 * @param string $target
	 * @param bool $overwrite
	 * @param string|null $targetPartition
	 * @return $this
	 */
	public function rename(string $origin, string $target, bool $overwrite = true, string $targetPartition = null): self
	{
		if(!$this->workingDirectory) {
			throw new FileManagerException('Working directory is not set.');
		}

		FileSystem::rename($this->absolutePath($origin), $this->absolutePath($target, $targetPartition), $overwrite);

		return $this;
	}


	/**
	 * @param string $file
	 * @return $this
	 */
	public function delete(string $file): self
	{
		if(!$this->workingDirectory) {
			throw new FileManagerException('Working directory is not set.');
		}

		FileSystem::delete($this->absolutePath($file));

		return $this;
	}


	/**
	 * @param string $origin
	 * @param string $target
	 * @param bool $overwrite
	 * @param string|null $targetPartition
	 * @return $this
	 */
	public function copy(string $origin, string $target, bool $overwrite = true, string $targetPartition = null): self
	{
		if(!$this->workingDirectory) {
			throw new FileManagerException('Working directory is not set.');
		}

		FileSystem::copy($this->absolutePath($origin), $this->absolutePath($target, $targetPartition), $overwrite);

		return $this;
	}


	/**
	 * @param string $origin
	 * @param string $target
	 * @param bool $overwrite
	 * @param string|null $targetPartition
	 * @return $this
	 */
	public function move(string $origin, string $target, bool $overwrite = true, string $targetPartition = null): self
	{
		return $this->rename($origin, $target, $overwrite, $targetPartition);
	}


	/**
	 * @param string $dir
	 * @param int $mode
	 * @return $this
	 */
	public function createDir(string $dir, int $mode = 0777): self
	{
		if(!$this->workingDirectory) {
			throw new FileManagerException('Working directory is not set.');
		}

		FileSystem::createDir($this->absolutePath($dir), $mode);

		return $this;
	}
}