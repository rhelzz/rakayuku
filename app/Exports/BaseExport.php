<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

abstract class BaseExport implements WithStyles, ShouldAutoSize, WithCustomStartCell
{
    use RegistersEventListeners;

    protected array $rows = [];
    protected int $titleRowOffset = 0;
    protected int $headerRow = 0;
    protected int $dataStartRow = 0;
    protected int $footerRow = 0;

    public function startCell(): string
    {
        return 'A1';
    }

    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        try {
            $lastColLetter = $sheet->getHighestColumn();
            if (!empty($lastColLetter) && $lastColLetter !== 'A') {
                $sheet->mergeCells('A1:' . $lastColLetter . '1');
            }
        } catch (\Exception $e) {
        }
    }

    public function styles(Worksheet $sheet): array
    {
        $titleStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 14,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ];

        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '065F46'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ];

        $dataStyle = [
            'font' => [
                'name' => 'Calibri',
                'size' => 10,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $currencyDataStyle = [
            'font' => [
                'name' => 'Calibri',
                'size' => 10,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
            'numberFormat' => [
                'formatCode' => '#,##0.00',
            ],
        ];

        $alternateRowStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F0FDF4'],
            ],
            'font' => [
                'name' => 'Calibri',
                'size' => 10,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $alternateCurrencyStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F0FDF4'],
            ],
            'font' => [
                'name' => 'Calibri',
                'size' => 10,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
            'numberFormat' => [
                'formatCode' => '#,##0.00',
            ],
        ];

        $footerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'],
            ],
            'font' => [
                'bold' => true,
                'italic' => true,
                'color' => ['rgb' => '374151'],
                'size' => 10,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ];

        $styles = [];

        if (empty($this->rows)) {
            return $styles;
        }

        $columnCount = 0;
        foreach ($this->rows as $row) {
            if (is_array($row) && count($row) > $columnCount) {
                $columnCount = count($row);
            }
        }
        if ($columnCount === 0) {
            return $styles;
        }

        $lastColumn = $this->getColumnLetter($columnCount);

        $styles['A1:' . $lastColumn . '1'] = $titleStyle;

        $styles['A2:' . $lastColumn . '2'] = $headerStyle;

        $dataRowStart = 3;
        $lastRow = count($this->rows) + 1;

        for ($row = $dataRowStart; $row < $lastRow - 1; $row++) {
            if (($row - $dataRowStart) % 2 === 0) {
                $styles['A' . $row . ':' . $lastColumn . $row] = $dataStyle;
            } else {
                $styles['A' . $row . ':' . $lastColumn . $row] = $alternateRowStyle;
            }
        }

        $styles['A' . ($lastRow - 1) . ':' . $lastColumn . ($lastRow - 1)] = $footerStyle;

        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(22);

        for ($col = 1; $col <= $columnCount; $col++) {
            $colLetter = $this->getColumnLetter($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $sheet->setAutoFilter('A2:' . $lastColumn . '2');

        return $styles;
    }

    protected function getColumnLetter(int $columnNumber): string
    {
        $letter = '';
        while ($columnNumber > 0) {
            $columnNumber--;
            $letter = chr(65 + ($columnNumber % 26)) . $letter;
            $columnNumber = intdiv($columnNumber, 26);
        }
        return $letter;
    }

    public function array(): array
    {
        return $this->rows;
    }
}
