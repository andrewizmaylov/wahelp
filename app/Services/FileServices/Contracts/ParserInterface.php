<?php

namespace App\Services\FileServices\Contracts;

interface ParserInterface
{
	/**
	 * Производит необходимые манипуляции с файлом. Проверка файла реализована в абстрактном классе FileParser
	 *
	 * @return void
	 */
	public function handle(): void;

	public function proceedRow(iterable $rows): iterable;
}