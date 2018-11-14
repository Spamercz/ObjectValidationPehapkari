<?php declare(strict_types = 1);


class PriceService
{

	public function get($productId, $priceLevel)
	{
		return \random_int(0, 1) ?
			new Price(
				1000.0,
				1200.0,
				new PriceLevel('default'),
				new Currency('EUR')
			)
			: NULL;
	}


	public function save(
		Price $priceEntity
	): Price
	{
		return $priceEntity;
	}

}
