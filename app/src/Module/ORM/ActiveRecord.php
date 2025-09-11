<?php

declare(strict_types=1);

namespace App\Module\ORM;

use Cycle\ActiveRecord\Facade;
use Cycle\ORM\SchemaInterface;

class ActiveRecord extends \Cycle\ActiveRecord\ActiveRecord
{
    private function __construct() {}

    /**
     * @return non-empty-string
     */
    public static function getTableName(): string
    {
        return Facade::getOrm()->getSchema()->define(static::class, SchemaInterface::TABLE);
    }
}
