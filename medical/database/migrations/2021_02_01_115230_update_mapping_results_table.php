<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMappingResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mapping_results', function (Blueprint $table) {
            $table->text('total_billed')->change();
            $table->text('total_received')->change();
            $table->text('total_difference')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mapping_results', function (Blueprint $table) {
            $table->double('total_billed')->change();
            $table->double('total_received')->change();
            $table->double('total_difference')->change();
        });
    }
}
