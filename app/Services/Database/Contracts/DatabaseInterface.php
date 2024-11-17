<?php

namespace App\Services\Database\Contracts;

interface DatabaseInterface
{
	/**
	 * @throws \Exception
	 */
	public static function init(): void;

	/**
	 * @return \PDO
	 * @throws \Exception
	 */
	public static function getConnection(): \PDO;

	public static function getQueryResults(string $query): false|\PDOStatement;
}