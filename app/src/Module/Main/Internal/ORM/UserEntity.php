<?php

declare(strict_types=1);

namespace App\Module\Main\Internal\ORM;

use App\Application\ORM\ActiveRecord;
use App\Module\Github\Dto\GithubUser;
use App\Module\Github\Result\UserInfo;
use App\Module\Main\DTO\User;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\UpdatedAt;

#[Entity(
    role: 'user',
    repository: UserRepository::class,
    table: 'user',
)]
#[Index(columns: ['login'])]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
class UserEntity extends ActiveRecord
{
    /**
     * Identifier on GitHub
     */
    #[Column(type: 'bigInteger', primary: true, typecast: 'int')]
    public int $id;

    /** @var non-empty-string */
    #[Column(type: 'string')]
    public string $login;

    #[Column(type: 'json', typecast: [UserInfo::class, 'fromJsonString'])]
    public UserInfo $info;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $updatedAt;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $createdAt;

    public static function createFromOwnerInfo(UserInfo $info): self
    {
        return self::make([
            'id' => $info->id,
            'login' => $info->login,
            'info' => $info,
        ]);
    }

    public function toDTO(): User
    {
        return new User(
            id: $this->id,
            login: new GithubUser($this->login),
            info: $this->info,
            updatedAt: $this->updatedAt,
            createdAt: $this->createdAt,
        );
    }
}
