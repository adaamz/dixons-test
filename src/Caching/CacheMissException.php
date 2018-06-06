<?php declare(strict_types = 1);

namespace DixonsTest\Caching;

final class CacheMissException extends \Exception
{
	/**
	 * @var string
	 */
	private $cacheKey;

	public function __construct(string $cacheKey, ?\Throwable $previous = null)
	{
		parent::__construct(sprintf('Cache miss for key "%s".', $cacheKey), 0, $previous);

		$this->cacheKey = $cacheKey;
	}

	public function getCacheKey(): string
	{
		return $this->cacheKey;
	}
}
