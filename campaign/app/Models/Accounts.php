<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'name',
        'slug',
        'platform',
        'api_account_id',
        'website_id',
        'created_at',
    ];

    public function website()
    {
        return $this->hasOne(Websites::class, 'id', 'website_id');
    }

    public function geos(){

        return $this->belongsToMany('App\Models\Geos', 'geos_accounts');

    }
}
