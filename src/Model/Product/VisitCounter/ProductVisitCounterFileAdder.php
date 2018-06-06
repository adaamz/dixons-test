<?php declare(strict_types = 1);

namespace DixonsTest\Model\Product\VisitCounter;

use Nette\IOException;
use Nette\Utils\Json;

final class ProductVisitCounterFileAdder implements ProductVisitCounterAdder
{
	/**
	 * @var string
	 */
	private $filePath;

	public function __construct(string $filePath)
	{
		$this->filePath = $filePath;
	}

	public function addNewVisit(string $productId): void
	{
		$filePath = $this->filePath;

		$fileHandle = fopen($filePath, 'cb+');
		if ($fileHandle === false) {
			throw new IOException(sprintf('Cannot open file "%s" for read+write.', $filePath));
		}

		if (!flock($fileHandle, LOCK_EX | LOCK_NB)) {
			// todo: try next attempt to lock file?
			throw new IOException(sprintf('Cannot lock file "%s" for exclusive lock.', $filePath));
		}

		$fileSize = filesize($filePath);
		if ($fileSize === false) {
			throw new IOException(sprintf('Cannot get size of file "%s".', $filePath));
		}

		if ($fileSize === 0) {
			$productsVisits = [];
		} else {
			$fileContents = fread($fileHandle, $fileSize);
			if ($fileContents === false) {
				throw new IOException(sprintf('Cannot read file "%s".', $filePath));
			}

			$productsVisits = (array)Json::decode($fileContents);
		}

		$actualVisitsCount = $productsVisits[$productId] ?? 0;
		$productsVisits[$productId] = $actualVisitsCount + 1;

		if (!ftruncate($fileHandle, 0)) {
			throw new IOException(sprintf('Cannot truncate file "%s".', $filePath));
		}

		if (!rewind($fileHandle)) {
			throw new IOException(sprintf('Cannot rewind file "%s"', $filePath));
		}

		$writtenBytes = fwrite($fileHandle, Json::encode($productsVisits));
		if ($writtenBytes === 0 || $writtenBytes === false) {
			throw new IOException(sprintf('Cannot write new visit count to file "%s"', $filePath));
		}

		fclose($fileHandle);
	}
}
