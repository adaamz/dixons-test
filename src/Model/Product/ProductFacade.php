<?php declare(strict_types = 1);

namespace DixonsTest\Model\Product;

use DixonsTest\Caching\CacheHelper;
use DixonsTest\Model\Product\Repository\IProductRepository;
use DixonsTest\Model\Product\VisitCounter\ProductVisitCounterAdder;
use Nette\Utils\Json;

final class ProductFacade
{
	private const PRODUCT_CACHE_KEY = __CLASS__ . '.getProduct';

	/**
	 * @var IProductRepository
	 */
	private $productRepository;

	/**
	 * @var CacheHelper
	 */
	private $cacheHelper;

	/**
	 * @var ProductVisitCounterAdder
	 */
	private $productVisitCounterAdder;

	public function __construct(
		CacheHelper $cacheHelper,
		IProductRepository $productRepository,
		ProductVisitCounterAdder $productVisitCounterAdder
	) {
		$this->cacheHelper = $cacheHelper;
		$this->productRepository = $productRepository;
		$this->productVisitCounterAdder = $productVisitCounterAdder;
	}

	public function getProductById(string $productId): array
	{
		$productData = $this->cacheHelper->readOrFallbackWithCacheWrite($this->getCacheKey($productId), function () use ($productId) {
			$productInfo = $this->productRepository->findProduct($productId);

			return Json::encode($productInfo);
		});

		$this->productVisitCounterAdder->addNewVisit($productId);

		return (array)Json::decode($productData);
	}

	private function getCacheKey(string $productId): string
	{
		return self::PRODUCT_CACHE_KEY . '.' . $productId;
	}
}
