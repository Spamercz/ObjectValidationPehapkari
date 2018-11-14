<?php declare(strict_types = 1);

class Currency
{
	private const CURRENCY_LIST = [
		'EUR',
		'CZK',
	];

	/**
	 * @var string
	 */
	private $currency;


	public function __construct(
		string $currency
	)
	{
		if ( ! \in_array($currency, self::CURRENCY_LIST, TRUE)) {
			throw new InvalidArgumentException('Not supported currency: ' . $currency);
		}
		$this->currency = $currency;
	}


	public function currency(): string
	{
		return $this->currency;
	}

}
