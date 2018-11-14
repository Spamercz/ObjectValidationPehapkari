<?php declare(strict_types = 1);


class PriceLevel
{
	private const PRICE_LEVEL_LIST = [
		'default',
		'discount',
		'normal',
	];

	/**
	 * @var string
	 */
	private $priceLevel;


	public function __construct(
		string $priceLevel
	)
	{
		if ( ! \in_array($priceLevel, self::PRICE_LEVEL_LIST, TRUE)) {
			throw new InvalidArgumentException('Not supported priceLevel: ' . $priceLevel);
		}
		$this->priceLevel = $priceLevel;
	}


	public function priceLevel(): string
	{
		return $this->priceLevel;
	}

}
