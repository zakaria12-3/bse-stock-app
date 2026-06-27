<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE products
            SET total_ht = ROUND(quantity * unit_pr_ht, 3)
            WHERE COALESCE(total_ht, 0) = 0
              AND COALESCE(quantity, 0) > 0
              AND COALESCE(unit_pr_ht, 0) > 0
        ");
    }

    public function down(): void
    {
        // No-op: this migration only repairs missing computed values.
    }
};
