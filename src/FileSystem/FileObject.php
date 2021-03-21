<?php
declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem;

use Nette\Http\FileUpload;
use Nette\SmartObject;


/**
 * Trait FileObject
 * @package Flexsyscz\Universe\FileSystem
 */
trait FileObject
{
	use SmartObject;

	/** @var bool */
	private $isImage;

	/** @var string */
	private $name;

	/** @var int */
	private $size;

	/** @var string|null */
	private $type;


	public function isImage(): bool
	{
		if (!$this->isImage) {
			$this->isImage = in_array($this->type, FileUpload::IMAGE_MIME_TYPES, true);
		}

		return $this->isImage;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getSize(): int
	{
		return $this->size;
	}


	public function getType(): ?string
	{
		return $this->type;
	}
}
