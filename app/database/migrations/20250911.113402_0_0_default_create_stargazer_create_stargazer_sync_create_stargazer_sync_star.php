<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultA9d1732f464295bd183dccb863fe81aa extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('stargazer')
        ->addColumn('created_at', 'datetime', ['nullable' => false, 'defaultValue' => 'CURRENT_TIMESTAMP'])
        ->addColumn('updated_at', 'datetime', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('user_id', 'bigInteger', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('repo_id', 'bigInteger', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('starred_at', 'datetime', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('last_sync_id', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 255])
        ->setPrimaryKeys(['user_id', 'repo_id'])
        ->create();
        $this->table('stargazer_sync')
        ->addColumn('created_at', 'datetime', ['nullable' => false, 'defaultValue' => 'CURRENT_TIMESTAMP'])
        ->addColumn('updated_at', 'datetime', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('id', 'bigPrimary', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('repo_id', 'bigInteger', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('repo_stars', 'bigInteger', ['nullable' => false, 'defaultValue' => 0])
        ->addColumn('finished_at', 'datetime', ['nullable' => true, 'defaultValue' => null])
        ->setPrimaryKeys(['id'])
        ->create();
        $this->table('stargazer_sync_star')
        ->addColumn('user_id', 'bigInteger', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('sync_id', 'bigInteger', ['nullable' => false, 'defaultValue' => null])
        ->addColumn('info', 'json', ['nullable' => true, 'defaultValue' => null])
        ->setPrimaryKeys(['user_id', 'sync_id'])
        ->create();
    }

    public function down(): void
    {
        $this->table('stargazer_sync_star')->drop();
        $this->table('stargazer_sync')->drop();
        $this->table('stargazer')->drop();
    }
}
