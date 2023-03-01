<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountiesCodesToGeosOutbrain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geos', function (Blueprint $table) {
            $table->string('countries_outbrain_ISO_list', 255)->after('countries_ISO_list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geos', function (Blueprint $table) {
            $table->dropColumn('countries_outbrain_ISO_list');
        });
    }
}
