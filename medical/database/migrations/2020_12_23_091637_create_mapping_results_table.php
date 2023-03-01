<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapping_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mapping_id');
            $table->string('billed_document');
            $table->string('received_document');
            $table->string('result_file');
            $table->double('total_billed');
            $table->double('total_received');
            $table->double('total_difference');
            $table->text('mapping_result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mapping_results');
    }
}
