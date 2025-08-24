<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefault15f34313a16b5bfdfa0aa911f7f0ae8d extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('repository')
            ->addColumn('updated_at', 'datetime', ['nullable' => false, 'defaultValue' => null])
            ->addColumn('owner_id', 'bigInteger', ['nullable' => false, 'defaultValue' => null])
            ->addColumn('info', 'json', ['nullable' => true, 'defaultValue' => null])
            ->alterColumn('created_at', 'datetime', ['nullable' => false, 'defaultValue' => 'CURRENT_TIMESTAMP'])
            ->update();
    }

    public function down(): void
    {
        $this->table('repository')
            ->alterColumn('created_at', 'datetime', ['nullable' => false, 'defaultValue' => null])
            ->dropColumn('updated_at')
            ->dropColumn('owner_id')
            ->dropColumn('info')
            ->update();
    }
}
