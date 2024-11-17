<?php

namespace App\Services\FileServices;

class EnvParser extends FileParser
{
	/**
	 * @return void
	 */
	public function handle(): void
	{
		$lines = $this->proceedFile();
		$lines = $this->proceedRow($lines);

		foreach ($lines as $line) {
			$this->data[$line[0]] = $line[1];
		}
	}


	/**
	 * Получаем пару Ключ Значение
	 *
	 * @param  iterable  $rows
	 * @return iterable
	 */
	public function proceedRow(iterable $rows): iterable
	{
		foreach ($rows as $row) {
			if (str_starts_with(trim($row), '#')) {
				continue;
			}

			list($key, $value) = explode('=', $row, 2);
			yield [trim($key), trim($value)];
		}
	}

	/**
	 * @param  string  $key
	 * @return string|null
	 * @throws \Exception
	 */
	public function getKey(string $key): string|null
	{
		return array_key_exists($key, $this->data)
			? $this->data[$key]
			: null;
	}
}