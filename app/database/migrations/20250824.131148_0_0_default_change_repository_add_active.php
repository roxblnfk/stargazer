<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefault2a7ff09c748c669d2d4ee0ee0c082903 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('repository')
            ->addColumn('active', 'boolean', ['nullable' => false, 'defaultValue' => true, 'size' => 1])
            ->update();
    }

    public function down(): void
    {
        $this->table('repository')
            ->dropColumn('active')
            ->update();
    }
}
