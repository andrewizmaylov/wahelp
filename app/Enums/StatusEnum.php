<?php

namespace App\Enums;

enum StatusEnum: int
{
	const NEW = 1;
	const PROCESSED = 2;
	const FAIL = 3;
	const SUCCESS = 4;
}
