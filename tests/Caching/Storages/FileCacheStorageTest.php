<?php declare(strict_types = 1);

namespace DixonsTest\Caching\Storages;

use DixonsTest\BaseTestCase;
use DixonsTest\Caching\CacheMissException;

final class FileCacheStorageTest extends BaseTestCase
{
	public function testReadWithContents(): void
	{
		file_put_contents($this->tempDir . '/0a42907b589a309ad94f8874eacbc63f', 'cache content'); // md5(cache_key)

		$storage = new FileCacheStorage($this->tempDir);

		self::assertSame('cache content', $storage->read('cache_key'));
	}

	public function testReadWithoutContents(): void
	{
		$filePath = $this->tempDir . '/74dd58da41bc341d765075d4c63116bd'; // md5(non_existing_cache_key)
		if (is_file($filePath)) {
			unlink($filePath);
		}

		$storage = new FileCacheStorage($this->tempDir);

		$this->expectExceptionObject(new CacheMissException('non_existing_cache_key'));
		$storage->read('non_existing_cache_key'); // should throw exception
	}

	public function testWriteWithContents(): void
	{
		$filePath = $this->tempDir . '/0a42907b589a309ad94f8874eacbc63f'; // md5(cache_key)
		file_put_contents($filePath, 'cache content');

		$storage = new FileCacheStorage($this->tempDir);

		$storage->write('cache_key', 'whole new data');

		self::assertSame('whole new data', file_get_contents($filePath));
	}

	public function testWriteWithoutContents(): void
	{
		$filePath = $this->tempDir . '/74dd58da41bc341d765075d4c63116bd'; // md5(non_existing_cache_key)

		if (is_file($filePath)) {
			unlink($filePath);
		}

		$storage = new FileCacheStorage($this->tempDir);

		$storage->write('non_existing_cache_key', 'new cache data');

		self::assertSame('new cache data', file_get_contents($filePath));
	}
}
