<?php

namespace App\Controllers;

use App\Services\Database\Database;

class MessageController
{
	/**
	 * @param  string  $theme
	 * @param  string  $message
	 * @return false|int
	 * @throws \Exception
	 */
	public static function createMessage(string $theme, string $message): false|int
	{
		$query = 'INSERT INTO messages (theme, message) VALUES(:theme, :message)';
		$connection = Database::getConnection();
		$statement = $connection->prepare($query);
		$statement->bindValue(':theme', $theme);
		$statement->bindValue(':message', $message);
		$statement->execute();

		return $connection->lastInsertId();
	}
}