<?php

namespace App\Controllers;

class ApiController
{
	/**
	 * @throws \Exception
	 */
	public static function loadUsersFromCSV(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_FILES['file'])) {
			echo "Ошибка использования API";
			exit;
		}

		$csv_file = $_FILES['file']['tmp_name'];

		// Validate the uploaded file (check file type)
		$fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
		if ($fileExtension !== 'csv') {
			echo "Загрузка возможно только из файлов CSV.";
			exit;
		}

		echo DatabaseController::createTable('users') . PHP_EOL;

		$controller = new UserController();

		$users = $controller->importUsersFromFile($csv_file);

		echo $controller->addUsersToDB($users);
	}


	/**
	 * @throws \Exception
	 */
	public static function createMessage(): void
	{
		self::checkRequestType();

		$data = self::getDataFromRequest();

		if (!isset($data['theme']) || !isset($data['message'])) {
			echo "Недостаточно данных для создания сообщения";
			exit;
		}


		echo DatabaseController::createTable('messages') . PHP_EOL;
		$message_id = MessageController::createMessage(
			'Информационное сообщение',
			'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius facilis fugit hic quam quasi quibusdam quos sint voluptate. Accusantium est eum ex laboriosam minima nesciunt nihil, praesentium quae quam vero.',
		);

		echo 'Сообщение создано успешно, id: ' . $message_id;
	}

	/**
	 * @throws \Exception
	 */
	public static function createMailList(): void
	{
		self::checkRequestType();

		$data = self::getDataFromRequest();

		if (!isset($data['message_id'])) {
			echo "Недостаточно данных для создания очереди отправки";
			exit;
		}

		echo DatabaseController::createTable('queues') . PHP_EOL;
		echo QueueController::createNewQueue($data['message_id']) . PHP_EOL;
	}


	/**
	 * @throws \Exception
	 */
	public static function proceedQueue(): void
	{
		self::checkRequestType();

		QueueController::proceedQueue();
		echo  'Все сообщения успешно отправлены.' . PHP_EOL;
	}
	public static function notFound()
	{

	}

	private static function getDataFromRequest()
	{
		$content = file_get_contents('php://input');

		return json_decode($content, true);
	}

	/**
	 * @throws \Exception
	 */
	public static function deleteProceeded(): void
	{
		self::checkRequestType();

		QueueController::deleteProceeded();

		echo  'Отправленные сообщения успешно удалены.' . PHP_EOL;
	}

	/**
	 * @return void
	 */
	public static function checkRequestType(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo "Ошибка использования API: Допустимый метод POST";
			exit;
		}
	}
}