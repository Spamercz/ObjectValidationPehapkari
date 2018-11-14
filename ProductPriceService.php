<?php declare(strict_types = 1);


class ProductPriceService
{

	public function save(array $priceArray): void
	{
		if ( ! isset($priceArray['currency'])) {
			throw new RuntimeException('No currency');
		}
		if ( ! isset($priceArray['price'])) {
			throw new RuntimeException('No price');
		}
	}

}
