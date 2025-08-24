<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultB6903aa23ae12189b27450140e14bfee extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('user')
            ->addColumn('created_at', 'datetime', ['nullable' => false, 'defaultValue' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['nullable' => false, 'defaultValue' => null])
            ->addColumn('id', 'bigInteger', ['nullable' => false, 'defaultValue' => null])
            ->addColumn('login', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 255])
            ->addColumn('info', 'json', ['nullable' => true, 'defaultValue' => null])
            ->addIndex(['login'], ['name' => 'user_index_login_68ab2247a0aac', 'unique' => false])
            ->setPrimaryKeys(['id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('user')->drop();
    }
}
