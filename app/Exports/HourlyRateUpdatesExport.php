<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HourlyRateUpdatesExport implements FromCollection, WithHeadings
{
    /**
     * @var array<int, object>
     */
    protected array $rows;

    /**
     * @param  array<int, object>  $rows  Plain objects with keys:
     *                                   name, updated_at, previous_rate, new_rate
     */
    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * Return a collection of rows for the sheet.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->rows)->map(fn($r) => [
            'Name'           => $r->name,
            'Updated At'     => $r->updated_at,
            'Previous Rate'  => number_format($r->previous_rate, 2),
            'New Rate'       => number_format($r->new_rate, 2),
        ]);
    }

    /**
     * Define the header row.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Name',
            'Updated At',
            'Previous Rate',
            'New Rate',
        ];
    }
}

