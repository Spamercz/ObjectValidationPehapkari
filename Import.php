<?php declare(strict_types = 1);


class Import
{

	/**
	 * @var \ProductPriceService
	 */
	private $productPriceService;

	/**
	 * @var \ProductService
	 */
	private $productService;

	/**
	 * @var \PriceService
	 */
	private $priceService;


	public function __construct(
		ProductPriceService $productPriceService,
		ProductService $productService,
		PriceService $priceService
	)
	{
		$this->productPriceService = $productPriceService;
		$this->productService = $productService;
		$this->priceService = $priceService;
	}


	public function doImport(
		stdClass $data
	)
	{
		foreach ($data->results as $item) {
			if (isset($item->ean)) {
				$productId = $this->productService->fetch($item->ean);
			}
			if ($productId) {
				if (isset($item->price)) {
					if ($item->price > 0) {
						$this->productPriceService->save(['product' => $productId, 'price' => $item->price, 'priceIncVat' => $item->priceInclVat, 'currency' => $item->currency,]);
						$valid = TRUE;
					} else {
						$valid = FALSE;
					}
				} else {
					$valid = FALSE;
				}
				if (isset($item->discountPrice)) {
					$this->productPriceService->save(['product' => $productId, 'price' => $item->discountPrice, 'priceIncVat' => $item->discountPriceInclVat, 'currency' => $item->currency,					]);
				}
				if (isset($item->defaultPrice)) {
					$oldPrice = $this->priceService->get($productId, 'default');
					$diff = $item->defaultPrice - $oldPrice;

					if ($item->defaultPrice > 0 && abs($diff) < ($item->defaultPrice * 0.10)) {
						$this->productPriceService->save(['product' => $productId, 'price' => $item->defaultPrice, 'priceIncVat' => $item->defaultPriceInclVat, 'currency' => $item->currency,]);
						$valid = TRUE;

					} else {
						$valid = FALSE;
					}
				} else {
					$valid = FALSE;
				}
			}
			if ($valid) {
				$this->productService->save([
					'isPublic' => $valid ? TRUE : FALSE,
				]);
			}
		}
	}

}


$productPrices = \file_get_contents('ProductPrices.json');
$productPrices = str_replace(["\r\n ", "\r\n\t", " "], '', $productPrices);
$data = \json_decode($productPrices);
(
	new Import(
		new ProductPriceService(),
		new ProductService(),
		new PriceService()
	)
)
	->doImport($data);
