<?php declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;

use Flexsyscz\Universe\Exceptions\InvalidStateException;


/**
 * Class CsvStreamWriter
 * @package Flexsyscz\Universe\FileSystem
 */
class CsvStreamWriter implements IStreamWriter
{
	/** @var string */
	public const DELIMITER = ';';

	/** @var resource */
	private $file;


	/**
	 * @param string $filePath
	 * @return $this
	 */
	public function open(string $filePath): self
	{
		$file = fopen($filePath, 'w');
		if(!$file) {
			throw new InvalidStateException(sprintf('Unable to open a stream to the file %s', $filePath));
		}

		$this->file = $file;
		if(!flock($this->file, LOCK_EX)) {
			throw new InvalidStateException(sprintf('Unable to set a lock on the file %s', $filePath));
		}

		return $this;
	}


	/**
	 * @param array<string>|string $data
	 * @param string $delimiter
	 * @param string $enclosure
	 * @param string $escapeChar
	 * @return $this
	 */
	public function write($data, string $delimiter = self::DELIMITER, string $enclosure = "'", string $escapeChar = "\\"): self
	{
		if(!is_array($data) || fputcsv($this->file, $data, $delimiter, $enclosure, $escapeChar) === false) {
			throw new InvalidStateException('Unable to write the data into the CSV stream.');
		}

		return $this;
	}


	/**
	 * @return $this
	 */
	public function close(): self
	{
		if(!flock($this->file, LOCK_UN)) {
			throw new InvalidStateException('Unable to release the file\'s lock.');
		}

		return $this;
	}
}