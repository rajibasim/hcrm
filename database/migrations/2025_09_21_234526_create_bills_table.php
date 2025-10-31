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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->integer('financial_year')->default('0');
            $table->string('bill_number')->unique();
            $table->date('invoice_date')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->cascadeOnDelete();
            $table->foreignId('sales_person_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->date('delivery_status_update_date')->nullable();
            $table->foreignId('delivery_status_id')->nullable()->constrained('delivery_statuses')->cascadeOnDelete();
            $table->decimal('billed_amount', 10, 2)->default(0)->nullable();
            $table->decimal('damage_amount', 10, 2)->default(0)->nullable();
            $table->decimal('return_amount', 10, 2)->default(0)->nullable();
            $table->decimal('adjusment_percent', 10, 2)->default(0)->nullable();
            $table->decimal('adjusment_amount', 10, 2)->default(0)->nullable();
            $table->decimal('online_amount', 10, 2)->default(0)->nullable();
            $table->decimal('cash_amount', 10, 2)->default(0)->nullable();
            $table->decimal('balance_amount', 10, 2)->default(0)->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('bills');
    }
};
