<?php declare(strict_types = 1);


class ImportBetter
{

	/**
	 * @var bool
	 */
	private $customErrorHandler;

	/**
	 * @var \ProductService
	 */
	private $productService;

	/**
	 * @var \PriceService
	 */
	private $priceService;

	/**
	 * @var \Logger
	 */
	private $logger;

	/**
	 * @var \PriceChangeValidator
	 */
	private $priceChangeValidator;


	public function __construct(
		Logger $logger,
		ProductService $productService,
		PriceService $priceService,
		PriceChangeValidator $priceValidator
	)
	{
		$this->productService = $productService;
		$this->priceService = $priceService;
		$this->logger = $logger;
		$this->priceChangeValidator = $priceValidator;
	}


	public function formatData($data): \Generator
	{
		foreach($data->results as $result) {
			yield [
				'ean' => $result->ean,
				'price' => $result->price,
				'priceIncVat' => $result->priceInclVat,
				'currency' => $result->currency,
				'level' => 'normal',
			];
			yield [
				'ean' => $result->ean,
				'price' => $result->discountPrice,
				'priceIncVat' => $result->discountPriceInclVat,
				'currency' => $result->currency,
				'level' => 'discount',
			];
			yield [
				'ean' => $result->ean,
				'price' => $result->defaultPrice,
				'priceIncVat' => $result->defaultPriceInclVat,
				'currency' => $result->currency,
				'level' => 'default',
			];
		}
	}


	public function toggleErrorHandler(): void
	{
		$this->customErrorHandler = ! $this->customErrorHandler;

		restore_error_handler();

		if ($this->customErrorHandler) {
			set_error_handler([$this, 'errorHandler']);
		}
	}


	public function errorHandler(
		$errorNumber,
		$errorString,
		$errorFile,
		$errorLine
	)
	{
		if (
			\strpos($errorString, 'Undefined index') !== FALSE
			|| \strpos($errorString, 'Undefined property') !== FALSE
		) {
			$errorString .= ' in file ' . $errorFile . ' on line ' . $errorLine;
			throw new InvalidArgumentException($errorString);
		}
	}


	public function doImport(
		stdClass $data
	): void
	{
		$this->logger->log('Import starting');
		$rows = $this->formatData($data);
		$this->toggleErrorHandler();

		foreach ($rows as $row) {
			try {
				$this->logger->log('Processing priceLevel: ' . $row['level']);

				$priceEntity = $this->fetchPriceEntity($row['ean'], $row['level']);

				$preparedEntity = $this->prepareData($priceEntity, $row);

				$savedEntity = $this->import($preparedEntity);

				$this->afterImport($savedEntity);

			} catch (InvalidArgumentException $invalidArgumentException) {
				$this->productService->save([
					'isPublic' => FALSE,
				]);

				$this->logger->log($invalidArgumentException->getMessage());
			}
		}

		$this->toggleErrorHandler();
		$this->logger->log('Import done');
	}


	public function fetchPriceEntity(
		string $productEan,
		string $priceLevel
	):? Price
	{
		return $this->priceService->get($productEan, $priceLevel);
	}


	public function prepareData(
		?Price $price,
		array $row
	): Price
	{
		if ( ! $this->priceChangeValidator->isValid($price, $row['price'])) {
			throw new InvalidArgumentException('Price change exceeds agreed value.');
		}

		$product = $this->productService->fetch($row['ean']);
		if ( ! $product) {
			throw new InvalidArgumentException('Price for unknown product ean: ' . $row['ean']);
		}

		return new Price(
			(float) $row['price'],
			(float) $row['priceIncVat'],
			new PriceLevel($row['level']),
			new Currency($row['currency'])
		);
	}


	public function import(
		Price $price
	): Price
	{
		return $this->priceService->save($price);
	}


	public function afterImport(
		Price $price
	): void
	{
		$this->productService->publishForPrice($price);
	}

}
