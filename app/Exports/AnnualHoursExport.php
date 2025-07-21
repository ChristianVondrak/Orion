<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnnualHoursExport implements FromCollection, WithHeadings, WithStyles
{
    protected $rows;
    protected $year;

    public function __construct($rows, $year)
    {
        $this->rows = $rows;
        $this->year = $year;
    }

    public function collection()
    {
        return collect($this->rows)->map(function($r) {
            $row = [
                'Nombre' => $r['name'],
                'Email' => $r['email'],
            ];
            for ($m = 1; $m <= 12; $m++) {
                $row[__('Mes').' '.$m] = $r['months'][$m] ?? 0;
            }
            $row['Total'] = array_sum($r['months']);
            return $row;
        });
    }

    public function headings(): array
    {
        $headings = ['Nombre', 'Email'];
        for ($m = 1; $m <= 12; $m++) {
            $headings[] = __(\Carbon\Carbon::create()->month($m)->locale('es')->isoFormat('MMM'));
        }
        $headings[] = 'Total';
        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        // Encabezados en negrita
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $rowCount = count($this->rows);
        // Para cada fila de datos
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->rows[$i];
            // Meses: columnas C a N (3 a 14)
            for ($col = 3; $col <= 14; $col++) {
                $hours = $row['months'][$col-2] ?? 0;
                $cell = $sheet->getCellByColumnAndRow($col, $i+2)->getCoordinate();
                if ($hours < 160 && $hours > 0) {
                    $sheet->getStyle($cell)->getFill()->setFillType('solid')->getStartColor()->setRGB('fee2e2');
                    $sheet->getStyle($cell)->getFont()->getColor()->setRGB('b91c1c');
                } elseif ($hours >= 160) {
                    $sheet->getStyle($cell)->getFill()->setFillType('solid')->getStartColor()->setRGB('dcfce7');
                    $sheet->getStyle($cell)->getFont()->getColor()->setRGB('166534');
                }
            }
            // Total: columna O (15)
            $total = array_sum($row['months']);
            $cellTotal = $sheet->getCellByColumnAndRow(15, $i+2)->getCoordinate();
            if ($total < 160*12 && $total > 0) {
                $sheet->getStyle($cellTotal)->getFill()->setFillType('solid')->getStartColor()->setRGB('fee2e2');
                $sheet->getStyle($cellTotal)->getFont()->getColor()->setRGB('b91c1c');
            } elseif ($total >= 160*12) {
                $sheet->getStyle($cellTotal)->getFill()->setFillType('solid')->getStartColor()->setRGB('dcfce7');
                $sheet->getStyle($cellTotal)->getFont()->getColor()->setRGB('166534');
            }
        }
        return [];
    }
} 