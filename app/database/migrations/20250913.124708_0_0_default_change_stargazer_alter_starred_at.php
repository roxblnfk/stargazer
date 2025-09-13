<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultAe28201a375c47a2d87490d34c3e2d45 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('stargazer')
            ->alterColumn('starred_at', 'datetime', [
                'nullable' => true,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('stargazer')
            ->alterColumn('starred_at', 'timestamp', [
                'nullable' => false,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->update();
    }
}
