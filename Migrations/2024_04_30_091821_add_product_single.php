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
        \Log::info('SingleProductOrder migrate up');
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_single')->default(0)->nullable(false)->after('active')->comment('是否只允许单独结算');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Log::info('SingleProductOrder migrate down');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_single');
        });
    }
};
