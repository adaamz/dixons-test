<?php declare(strict_types = 1);

namespace DixonsTest\Caching\Storages;

use DixonsTest\Caching\CacheMissException;

final class MemoryCacheStorage implements CacheStorage
{
	/**
	 * @var array<string, string>
	 */
	private $memory;

	/**
	 * @var callable|null
	 */
	private $onCacheMissCallback;

	/**
	 * @var callable|null
	 */
	private $onWrite;

	public function __construct(array $memory = [], ?callable $onCacheMissCallback = null, ?callable $onWrite = null)
	{
		$this->memory = $memory;
		$this->onCacheMissCallback = $onCacheMissCallback;
		$this->onWrite = $onWrite;
	}

	/**
	 * @throws CacheMissException
	 */
	public function read(string $key): string
	{
		if (!array_key_exists($key, $this->memory)) {
			if ($this->onCacheMissCallback !== null) {
				call_user_func($this->onCacheMissCallback, $key);
			}

			throw new CacheMissException($key);
		}

		return $this->memory[$key];
	}

	public function write(string $key, string $data): void
	{
		if ($this->onWrite !== null) {
			call_user_func($this->onWrite, $key, $data);
		}

		$this->memory[$key] = $data;
	}
}
