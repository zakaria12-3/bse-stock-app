<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'total_ht')) {
                $table->decimal('total_ht', 15, 3)->default(0)->after('min_stock');
            }

            $table->foreignId('unit_id')->nullable()->change();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'total_ht')) {
                $table->dropColumn('total_ht');
            }

            $table->foreignId('unit_id')->nullable(false)->change();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('contact_person')->nullable(false)->change();
        });
    }
};
