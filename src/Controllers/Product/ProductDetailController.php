<?php declare(strict_types = 1);

namespace DixonsTest\Controllers\Product;

use DixonsTest\Controllers\BaseController;
use DixonsTest\Model\Product\ProductFacade;
use Nette\Utils\Json;

final class ProductDetailController extends BaseController
{
	/**
	 * @var ProductFacade
	 */
	private $productFacade;

	public function __construct(ProductFacade $productFacade)
	{
		$this->productFacade = $productFacade;
	}

	public function detail(string $id): string
	{
		$productInfo = $this->productFacade->getProductById($id);

		return Json::encode($productInfo);
	}
}
