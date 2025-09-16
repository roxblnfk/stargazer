<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultD9eeb8b27ef4c97c6770e7f88e021542 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('campaign')
            ->addColumn('uuid', 'uuid', ['nullable' => false, 'defaultValue' => null, 'comment' => ''])
            ->addColumn('created_at', 'datetime', [
                'nullable' => false,
                'defaultValue' => 'CURRENT_TIMESTAMP',
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('updated_at', 'datetime', [
                'nullable' => false,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('title', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 255, 'comment' => ''])
            ->addColumn('description', 'text', ['nullable' => false, 'defaultValue' => null, 'comment' => ''])
            ->addColumn('visible', 'boolean', ['nullable' => false, 'defaultValue' => true, 'comment' => ''])
            ->addColumn('started_at', 'datetime', [
                'nullable' => false,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('finished_at', 'datetime', [
                'nullable' => true,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addIndex(['finished_at'], ['name' => 'campaign_index_finished_at_68c7b324ead40', 'unique' => false])
            ->setPrimaryKeys(['uuid'])
            ->create();
        $this->table('campaign_repo')
            ->addColumn('created_at', 'datetime', [
                'nullable' => false,
                'defaultValue' => 'CURRENT_TIMESTAMP',
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('updated_at', 'datetime', [
                'nullable' => false,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('campaign_uuid', 'uuid', ['nullable' => false, 'defaultValue' => null, 'comment' => ''])
            ->addColumn('repo_id', 'bigInteger', ['nullable' => false, 'defaultValue' => null, 'comment' => ''])
            ->addColumn('repo_name', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 255, 'comment' => ''])
            ->addColumn('score', 'bigInteger', ['nullable' => false, 'defaultValue' => 1, 'comment' => ''])
            ->addIndex(['campaign_uuid'], ['name' => 'campaign_repo_index_campaign_uuid_68c7b324eaa9e', 'unique' => false])
            ->addForeignKey(['campaign_uuid'], 'campaign', ['uuid'], [
                'name' => 'campaign_repo_foreign_campaign_uuid_68c7b324eaab5',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'indexCreate' => true,
            ])
            ->setPrimaryKeys(['campaign_uuid', 'repo_id'])
            ->create();
        $this->table('campaign_user')
            ->addColumn('created_at', 'datetime', [
                'nullable' => false,
                'defaultValue' => 'CURRENT_TIMESTAMP',
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('updated_at', 'datetime', [
                'nullable' => false,
                'defaultValue' => null,
                'withTimezone' => false,
                'comment' => '',
            ])
            ->addColumn('campaign_uuid', 'uuid', ['nullable' => false, 'defaultValue' => null, 'comment' => ''])
            ->addColumn('user_id', 'bigInteger', ['nullable' => false, 'defaultValue' => null, 'comment' => ''])
            ->addColumn('user_name', 'string', ['nullable' => false, 'defaultValue' => null, 'size' => 255, 'comment' => ''])
            ->addColumn('score', 'bigInteger', ['nullable' => false, 'defaultValue' => 0, 'comment' => ''])
            ->addIndex(['campaign_uuid'], ['name' => 'campaign_user_index_campaign_uuid_68c7b324eabba', 'unique' => false])
            ->addForeignKey(['campaign_uuid'], 'campaign', ['uuid'], [
                'name' => 'campaign_user_foreign_campaign_uuid_68c7b324eabcd',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'indexCreate' => true,
            ])
            ->setPrimaryKeys(['campaign_uuid', 'user_id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('campaign_user')->drop();
        $this->table('campaign_repo')->drop();
        $this->table('campaign')->drop();
    }
}
