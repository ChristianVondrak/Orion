<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NewHiresExport implements FromCollection, WithHeadings
{
    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return collect($this->rows)->map(fn($r) => [
            'Name'       => $r->name,
            'Country'    => $r->country,
            'Position'   => $r->position,
            'Start Date' => $r->start_date,
        ]);
    }

    public function headings(): array
    {
        return ['Name','Country','Position','Start Date'];
    }
}
