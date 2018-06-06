<?php declare(strict_types = 1);

namespace DixonsTest\Caching\Storages;

use DixonsTest\Caching\CacheMissException;
use Nette\IOException;

final class FileCacheStorage implements CacheStorage
{
	/**
	 * @var string
	 */
	private $tempDirectory;

	public function __construct(string $tempDirectory)
	{
		$this->tempDirectory = $tempDirectory;
	}

	/**
	 * @throws CacheMissException
	 */
	public function read(string $key): string
	{
		$filePath = $this->getCacheFilePath($key);

		if (!is_file($filePath)) {
			throw new CacheMissException($key);
		}

		$fileHandle = fopen($filePath, 'rb');
		if ($fileHandle === false) {
			throw new IOException(sprintf('Cannot open file "%s" for reading.', $filePath));
		}

		if (!flock($fileHandle, LOCK_SH | LOCK_UN)) {
			// todo: try next attempt to lock file?
			throw new IOException(sprintf('Cannot lock file "%s" for shared lock.', $filePath));
		}

		$fileSize = filesize($filePath);
		if ($fileSize === false) {
			throw new IOException(sprintf('Cannot get size of file "%s".', $filePath));
		} elseif ($fileSize === 0) {
			return '';
		}

		$fileContents = fread($fileHandle, $fileSize);

		if ($fileContents === false) {
			throw new IOException(sprintf('Cannot read file "%s".', $filePath));
		}

		fclose($fileHandle);

		return $fileContents;
	}

	public function write(string $key, string $data): void
	{
		$filePath = $this->getCacheFilePath($key);

		$fileHandle = fopen($filePath, 'wb');
		if ($fileHandle === false) {
			throw new IOException(sprintf('Cannot open file "%s" for writing.', $filePath));
		}

		if (!flock($fileHandle, LOCK_EX | LOCK_UN)) {
			throw new IOException(sprintf('Cannot lock file "%s" for exclusive lock.', $filePath));
		}

		$writtenBytes = fwrite($fileHandle, $data);

		// strlen returns total bytes of string, not characters count
		if ($writtenBytes === false || $writtenBytes !== strlen($data)) {
			throw new IOException(sprintf('Cannot write new data to file "%s".', $filePath));
		}
	}

	private function getCacheFilePath(string $key): string
	{
		return $this->tempDirectory . '/' . md5($key);
	}
}
