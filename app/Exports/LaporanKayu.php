<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanKayu implements FromCollection, WithHeadings, WithMapping
{
    protected $query;
    protected array $columns;

    public function __construct($query, array $columns)
    {
        $this->query = $query;
        $this->columns = $columns;
    }

    public function collection()
    {
        // Query Builder biasa → get() jadi Collection
        return $this->query->get();
    }

    public function headings(): array
    {
        return array_map(fn($c) => $c['label'], $this->columns);
    }

    public function map($row): array
    {
        return array_map(function ($col) use ($row) {
            return data_get($row, $col['field']);
        }, $this->columns);
    }
}
