<?php

declare(strict_types=1);

namespace App\Application\Form;

use Spiral\Filters\Model\FilterInterface;
use Spiral\Filters\Model\Mapper\CasterInterface;

final class DateTimeCaster implements CasterInterface
{
    public function supports(\ReflectionNamedType $type): bool
    {
        return $type->getName() === \DateTimeInterface::class or $type->getName() === \DateTimeImmutable::class;
    }

    public function setValue(FilterInterface $filter, \ReflectionProperty $property, mixed $value): void
    {
        if ($property->getType()?->allowsNull() && empty($value)) {
            $property->setValue($filter, null);
            return;
        }

        $property->setValue($filter, new \DateTimeImmutable($value));
    }
}
