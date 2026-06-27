<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('labor_hours', 10, 3)->default(0)->after('discount');
            $table->decimal('labor_rate', 15, 3)->default(0)->after('labor_hours');
            $table->decimal('labor_total', 15, 3)->default(0)->after('labor_rate');
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['labor_hours', 'labor_rate', 'labor_total']);
        });
    }
};
