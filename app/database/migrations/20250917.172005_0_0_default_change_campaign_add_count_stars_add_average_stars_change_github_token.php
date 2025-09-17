<?php

declare(strict_types=1);

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultF9be28a98b3a8f02a5299237d1215b4d extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('campaign')
            ->addColumn('count_stars', 'bigInteger', ['nullable' => false, 'defaultValue' => 0, 'comment' => ''])
            ->addColumn('old_stars_coefficient', 'decimal', [
                'nullable' => false,
                'defaultValue' => 1.0,
                'precision' => 4,
                'scale' => 2,
                'comment' => '',
            ])->update();
    }

    public function down(): void
    {
        $this->table('campaign')
            ->dropColumn('count_stars')
            ->dropColumn('old_stars_coefficient')
            ->update();
    }
}
