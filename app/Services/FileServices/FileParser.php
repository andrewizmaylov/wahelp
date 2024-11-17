<?php

namespace App\Services\FileServices;

use App\Services\FileServices\Contracts\ParserInterface;

abstract class FileParser implements ParserInterface
{
	protected string $path;

	protected array $data = [];

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	public function __construct(string $path)
	{
		$this->path = $path;
		try {
			$this->checkFileExists();
		} catch (\Exception $e) {
			// Логирование ошибок не реализуем, просто выводим сообщение
			echo $e->getMessage();
		}
	}

	/**
	 * Проверяем наличие файла
	 *
	 * @throws \Exception
	 */
	protected function checkFileExists(): void
	{
		if (!file_exists($this->path)) {
			throw new \Exception("File not found at $this->path");
		}
	}

	/**
	 * Используем генератор для оптимизации работы скрипта
	 *
	 * @return \Generator
	 */
	protected function proceedFile(): \Generator
	{
		$fh = fopen($this->path, 'r');
		while ($line = fgets($fh)) {
			yield $line;
		}
		fclose($fh);
	}


}