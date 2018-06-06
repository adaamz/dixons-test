<?php declare(strict_types = 1);

namespace DixonsTest\Model\Product\VisitCounter;

use DixonsTest\BaseTestCase;
use Nette\IOException;
use Nette\Utils\Json;

final class ProductVisitCounterFileAdderTest extends BaseTestCase
{
	public function testExistingProduct(): void
	{
		$filePath = $this->tempDir . '/product_5.txt';
		file_put_contents($filePath, Json::encode(['5' => 9]));

		$adder = new ProductVisitCounterFileAdder($filePath);
		$adder->addNewVisit('5');

		self::assertEquals((object)['5' => 10], Json::decode(file_get_contents($filePath)));
	}

	public function testNonExistingProduct(): void
	{
		$filePath = $this->tempDir . '/product_5.txt';
		file_put_contents($filePath, Json::encode(['5' => 9]));

		$adder = new ProductVisitCounterFileAdder($filePath);
		$adder->addNewVisit('9999');

		self::assertEquals((object)['5' => 9, '9999' => 1], Json::decode(file_get_contents($filePath)));
	}

	public function testNonExistingProductFile(): void
	{
		$filePath = $this->tempDir . '/product_999.txt';
		if (is_file($filePath)) {
			unlink($filePath);
		}

		$adder = new ProductVisitCounterFileAdder($filePath);
		$adder->addNewVisit('9999');

		self::assertEquals((object)['9999' => 1], Json::decode(file_get_contents($filePath)));
	}

	public function testPermanentlyLockedProductFile(): void
	{
		$filePath = $this->tempDir . '/product_999.txt';

		$fileHandle = fopen($filePath, 'cb+');
		self::assertNotFalse($fileHandle, 'Cannot open testing file.');
		self::assertNotFalse(flock($fileHandle, LOCK_EX), 'Cannot lock testing file.');

		$adder = new ProductVisitCounterFileAdder($filePath);

		$this->expectExceptionObject(new IOException(sprintf('Cannot lock file "%s" for exclusive lock.', $filePath)));
		$adder->addNewVisit('9999'); // should throw exception
	}
}
