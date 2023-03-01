<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class DefaultOptionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $date = Carbon::create(1970, 1, 1, 0, 0, 0);
        $OB_token_update_date = $date->format('Y-m-d H:i:s');

        DB::table('options')->insert(
            array(
                'tracking_taboola'      => '',
                'tracking_outbrain'     => '',
                'OB_token'              => '',
                'OB_token_update_date'  => $OB_token_update_date
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
