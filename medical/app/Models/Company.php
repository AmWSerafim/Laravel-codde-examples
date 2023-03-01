<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'slug',
        'address',
        'description',
        'comment',
        'addition_data',
        'created_at',
        'updated_at'
    ];

    public function users() {

        return $this->belongsToMany(User::class,'companies_users');

    }
}
