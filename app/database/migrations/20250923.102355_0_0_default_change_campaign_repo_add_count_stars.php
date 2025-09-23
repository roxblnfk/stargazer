<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefault78f79d7d0d2e9e7e939457807087bf67 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this
            ->table('campaign_repo')
            ->addColumn('count_stars', 'bigInteger', ['nullable' => false, 'defaultValue' => 0, 'comment' => ''])
            ->addColumn('count_stars_at_all', 'bigInteger', ['nullable' => false, 'defaultValue' => 0, 'comment' => ''])
            ->update();
    }

    public function down(): void
    {
        $this
            ->table('campaign_repo')
            ->dropColumn('count_stars')
            ->dropColumn('count_stars_at_all')
            ->update();
    }
}
