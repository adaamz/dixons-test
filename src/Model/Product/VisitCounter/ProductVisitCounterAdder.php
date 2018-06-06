<?php declare(strict_types = 1);

namespace DixonsTest\Model\Product\VisitCounter;

interface ProductVisitCounterAdder
{
	public function addNewVisit(string $productId): void;
}
