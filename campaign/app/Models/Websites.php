<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Websites extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'name',
        'slug',
        'url',
        'created_at',
    ];
}
