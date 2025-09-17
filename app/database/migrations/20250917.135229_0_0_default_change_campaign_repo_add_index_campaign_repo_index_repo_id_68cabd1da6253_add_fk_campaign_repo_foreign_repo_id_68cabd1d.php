<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefault4ed06d01dea2f84c54af53f5db712af8 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('campaign_repo')
            ->addIndex(['repo_id'], ['name' => 'campaign_repo_index_repo_id_68cabd1da6253', 'unique' => false])
            ->addForeignKey(['repo_id'], 'repository', ['id'], [
                'name' => 'campaign_repo_foreign_repo_id_68cabd1da6267',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'indexCreate' => true,
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('github_token')
            ->update();
        $this->table('campaign_repo')
            ->dropForeignKey(['repo_id'])
            ->dropIndex(['repo_id'])
            ->update();
    }
}
