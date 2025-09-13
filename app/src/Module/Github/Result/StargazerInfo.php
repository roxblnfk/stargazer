<?php

declare(strict_types=1);

namespace App\Module\Github\Result;

use TypeLang\Mapper\Mapping\MapName;

final class StargazerInfo implements \JsonSerializable, \Stringable
{
    public function __construct(
        #[MapName('starred_at')]
        public readonly \DateTimeImmutable $starredAt,
        public readonly UserInfo $user,
    ) {}

    public static function fromJsonArray(array $info): self
    {
        $info['starredAt'] = new \DateTimeImmutable($info['starredAt']);
        $info['user'] = UserInfo::fromJsonArray($info['user']);
        return new self(...$info);
    }

    public static function fromJsonString(string $json): self
    {
        return self::fromJsonArray(\json_decode($json, true));
    }

    public function jsonSerialize(): array
    {
        $result = (array) $this;
        $result['starredAt'] = $this->starredAt->format(DATE_ATOM);
        return $result;
    }

    public function __toString(): string
    {
        return \json_encode($this, \JSON_UNESCAPED_UNICODE);
    }
}
