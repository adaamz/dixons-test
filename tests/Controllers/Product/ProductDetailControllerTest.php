<?php declare(strict_types = 1);

namespace DixonsTest\Controllers\Product;

use DixonsTest\BaseTestCase;
use DixonsTest\Caching\CacheHelper;
use DixonsTest\Caching\Storages\MemoryCacheStorage;
use DixonsTest\Model\Product\ProductFacade;
use DixonsTest\Model\Product\Repository\ProductMySqlDummyRepository;
use DixonsTest\Model\Product\VisitCounter\ProductVisitCounterMemoryAdder;

final class ProductDetailControllerTest extends BaseTestCase
{
	public function testDetail(): void
	{
		$productFacade = new ProductFacade(
			new CacheHelper(new MemoryCacheStorage()),
			new ProductMySqlDummyRepository(),
			new ProductVisitCounterMemoryAdder()
		);

		$controller = new ProductDetailController($productFacade);

		self::assertSame('{"id":"123","name":"202cb962ac59075b964b07152d234b70"}', $controller->detail('123'));
	}
}
