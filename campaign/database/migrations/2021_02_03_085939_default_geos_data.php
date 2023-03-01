<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DefaultGeosData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('geos')->insert(
            array(
                'name' => 'Switzerland + Belgium + Luxembourg + France + Canada',
                'slug' => "FR",
                'countries_ISO_list' => "CA,CH,BE,FR,LU",
            )
        );
        DB::table('geos')->insert(
            array(
                'name' => 'Canada',
                'slug' => "FRCA",
                'countries_ISO_list' => "CA",
            )
        );
        DB::table('geos')->insert(
            array(
                'name' => 'Germany + Austria + Switzerland',
                'slug' => "DE",
                'countries_ISO_list' => "CH,DE,AT",
            )
        );
        DB::table('geos')->insert(
            array(
                'name' => 'Switzerland',
                'slug' => "DESW",
                'countries_ISO_list' => "CH",
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
