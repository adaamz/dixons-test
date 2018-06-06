<?php declare(strict_types = 1);

namespace DixonsTest\Caching;

use DixonsTest\Caching\Storages\CacheStorage;

final class CacheHelper
{
	/**
	 * @var CacheStorage
	 */
	private $cacheStorage;

	public function __construct(CacheStorage $cacheStorage)
	{
		$this->cacheStorage = $cacheStorage;
	}

	public function readOrFallbackWithCacheWrite(string $key, callable $fallback): string
	{
		try {
			return $this->cacheStorage->read($key);
		} catch (CacheMissException $e) {
			// log?
		}

		$data = $fallback();
		$this->cacheStorage->write($key, $data);

		return $data;
	}
}
