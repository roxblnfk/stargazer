<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultF7ef48759250f6efed07a5f2ef2e7d88 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('github_token')
            ->addColumn('uuid', 'uuid', ['nullable' => false, 'defaultValue' => null, 'size' => 36])
            ->addColumn('created_at', 'datetime', ['nullable' => false, 'defaultValue' => 'CURRENT_TIMESTAMP'])
            ->addColumn('value', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 255])
            ->create();
        $this->table('repository')
            ->addColumn('id', 'bigInteger', ['nullable' => false, 'defaultValue' => null])
            ->addColumn('owner', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 255])
            ->addColumn('name', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 255])
            ->addColumn('created_at', 'datetime', ['nullable' => false, 'defaultValue' => null])
            ->addIndex(['name'], ['name' => 'repository_index_name_68aafd1867308', 'unique' => false])
            ->setPrimaryKeys(['id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('repository')->drop();
        $this->table('github_token')->drop();
    }
}
