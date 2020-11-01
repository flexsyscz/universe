<?php

declare(strict_types=1);

namespace Tests\Model;

use Nextras\Orm\Repository\Repository;


/**
 * Class UsersRepository
 * @package App\Model
 */
final class UsersRepository extends Repository
{
	/**
	 * @return array
	 */
	static function getEntityClassNames() : array
	{
		return [User::class];
	}
}
