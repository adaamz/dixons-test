<?php declare(strict_types = 1);

namespace DixonsTest;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
	/**
	 * @var string
	 */
	protected $tempDir = __DIR__ . '/tmp';
}
