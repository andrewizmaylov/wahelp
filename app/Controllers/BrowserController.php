<?php

namespace App\Controllers;

class BrowserController
{
	/**
	 * @throws \Exception
	 */
	public static function handle(): void
	{
		$start = microtime(true);
		function printString(): void
		{
			printf(str_repeat('-', 70) . PHP_EOL) ;
		}
		echo '<pre>' . PHP_EOL;
		echo 'Тестовое задание от команды Wahelp.ru для будущего backend разработчика' . PHP_EOL;
		printString();

		echo 'Задача No1' . PHP_EOL;
		echo 'Реализовать механизм загрузки списка пользователей в БД для рассылки из переданного извне файла в формате .csv (номер, имя)' . PHP_EOL;

		echo 'Шаг No1: Создаем таблицу users' . PHP_EOL;
		try {
			$message = DatabaseController::createTable('users');
			echo 'USERS: ' . $message . PHP_EOL;

			echo 'Шаг No2: Импортируем пользователей из файла CSV' . PHP_EOL;
			$user_controller = new UserController();
			$users = $user_controller->importUsersFromFile(__DIR__.'/../../users.csv');

			$add_user_message = $user_controller->addUsersToDB($users);
			echo 'Из файла импортировано ' . count($users) . ' записей.' . PHP_EOL;
			echo $add_user_message . PHP_EOL;
			printString();

			echo 'Задача No2. Рассылка сообщений' . PHP_EOL;
			printString();

			echo 'Шаг No1: Создаем таблицы messages и queues' . PHP_EOL;

			$message = DatabaseController::createTable('messages');
			echo 'MESSAGES: ' . $message . PHP_EOL;
			$message = DatabaseController::createTable('queues');
			echo 'QUEUES: ' . $message . PHP_EOL;
			printString();

			echo 'Шаг No2: Создаем Сообщение для отправки' . PHP_EOL;
			$message_id = MessageController::createMessage(
				'Информационное сообщение',
				'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius facilis fugit hic quam quasi quibusdam quos sint voluptate. Accusantium est eum ex laboriosam minima nesciunt nihil, praesentium quae quam vero.',
			);
			$message_result = isset($message_id)
				? 'Cooбщение успешно создано, id: ' . $message_id
				: 'Сообщение не создано';
			echo $message_result . PHP_EOL;
			printString();

			echo 'Шаг No3: Добавляем всех пользователей в очередь' . PHP_EOL;
			echo QueueController::createNewQueue($message_id) . PHP_EOL;
			printString();

			echo 'Шаг No4: Начинаем перебор очереди отправки сообщений' . PHP_EOL;
			printString();
			QueueController::proceedQueue();

			printString();
			echo  'Все сообщения успешно отправлены.' . PHP_EOL;
			printString();

			echo 'All memory usage' . memory_get_usage(true)/1024 . PHP_EOL;
			$finish = microtime(true);
			echo "Execution time: " . number_format(($finish - $start), 4) . " seconds" . PHP_EOL;
			echo '</pre>' . PHP_EOL;
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
}