<?php

namespace App\Models\Message;

abstract class Message
{
	public readonly string $theme;
	public readonly string $message;

	/**
	 * @param  string  $theme
	 * @param  string  $message
	 */
	public function __construct(string $theme, string $message)
	{
		$this->theme = $theme;
		$this->message = $message;
	}
}