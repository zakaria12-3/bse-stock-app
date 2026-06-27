<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->constrained()->restrictOnDelete();
            $table->string('reference', 50)->unique();
            $table->string('ref_fr', 50)->nullable();
            $table->string('designation', 150)->index();
            $table->string('designation_2', 150)->nullable();
            $table->decimal('purchase_price_eur', 15, 3)->default(0);
            $table->decimal('unit_pr_ht', 15, 3)->default(0);
            $table->decimal('selling_price', 15, 3)->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('min_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
