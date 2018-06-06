<?php declare(strict_types = 1);

namespace DixonsTest\Caching\Storages;

use DixonsTest\Caching\CacheMissException;

interface CacheStorage
{
	/**
	 * @throws CacheMissException
	 */
	public function read(string $key): string;

	public function write(string $key, string $data): void;
}
