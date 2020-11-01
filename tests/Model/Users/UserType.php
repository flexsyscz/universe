<?php

declare(strict_types=1);

namespace Tests\Model;


class UserType extends \MabeEnum\Enum
{
	const TECHNICIAN = 'technician';
	const MANAGER = 'manager';
	const CEO = 'ceo';
	const ADMIN = 'admin';
}