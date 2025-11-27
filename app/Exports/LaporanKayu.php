<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanKayu implements FromQuery, WithHeadings, WithMapping
{
    protected Builder $query;
    protected array $columns;

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query  Eloquent builder (gunakan ->with(...) untuk eager loads)
     * @param array $columns  Array definisi: [['label'=>'Nama Kolom','field'=>'relation.field_or_attribute'], ...]
     */
    public function __construct(Builder $query, array $columns)
    {
        $this->query = $query;
        $this->columns = $columns;
    }

    public function query()
    {
        // FromQuery expects a Builder returning models
        return $this->query;
    }

    public function headings(): array
    {
        return array_map(fn($c) => $c['label'] ?? $c['field'], $this->columns);
    }

    public function map($row): array
    {
        // $row adalah Eloquent model; pakai data_get untuk dot notation
        return array_map(function ($col) use ($row) {
            $value = data_get($row, $col['field']);

            // formatting sederhana: tanggal -> ISO, Carbon -> string, boolean -> 0/1
            if ($value instanceof Carbon) {
                return $value->toDateString();
            }
            if ($value instanceof \DateTime) {
                return Carbon::instance($value)->toDateString();
            }
            if (is_bool($value)) {
                return $value ? '1' : '0';
            }
            // untuk numeric dan string, kembalikan apa adanya
            return $value;
        }, $this->columns);
    }
}
