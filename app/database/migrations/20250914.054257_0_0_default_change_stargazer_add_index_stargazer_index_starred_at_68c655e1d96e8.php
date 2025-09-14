<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultCd34ad250775abacfb0466ae1af6ef9c extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('stargazer')
        ->addIndex(['starred_at'], ['name' => 'stargazer_index_starred_at_68c655e1d96e8', 'unique' => false])
        ->update();
    }

    public function down(): void
    {
        $this->table('stargazer')
        ->dropIndex(['starred_at'])
        ->update();
    }
}
