<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefault25928408c521e300bf4d14812dc54fdc extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('campaign')
            ->addColumn('invite_code', 'string', [
                'nullable' => true,
                'defaultValue' => null,
                'length' => 64,
                'size' => 255,
                'comment' => '',
            ])
            ->addColumn('count_users', 'bigInteger', ['nullable' => false, 'defaultValue' => 0, 'comment' => ''])
            ->addColumn('count_repos', 'bigInteger', ['nullable' => false, 'defaultValue' => 0, 'comment' => ''])
            ->alterColumn('visible', 'boolean', ['nullable' => false, 'defaultValue' => false, 'comment' => ''])
            ->update();
    }

    public function down(): void
    {
        $this->table('campaign')
            ->alterColumn('visible', 'boolean', ['nullable' => false, 'defaultValue' => true, 'comment' => ''])
            ->dropColumn('invite_code')
            ->dropColumn('count_users')
            ->dropColumn('count_repos')
            ->update();
    }
}
