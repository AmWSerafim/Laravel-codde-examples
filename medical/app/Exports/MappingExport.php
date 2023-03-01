<?php
namespace App\Exports;

use App\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;

class MappingExport implements FromArray
{
    protected $mapping;

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function array(): array
    {
        return $this->mapping;
    }
}
