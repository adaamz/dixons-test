<?php declare(strict_types = 1);

namespace DixonsTest\Model\Product;

use DixonsTest\BaseTestCase;
use DixonsTest\Caching\CacheHelper;
use DixonsTest\Caching\Storages\MemoryCacheStorage;
use DixonsTest\Model\Product\Repository\ProductElasticSearchDummyRepository;
use DixonsTest\Model\Product\Repository\ProductMySqlDummyRepository;
use DixonsTest\Model\Product\VisitCounter\ProductVisitCounterMemoryAdder;
use Nette\Utils\Json;

final class ProductFacadeTest extends BaseTestCase
{
	public function testGetFromCache(): void
	{
		$missedKey = null;
		$writeKey = null;
		$writeData = null;

		$cacheStorage = new MemoryCacheStorage(
			[ProductFacade::class . '.getProduct.123456' => Json::encode(['id' => '123456', 'name' => 'e10adc3949ba59abbe56e057f20f883e'])],
			function (string $key) use (&$missedKey): void {
				$missedKey = $key;
			},
			function (string $key, string $data) use (&$writeKey, &$writeData): void {
				$writeKey = $key;
				$writeData = $data;
			}
		);

		$productVisitCounter = new ProductVisitCounterMemoryAdder();

		$productFacade = new ProductFacade(
			new CacheHelper($cacheStorage),
			new ProductMySqlDummyRepository(),
			$productVisitCounter
		);

		$productFacade->getProductById('123456');

		self::assertNull($missedKey);
		self::assertNull($writeKey);
		self::assertNull($writeData);

		self::assertSame(['123456' => 1], $productVisitCounter->getProductCounts());
	}

	public function testGetFromMysql(): void
	{
		$missedKey = null;
		$writeKey = null;
		$writeData = null;

		$cacheStorage = new MemoryCacheStorage(
			[ProductFacade::class . '.getProduct.ANOTHER_ID' => Json::encode(['id' => 'ANOTHER_ID', 'name' => 'DOES NOT METTER'])],
			function (string $key) use (&$missedKey): void {
				$missedKey = $key;
			},
			function (string $key, string $data) use (&$writeKey, &$writeData): void {
				$writeKey = $key;
				$writeData = $data;
			}
		);

		$productVisitCounter = new ProductVisitCounterMemoryAdder();

		$productFacade = new ProductFacade(
			new CacheHelper($cacheStorage),
			new ProductElasticSearchDummyRepository(),
			$productVisitCounter
		);

		$productFacade->getProductById('123456');

		self::assertSame(ProductFacade::class . '.getProduct.123456', $missedKey);
		self::assertSame(ProductFacade::class . '.getProduct.123456', $writeKey);
		self::assertSame('{"id":"123456","name":"e10adc3949ba59abbe56e057f20f883e"}', $writeData);

		self::assertSame(['123456' => 1], $productVisitCounter->getProductCounts());
	}
}
