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
        Schema::create('balance_sheets', function (Blueprint $table) {
            $table->id();
            $table->integer('financial_year')->default('0');
            $table->date('entry_date')->nullable();
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->enum('purpose', ['1', '2', '3', '4', '5'])->nullable()->comment('1 => Asset, 2 => Liability, 3 => Equity, 4 => Income, 5 => Expense');
            $table->enum('type', ['1', '2'])->nullable()->comment('1 => CR, 2 => DR');
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
        Schema::dropIfExists('balance_sheets');
    }
};
