<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    use HasFactory;

    protected $table = 'mappings';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'slug',
        'files_fields',
        'mapped_fields',
        'created_at',
        'updated_at',
        'user_id',
        'creator_company_id'
    ];
}
