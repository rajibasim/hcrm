<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('return_entry_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_entry_id')->nullable()->constrained('return_entries')->cascadeOnDelete();
            $table->date('return_date')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->decimal('product_qty', 10,2)->nullable();
            $table->decimal('product_unit_price', 10,2)->nullable();
            $table->decimal('sub_total', 10,2)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->tinyInteger('is_active')->default('1')->comment('1 => Active , 0 => In-Active');
            $table->tinyInteger('is_deleted')->default('0');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_entry_products');
    }
};
