<?php declare(strict_types = 1);


class PriceChangeValidator
{

	public function isValid(?Price $oldPrice, $newPrice): bool
	{
		if ( ! $oldPrice) {
			return TRUE;
		}

		if ($oldPrice->diff($newPrice) < $newPrice * 0.10) {
			return TRUE;
		}

		return FALSE;
	}

}
