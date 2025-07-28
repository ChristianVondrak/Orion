<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AnnualHoursExport implements FromCollection, WithHeadings, WithStyles
{
    private Collection $rows;
    private int $year;

    private const MONTHS_IN_YEAR    = 12;
    private const MONTHLY_MIN_HOURS = 160;
    private const TOTAL_MIN_HOURS   = self::MONTHLY_MIN_HOURS * self::MONTHS_IN_YEAR;

    private array $monthLabels;

    public function __construct(Collection $rows, int $year)
    {
        $this->rows        = $rows;
        $this->year        = $year;
        $this->monthLabels = $this->generateMonthLabels();
    }

    public function collection(): Collection
    {
        return $this->rows->map(fn(array $row) => $this->formatRow($row));
    }

    public function headings(): array
    {
        return array_merge(['Name', 'Email'], $this->monthLabels, ['Total']);
    }

    public function styles(Worksheet $sheet): array
    {
        // bold header
        $sheet->getStyle('A1:' . $sheet->getCellByColumnAndRow(self::MONTHS_IN_YEAR + 2, 1)->getCoordinate())
            ->getFont()
            ->setBold(true);

        foreach ($this->rows as $i => $row) {
            $excelRow = $i + 2;
            // estilo mensual
            for ($col = 3; $col <= self::MONTHS_IN_YEAR + 2; $col++) {
                $hours = $row['months'][$col - 2] ?? 0;
                $cell  = $sheet->getCellByColumnAndRow($col, $excelRow)->getCoordinate();

                if ($hours > 0 && $hours < self::MONTHLY_MIN_HOURS) {
                    $this->applyCellStyle($sheet, $cell, 'fee2e2', 'b91c1c');
                } elseif ($hours >= self::MONTHLY_MIN_HOURS) {
                    $this->applyCellStyle($sheet, $cell, 'dcfce7', '166534');
                }
            }

            // estilo total
            $totalCell = $sheet->getCellByColumnAndRow(self::MONTHS_IN_YEAR + 3, $excelRow)->getCoordinate();
            $total     = array_sum($row['months'] ?? []);

            if ($total > 0 && $total < self::TOTAL_MIN_HOURS) {
                $this->applyCellStyle($sheet, $totalCell, 'fee2e2', 'b91c1c');
            } elseif ($total >= self::TOTAL_MIN_HOURS) {
                $this->applyCellStyle($sheet, $totalCell, 'dcfce7', '166534');
            }
        }

        return [];
    }

    private function generateMonthLabels(): array
    {
        $labels = [];
        for ($m = 1; $m <= self::MONTHS_IN_YEAR; $m++) {
            $labels[] = Carbon::create()->month($m)->isoFormat('MMM');
        }
        return $labels;
    }

    private function formatRow(array $row): array
    {
        $formatted = [
            'Name'  => $row['name']  ?? '',
            'Email' => $row['email'] ?? '',
        ];

        foreach ($this->monthLabels as $index => $label) {
            $formatted[$label] = $row['months'][$index + 1] ?? 0;
        }

        $formatted['Total'] = array_sum($row['months'] ?? []);
        return $formatted;
    }

    private function applyCellStyle(Worksheet $sheet, string $cell, string $fill, string $font): void
    {
        $style = $sheet->getStyle($cell);
        $style->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setRGB($fill);
        $style->getFont()
            ->getColor()
            ->setRGB($font);
    }
}
