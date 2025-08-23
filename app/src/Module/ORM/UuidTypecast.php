<?php

declare(strict_types=1);

namespace App\Module\ORM;

use Cycle\ORM\Parser\CastableInterface;
use Ramsey\Uuid\Uuid;

class UuidTypecast implements CastableInterface
{
    private array $fields = [];

    public function setRules(array $rules): array
    {
        foreach ($rules as $key => $rule) {
            if ($rule === 'uuid') {
                unset($rules[$key]);
                $this->fields[$key] = $rule;
            }
        }

        return $rules;
    }

    public function cast(array $data): array
    {
        foreach ($this->fields as $key => $fields) {
            isset($data[$key]) and $data[$key] = Uuid::fromString($data[$key]);
        }

        return $data;
    }
}
