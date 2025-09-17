<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultDfdc931aee42ae77f2b2375bd6192811 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        try {
            $this
                ->table('github_token')
                ->setPrimaryKeys(['uuid']);
        } catch (\Throwable) {
            // do nothing
        }
        $this
            ->table('github_token')
            ->addColumn('expires_at', 'datetime', [
                'nullable' => true,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('used_at', 'datetime', [
                'nullable' => false,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addIndex(['used_at'], ['name' => 'github_token_index_used_at_68ca67ccd64ad', 'unique' => false])
            ->update();
    }

    public function down(): void
    {
        $this
            ->table('github_token')
            ->dropIndex(['used_at'])
            ->dropColumn('expires_at')
            ->dropColumn('used_at')
            ->update();
    }
}
