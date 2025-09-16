<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultCa303a30e97bc2c7b5cf0392014514aa extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('campaign_user')
            ->addColumn('stars', 'bigInteger', ['nullable' => false, 'defaultValue' => 0, 'comment' => ''])
            ->update();
    }

    public function down(): void
    {
        $this->table('campaign_user')
            ->dropColumn('stars')
            ->update();
    }
}
