<?php

namespace App\Controllers;

//use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="WAHELP API",
 *     version="1.0.0",
 *     description="This API allows you to interact with the system and perform operations.",
 *     @OA\Contact(
 *         email="andrew.izmaylov@gmail.com"
 *     )
 * )
 */

class ApiController
{
	/**
	 * @OA\Post(
	 *     path="/api/v1/users/import",
	 *     summary="Upload CSV to import users",
	 *     description="This API endpoint allows you to upload a CSV file containing user data. The file should be in CSV format.",
	 *     tags={"Users"},
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="multipart/form-data",
	 *             @OA\Schema(
	 *                 type="object",
	 *                 @OA\Property(
	 *                     property="file",
	 *                     type="string",
	 *                     format="binary",
	 *                     description="CSV file containing user data"
	 *                 )
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response="200",
	 *         description="Users uploaded successfully and added to the database.",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 type="object",
	 *                 @OA\Property(property="message", type="string", example="Users uploaded successfully")
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response="400",
	 *         description="Invalid file format or other errors.",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 type="object",
	 *                 @OA\Property(property="error", type="string", example="Загрузка возможно только из файлов CSV.")
	 *             )
	 *         )
	 *     )
	 * )
	 *
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
	 * @OA\Post(
	 *      path="/api/v1/message/create",
	 *      summary="Create a new message",
	 *      description="This API endpoint allows you to create a new message by providing a theme and a message body.",
	 *      tags={"Messages"},
	 *      @OA\RequestBody(
	 *          required=true,
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  required={"theme", "message"},
	 *                  @OA\Property(property="theme", type="string", example="Announcement"),
	 *                  @OA\Property(property="message", type="string", example="Lorem ipsum dolor sit amet, consectetur adipiscing elit."),
	 *              )
	 *          )
	 *      ),
	 *      @OA\Response(
	 *          response="200",
	 *          description="Message created successfully",
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  @OA\Property(property="message", type="string", example="Message created successfully"),
	 *                  @OA\Property(property="message_id", type="integer", example=123)
	 *              )
	 *          )
	 *      ),
	 *      @OA\Response(
	 *          response="400",
	 *          description="Missing data or invalid request",
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  @OA\Property(property="error", type="string", example="Недостаточно данных для создания сообщения")
	 *              )
	 *          )
	 *      )
	 *  )
	 *
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
	 * @OA\Post(
	 *      path="/api/v1/mail_list/create",
	 *      summary="Create a new mail list queue",
	 *      description="This API endpoint allows you to create a new mail list queue. A message ID must be provided to create a new queue entry.",
	 *      tags={"Mailing Lists"},
	 *      @OA\RequestBody(
	 *          required=true,
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  required={"message_id"},
	 *                  @OA\Property(property="message_id", type="integer", example=1)
	 *              )
	 *          )
	 *      ),
	 *      @OA\Response(
	 *          response="200",
	 *          description="Queue created successfully",
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  @OA\Property(property="message", type="string", example="Queue created successfully"),
	 *                  @OA\Property(property="queue_id", type="integer", example=123)
	 *              )
	 *          )
	 *      ),
	 *      @OA\Response(
	 *          response="400",
	 *          description="Missing required data or invalid request",
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  @OA\Property(property="error", type="string", example="Недостаточно данных для создания очереди отправки")
	 *              )
	 *          )
	 *      )
	 *  )
	 *
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
	 * @OA\Post(
	 *      path="/api/v1/mail_list/proceed_queue",
	 *      summary="Process the message queue",
	 *      description="This API endpoint processes the message queue and sends all messages in the queue.",
	 *      tags={"Queues"},
	 *      @OA\Response(
	 *          response="200",
	 *          description="All messages processed and sent successfully",
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  @OA\Property(property="message", type="string", example="Все сообщения успешно отправлены.")
	 *              )
	 *          )
	 *      ),
	 *      @OA\Response(
	 *          response="400",
	 *          description="Invalid request or failure to process queue",
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  @OA\Property(property="error", type="string", example="Ошибка обработки очереди")
	 *              )
	 *          )
	 *      )
	 *  )
	 *
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
	 * @OA\Post(
	 *      path="/api/v1/mail_list/delete_proceeded",
	 *      summary="Delete all proceeded messages",
	 *      description="This API endpoint allows you to delete all messages that have already been processed and sent.",
	 *      tags={"Queues"},
	 *      @OA\Response(
	 *          response="200",
	 *          description="Messages successfully deleted",
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  @OA\Property(property="message", type="string", example="Отправленные сообщения успешно удалены.")
	 *              )
	 *          )
	 *      ),
	 *      @OA\Response(
	 *          response="400",
	 *          description="Invalid request or failure to delete messages",
	 *          @OA\MediaType(
	 *              mediaType="application/json",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  @OA\Property(property="error", type="string", example="Ошибка удаления отправленных сообщений")
	 *              )
	 *          )
	 *      )
	 *  )
	 *
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