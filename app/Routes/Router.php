<?php

namespace App\Routes;

use App\Controllers\ApiController;
use App\Controllers\BrowserController;

class Router
{
	protected string $url;
	protected string $method;

	public function __construct()
	{
		$this->url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$this->method = $_SERVER['REQUEST_METHOD'];
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @throws \Exception
	 */
	public function returnResponse(): void
	{
		match ($this->url) {
			'/api/v1/users/import'    => ApiController::loadUsersFromCSV(),
			'/api/v1/message/create'  => ApiController::createMessage(),
			'/api/v1/mail_list/create'  => ApiController::createMailList(),
			'/api/v1/mail_list/proceed_queue'  => ApiController::proceedQueue(),
			'/api/v1/mail_list/delete_proceeded'  => ApiController::deleteProceeded(),
			'/browser'  => BrowserController::handle(),
			default                     => ApiController::notFound()
		};
	}
}