<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingResult extends Model
{
    use HasFactory;

    protected $table = 'mapping_results';
    public $timestamps = true;

    protected $fillable = [
        'mapping_id',
        'billed_document',
        'received_document',
        'result_file',
        'total_billed',
        'total_received',
        'total_difference',
        'mapping_result',
        'creator_company_id'
    ];

    public function mapping()
    {
        return $this->belongsTo(Mapping::class);
    }
}
