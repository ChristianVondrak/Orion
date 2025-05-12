<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TerminationsExport implements FromCollection, WithHeadings
{
    protected array $rows;
    public function __construct(array $rows) { $this->rows = $rows; }

    public function collection()
    {
        return collect($this->rows)->map(fn($r) => [
            'Name'             => $r->name,
            'Termination Date' => $r->termination_date,
            'Reason'           => $r->reason,
        ]);
    }

    public function headings(): array
    {
        return ['Name','Termination Date','Reason'];
    }
}

