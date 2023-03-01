<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'name',
        'slug',
        'ad_title_prefix_outbrain',
        'geos_list',
        'created_at',
    ];
}
