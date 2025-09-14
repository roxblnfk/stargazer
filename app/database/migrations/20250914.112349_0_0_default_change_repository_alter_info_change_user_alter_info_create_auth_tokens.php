<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefault2b730eeefa0915d2d750f2a768883f46 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('repository')
            ->alterColumn('info', 'json', ['nullable' => false, 'defaultValue' => null, 'comment' => ''])
            ->update();
        $this->table('user')
            ->alterColumn('info', 'json', ['nullable' => false, 'defaultValue' => null, 'comment' => ''])
            ->update();
        $this->table('auth_tokens')
            ->addColumn('id', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 64, 'comment' => ''])
            ->addColumn('hashed_value', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 128, 'comment' => ''])
            ->addColumn('created_at', 'datetime', [
                'nullable' => false,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('expires_at', 'datetime', [
                'nullable' => true,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('payload', 'binary', ['nullable' => false, 'defaultValue' => null, 'comment' => ''])
            ->setPrimaryKeys(['id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('auth_tokens')->drop();
        $this->table('user')
            ->alterColumn('info', 'json', ['nullable' => true, 'defaultValue' => null, 'comment' => ''])
            ->update();
        $this->table('repository')
            ->alterColumn('info', 'json', ['nullable' => true, 'defaultValue' => null, 'comment' => ''])
            ->update();
    }
}
