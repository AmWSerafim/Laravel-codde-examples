<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Options extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'tracking_taboola',
        'tracking_outbrain',
        'OB_token',
        'OB_token_update_date'
    ];
}
