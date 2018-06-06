<?php declare(strict_types = 1);

namespace DixonsTest\Model\Product\VisitCounter;

final class ProductVisitCounterMemoryAdder implements ProductVisitCounterAdder
{
	/**
	 * @var array<string,int>
	 */
	private $productCounts;

	/**
	 * @param array<string,int> $productCounts
	 */
	public function __construct(array $productCounts = [])
	{
		$this->productCounts = $productCounts;
	}

	public function addNewVisit(string $productId): void
	{
		$actualCount = $this->productCounts[$productId] ?? 0;

		$this->productCounts[$productId] = $actualCount + 1;
	}

	public function getProductCounts(): array
	{
		return $this->productCounts;
	}
}
