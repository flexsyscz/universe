<?php

declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;

use Flexsyscz\Universe\Exceptions\InvalidStateException;
use Nette\Utils\ArrayHash;


/**
 * Class CsvStreamReader
 * @package Flexsyscz\Universe\FileSystem
 */
class CsvStreamReader implements StreamReader
{
	public const DELIMITER = ';';

	/** @var resource */
	private $file;


	/**
	 * @param string $filePath
	 * @return $this
	 */
	public function open(string $filePath): StreamReader
	{
		$file = fopen($filePath, 'r');
		if (!$file) {
			throw new InvalidStateException(sprintf('Unable to open a stream to the file %s', $filePath));
		}

		$this->file = $file;
		if (!flock($this->file, LOCK_EX)) {
			throw new InvalidStateException(sprintf('Unable to set a lock on the file %s', $filePath));
		}

		return $this;
	}


	/**
	 * @param int $length
	 * @param string $delimiter
	 * @param string $enclosure
	 * @param string $escapeChar
	 * @return array<mixed>|false
	 */
	public function read(
		int $length = 0,
		string $delimiter = self::DELIMITER,
		string $enclosure = "'",
		string $escapeChar = '\\'
	) {
		if (($result = fgetcsv($this->file, $length, $delimiter, $enclosure, $escapeChar)) === null) { // @phpstan-ignore-line
			throw new InvalidStateException('Unable to read the data from the CSV stream.');
		}

		return $result;
	}


	/**
	 * @return $this
	 */
	public function close(): StreamReader
	{
		if (!flock($this->file, LOCK_UN)) {
			throw new InvalidStateException('Unable to release the file\'s lock.');
		}

		return $this;
	}


	/**
	 * @param array<mixed> $data
	 * @param array<string|int> $mapper
	 * @return ArrayHash<mixed>
	 */
	public function map(array $data, array $mapper): ArrayHash
	{
		$row = new ArrayHash;
		foreach ($mapper as $key => $name) {
			$row->offsetSet($name, $data[$key] ?? null);
		}

		return $row;
	}
}
