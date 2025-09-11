<?php

declare(strict_types=1);

namespace App\Module\Stargazer\Internal;

use App\Module\Github\Result\StargazerInfo;
use App\Module\ORM\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(
    role: 'stargazer_sync_star',
    table: 'stargazer_sync_star',
)]
class SyncStarEntity extends ActiveRecord
{
    #[Column(type: 'bigInteger', name: 'user_id', primary: true, typecast: 'int')]
    public int $userId;

    #[Column(type: 'bigInteger', name: 'sync_id', primary: true, typecast: 'int')]
    public int $syncId;

    #[Column(type: 'json', nullable: true, typecast: [StargazerInfo::class, 'fromJsonString'])]
    public StargazerInfo $info;

    public static function create(int $syncId, StargazerInfo $userInfo): self
    {
        return self::make([
            'userId' => $userInfo->user->id,
            'syncId' => $syncId,
            'info' => $userInfo,
        ]);
    }
}
