<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Geos extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'name',
        'slug',
        'countries_ISO_list',
        'created_at',
        'countries_outbrain_ISO_list',
        'countries_outbrain_ids_list',
    ];

    public function accounts(){

        return $this->belongsToMany('App\Models\Accounts', 'geos_accounts');

    }
}
