<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('label', 80)->after('name');
            $table->integer('created_by')->nullable()->after('guard_name');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->tinyInteger('is_active')->default('1')->comment('1 => Active , 0 => In-Active')->after('updated_by');
            $table->tinyInteger('is_deleted')->default('0')->after('is_active');
            $table->dateTime('deleted_at')->nullable()->after('is_deleted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('label');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('is_active');
            $table->dropColumn('is_deleted');
            $table->dropColumn('deleted_at');
        });
    }
};
