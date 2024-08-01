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
        Schema::create('return_entries', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no')->nullable();
            $table->date('return_date')->nullable();
            $table->foreignId('sales_person_id')->nullable()->constrained('sales_person')->cascadeOnDelete();
            $table->foreignId('beat_id')->nullable()->constrained('beats')->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->cascadeOnDelete();
            $table->string('note')->nullable();
            $table->decimal('total_amount', 10,2)->nullable();
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
        Schema::dropIfExists('return_entries');
    }
};
