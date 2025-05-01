<?php

namespace App\Exports;

use App\Models\worksnapUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActivityIndexExport implements FromCollection, WithHeadings
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return collect($this->rows)->map(function($r){
            return [
                'Name'           => $r->name,
                'Email'          => $r->email,
                'Activity Index' => $r->activity_index.'%',
            ];
        });
    }

    public function headings(): array
    {
        return ['Name','Email','Activity Index'];
    }
}
