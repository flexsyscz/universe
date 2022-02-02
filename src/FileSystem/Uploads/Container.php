<?php

declare(strict_types=1);

namespace Flexsyscz\Universe\FileSystem\Uploads;

use Flexsyscz\Universe\FileSystem\FileDescriptor;
use Flexsyscz\Universe\FileSystem\FileObject;
use Nette\Http\FileUpload;
use Nette\Utils\Image;
use Nette\Utils\ImageException;
use Nette\Utils\Random;


/**
 * Class Container
 * @package Flexsyscz\Universe\FileSystem\Uploads
 */
final class Container implements FileDescriptor
{
	use FileObject;

	/** @var FileUpload */
	private $fileUpload;


	/**
	 * Container constructor.
	 * @param FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload)
	{
		$this->fileUpload = $fileUpload;

		$this->name = $fileUpload->getSanitizedName();
		$this->size = $fileUpload->getSize();
		$this->type = $fileUpload->getContentType();
		$this->isImage = $fileUpload->isImage();
	}


	public function isOk(): bool
	{
		return $this->fileUpload->isOk();
	}


	public function getError(): int
	{
		return $this->fileUpload->getError();
	}


	public function getFileUpload(): FileUpload
	{
		return $this->fileUpload;
	}


	/**
	 * @param int|string|null $width
	 * @param int|string|null $height
	 * @param int $flags
	 * @return Image|null
	 * @throws ImageException
	 */
	public function getOptimizedImage($width = null, $height = null, int $flags = Image::FIT): ?Image
	{
		if ($this->isOk()) {
			if ($this->isImage()) {
				$exif = @exif_read_data($this->fileUpload->getTemporaryFile());
				$image = $this->fileUpload->toImage();
				if (is_array($exif) && !empty($exif['Orientation'])) {
					switch ($exif['Orientation']) {
						case 8:
							$image = $image->rotate(90, 0);
							break;
						case 3:
							$image = $image->rotate(180, 0);
							break;
						case 6:
							$image = $image->rotate(-90, 0);
							break;
					}
				}

				if ($width || $height) {
					$image->resize($width, $height, $flags);
				}

				return $image;
			}
		}

		return null;
	}


	public function getRandomizedName(int $length = 10, string $charlist = '0-9a-z', string $mask = '%s_%s'): string
	{
		return sprintf($mask, Random::generate($length, $charlist), $this->name);
	}
}
