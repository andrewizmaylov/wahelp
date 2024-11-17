<?php

namespace App\Services\FileServices;

class CsvParser extends FileParser
{
	/**
	 *
	 * @return void
	 */
	public function handle(): void
	{
		$contacts = $this->proceedFile();
		$contacts = $this->proceedRow($contacts);

		foreach ($contacts as $contact) {
			$this->data[$contact['id']] = $contact;
		}
	}

	/**
	 * Получаем id, name, email из переданного файла
	 *
	 * @param  iterable  $rows
	 * @return iterable
	 */
	public function proceedRow(iterable $rows): iterable
	{
		foreach ($rows as $contact) {
			$clean_str = str_replace(["\r", "\n"], '', $contact); // Remove carriage returns and newlines
			$clean_str = trim($clean_str); // Trim leading/trailing spaces
			list($id, $name) = array_map('trim', explode(',', $clean_str));

			$row = [
				'id' => $id,
				'name' => $name,
				'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
			];

			if ($row) yield $row;
		}
	}

}

