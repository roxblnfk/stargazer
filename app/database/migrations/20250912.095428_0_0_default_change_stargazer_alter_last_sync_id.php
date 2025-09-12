<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefault4370391fa93d6c6ff2b7b555555c45e4 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('stargazer')
        ->alterColumn('last_sync_id', 'integer', ['nullable' => false, 'defaultValue' => null])
        ->update();
    }

    public function down(): void
    {
        $this->table('stargazer')
        ->alterColumn('last_sync_id', 'text', ['nullable' => false, 'defaultValue' => null, 'size' => 255])
        ->update();
    }
}
