<?php

declare(strict_types=1);

namespace App\Module\ORM;

use Cycle\ORM\Select\Repository;

/**
 * Repository provides ability to load entities and construct queries.
 *
 * @template TEntity of object
 *
 * @extends Repository<TEntity>
 */
abstract class BaseRepository extends Repository {}
