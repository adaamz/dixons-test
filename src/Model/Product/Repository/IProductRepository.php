<?php declare(strict_types = 1);

namespace DixonsTest\Model\Product\Repository;

interface IProductRepository
{
	public function findProduct(string $id): array;
}
