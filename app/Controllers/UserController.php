<?php

namespace App\Controllers;

use App\Services\Database\Database;
use App\Services\FileServices\CsvParser;

class UserController
{
	/**
	 * Импорт пользователей из файла CSV
	 * 
	 * @param  string  $file_path
	 * @return array
	 */
	public function importUsersFromFile(string $file_path): array
	{
		$parser = new CsvParser($file_path);
		$parser->handle();
		
		return $parser->getData();
	}

	/**
	 * @param  array  $users
	 * @return string
	 * @throws \Exception
	 */
	public function addUsersToDB(array $users): string
	{
		$connection = Database::getConnection();

		$insert_query = $connection->prepare('
		    INSERT INTO `users` (id, name, email)
		    VALUES (:id, :name, :email)
		    ON DUPLICATE KEY UPDATE 
		        id = VALUES(id), 
		        name = VALUES(name), 
		        email = VALUES(email)
		');

		$connection->beginTransaction();

		foreach ($users as $record) {
			$insert_query->bindParam(':id', $record['id']);
			$insert_query->bindParam(':name', $record['name']);
			$insert_query->bindParam(':email', $record['email']);
			$insert_query->execute();
		}

		$connection->commit();

		return 'Информация о пользователях успешно обновлена';
	}
}