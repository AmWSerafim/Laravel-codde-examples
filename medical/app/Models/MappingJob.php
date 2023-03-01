<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingJob extends Model
{
    use HasFactory;

    protected $table = 'mapping_jobs';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'mapping_result_id',
        'creator_company_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mappingResult()
    {
        return $this->belongsTo(MappingResult::class);
    }
}
