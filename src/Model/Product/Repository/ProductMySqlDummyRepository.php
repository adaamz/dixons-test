<?php declare(strict_types = 1);

namespace DixonsTest\Model\Product\Repository;

final class ProductMySqlDummyRepository implements IMySqlProductRepository
{
	public function findProduct(string $id): array
	{
		return [
			'id' => $id,
			'name' => md5($id),
		];
	}
}
