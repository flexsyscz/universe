<?php

declare(strict_types=1);

namespace Tests\Model;

use Flexsyscz\Universe\Model\EnumWrapper;
use Flexsyscz\Universe\Model\JsonWrapper;
use Flexsyscz\Universe\Model\SerializationWrapper;
use Nextras\Orm\Entity\Entity;


/**
 * @property 		int         					$id      					{primary}
 * @property 		UserType						$type						{wrapper EnumWrapper}
 * @property 		string							$profile					{wrapper JsonWrapper}
 * @property 		string							$metadata					{wrapper SerializationWrapper}
 */
final class User extends Entity
{
}