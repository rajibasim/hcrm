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
        Schema::create('balance_sheet_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('financial_year')->default('0');
            $table->date('entry_date')->nullable();
            $table->tinyInteger('purpose')->nullable()->comment('1 => Inventory(Add), 2 => Expenditure, 3 => Balance Transfer, 4 => Invest, 5 => Withdraw, 6 => Bill Inventory(Deduct), 7 => Bill Payment');
            $table->tinyInteger('type')->nullable()->comment('1 => Online, 2 => Cash, 4 => Cash to Online, 4 =>  Online to Cash');
            $table->tinyInteger('expenditure_purpose')->nullable()->comment('1 => Damage, 2 => Daliy Expenses, 3 => Salary, 4 => Rent, 5 => Oil, 6 => Other');
            $table->integer('invoice_number')->nullable();
            $table->integer('bill_id')->nullable();
            $table->decimal('inventory_amount', 15, 2)->default(0.00);
            $table->decimal('online_amount', 15, 2)->default(0.00);
            $table->decimal('cash_amount', 15, 2)->default(0.00);
            $table->decimal('opening_inventory_amount', 15, 2)->default(0.00);
            $table->decimal('opening_online_amount', 15, 2)->default(0.00);
            $table->decimal('opening_cash_amount', 15, 2)->default(0.00);
            $table->decimal('closing_inventory_amount', 15, 2)->default(0.00);
            $table->decimal('closing_online_amount', 15, 2)->default(0.00);
            $table->decimal('closing_cash_amount', 15, 2)->default(0.00);
            $table->decimal('claim_amount', 15, 2)->default(0.00);
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
        Schema::dropIfExists('balance_sheet_transactions');
    }
};
