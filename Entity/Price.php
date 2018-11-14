<?php


class Price
{

	/**
	 * @var float
	 */
	private $amount;

	/**
	 * @var float
	 */
	private $amountIncludingVat;

	/**
	 * @var \PriceLevel
	 */
	private $priceLevel;

	/**
	 * @var \Currency
	 */
	private $currency;


	public function __construct(
		float $amount
		, float $amountIncludingVat
		, PriceLevel $priceLevel
		, Currency $currency
	)
	{
		if ($amount == 0) {
			throw new InvalidArgumentException('Price not prided');
		}
		if ($amountIncludingVat < $amount) {
			throw new InvalidArgumentException('Vat price: ' . $amountIncludingVat . ' is less than price: ' . $amount);
		}
		$this->amount = $amount;
		$this->amountIncludingVat = $amountIncludingVat;
		$this->priceLevel = $priceLevel;
		$this->currency = $currency;
	}


	public function amount(): float
	{
		return $this->amount;
	}


	public function amountIncludingVat(): float
	{
		return $this->amountIncludingVat;
	}


	public function priceLevel(): \PriceLevel
	{
		return $this->priceLevel;
	}


	public function currency(): \Currency
	{
		return $this->currency;
	}


	public function diff($amount): int
	{
		return \abs($this->amount - $amount);
	}

}
