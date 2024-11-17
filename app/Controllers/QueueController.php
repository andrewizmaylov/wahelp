<?php

namespace App\Controllers;

use App\Enums\StatusEnum;
use App\Services\Database\Database;

class QueueController
{
	/**
	 * @return \Generator
	 */
	public static function getUnprocessedQueues(): \Generator
	{
		$amount = rand(300, 967);

		while (true) {
			$records = Database::getQueryResults('SELECT * FROM `queues` WHERE status <> ' . StatusEnum::SUCCESS . ' LIMIT ' . $amount)->fetchAll();
			if (empty($records)) {
				break;
			}

			foreach ($records as $record) {
				yield $record['id'];
			}

			echo count($records) . ' сообщений отправлено' . PHP_EOL;
		}
	}

	/**
	 * @throws \Exception
	 */
	public static function proceedQueue(): void
	{
		foreach (self::getUnprocessedQueues() as $id) {
			$updated_result = Database::getConnection()->prepare('UPDATE `queues` SET status = :status WHERE id = :id');
			$updated_result->execute([
				'id' => $id,
				'status' => StatusEnum::SUCCESS,
			]);
		}

	}

	/**
	 * Создаем очередь отправки после публикации нового сообщения
	 *
	 * @param  int  $message_id
	 * @return string
	 * @throws \Exception
	 */
	public static function createNewQueue(int $message_id): string
	{
		$connection = Database::getConnection();
		$message = $connection->query('SELECT * FROM `messages` WHERE id = ' . $message_id)->fetch();
		$users = $connection->query("SELECT * FROM `users`");
		$queues = array_map(function ($user) use ($message) {
			return json_encode([
				'message_id' => $message['id'],
				'theme' =>$message['theme'],
				'message' =>$message['message'],
				'user_id' => $user['id'],
			]);
		}, $users->fetchAll());

		$insert_query = $connection->prepare('
		    INSERT INTO `queues` (data)
		    VALUES (:data)
		');

		$connection->beginTransaction();

		foreach ($queues as $queue) {
			$insert_query->bindParam(':data', $queue);
			$insert_query->execute();
		}

		$connection->commit();

		return 'Сообщения поставлены в очередь на отправку';
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	public static function deleteProceeded(): void
	{
		$delete_query = Database::getConnection()->prepare('DELETE FROM `queues` WHERE status = :status');
		$delete_query->execute([
			'status' => StatusEnum::SUCCESS,
		]);
	}
}