<?php declare(strict_types = 1);

include_once './Entity/Currency.php';
include_once './Entity/Price.php';
include_once './Entity/PriceLevel.php';
include_once './Logger.php';
include_once './PriceChangeValidator.php';
include_once './PriceService.php';
include_once './ProductService.php';
include_once './ImportBetter.php';





$productPrices = \file_get_contents('ProductPrices.json');
$productPrices = str_replace(["\r\n ", "\r\n\t", " "], '', $productPrices);
$data = \json_decode($productPrices);
(
new ImportBetter(
	new Logger(),
	new ProductService(),
	new PriceService(),
	new PriceChangeValidator()
)
)
	->doImport($data);

