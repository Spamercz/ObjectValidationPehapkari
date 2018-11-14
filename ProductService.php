<?php declare(strict_types = 1);


class ProductService
{
	public function save(array $data)
	{

	}

	public function fetch($ean)
	{
		return \random_int(0, 1) ? 123 : NULL;
	}


	public function publishForPrice(
		Price $price
	): void
	{
		// Publikuji produkt pokud je potřeba
	}
}
