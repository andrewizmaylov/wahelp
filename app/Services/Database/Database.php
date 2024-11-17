<?php

namespace App\Services\Database;

use App\Services\Database\Contracts\DatabaseInterface;
use App\Services\FileServices\EnvParser;

class Database implements DatabaseInterface
{
	protected static array $config = [];
	protected static array $credentials = [];

	/**
	 * @throws \Exception
	 */
	public static function init(): void
	{
		$parser = new EnvParser(__DIR__ . '/../../../.env');
		$parser->handle();

		self::$credentials = [
			'user' => $parser->getKey('DB_USERNAME'),
			'pass' => $parser->getKey('DB_PASSWORD'),
		];
		self::$config = [
			'host' => $parser->getKey('DB_HOST'),
			'port' => $parser->getKey('DB_PORT'),
			'dbname' => $parser->getKey('DB_DATABASE'),
			'charset' => 'utf8mb4',
		];
	}

	protected static array $options = [
		\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,       // Fetch results as associative arrays
		\PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
	];

	public static \PDO $pdo;

	/**
	 * @throws \Exception
	 */
	protected static function initConnection(): void
	{
		self::init();
		$dsn = 'mysql:' . http_build_query(self::$config, '', ';');

		try {
			self::$pdo = new \PDO($dsn, self::$credentials['user'], self::$credentials['pass'], self::$options);
		} catch (\PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}

	/**
	 * @return \PDO
	 * @throws \Exception
	 */
	public static function getConnection(): \PDO
	{
		self::initConnection();

		return self::$pdo;
	}

	public static function getQueryResults(string $query): false|\PDOStatement
	{
		self::initConnection();
		$statement = self::$pdo->prepare($query);
		$statement->execute();

		return $statement;
	}
}