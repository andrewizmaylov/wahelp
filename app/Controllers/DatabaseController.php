<?php

namespace App\Controllers;

use App\Services\Database\Database;

class DatabaseController
{
	const TABLE_EXISTS = "Таблица уже существует.";
	const TABLE_CREATED = "Таблица создана.";

	/**
	 * @param  string  $table_name
	 * @return string
	 * @throws \Exception
	 */
	public static function createTable(string $table_name): string
	{
		$connection = Database::getConnection();
		$message = self::TABLE_EXISTS;

		if (self::checkTableExists($table_name, $connection)) {
			$create_table = $connection->prepare(self::getQuery($table_name));
			$create_table->execute();

			$message = self::TABLE_CREATED;
		}

		return $message;
	}

	/**
	 * @param  string  $table_name
	 * @param  \PDO  $connection
	 * @return bool
	 */
	protected static function checkTableExists(string $table_name, \PDO $connection): bool
	{
		$query = 'SHOW TABLES LIKE "' . $table_name . '"';
		$table_check_query = $connection->prepare($query);
		$table_check_query->execute();

		return $table_check_query->rowCount() === 0;
	}

	/**
	 * @param  string  $table_name
	 * @return string
	 */
	protected static function getQuery(string $table_name): string
	{
		return match ($table_name) {
			'users' => 'CREATE TABLE IF NOT EXISTS `users` (
			    id BIGINT UNSIGNED PRIMARY KEY,
			    name VARCHAR(255),
	            email VARCHAR(255),
			    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			)',
			'messages' => 'CREATE TABLE IF NOT EXISTS `messages` (
			    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			    theme VARCHAR(255),
			    message text,
			    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			)',
			'queues' => 'CREATE TABLE IF NOT EXISTS `queues` (
			    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			    status SMALLINT UNSIGNED DEFAULT 1,
			    data JSON,
			    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			)'
		};
	}
}