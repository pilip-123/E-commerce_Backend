<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportService
{
    protected Spreadsheet $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
    }

    public function createSheet(string $title, array $headers, array $rows): BinaryFileResponse
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        $this->addHeaders($sheet, $headers);
        $this->addRows($sheet, $rows, count($headers));

        $path = tempnam(sys_get_temp_dir(), 'export_') . '.xlsx';
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($path);

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $title . '_' . now()->format('Y-m-d') . '.xlsx'
        );

        return $response;
    }

    protected function addHeaders($sheet, array $headers): void
    {
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A1:' . chr(64 + count($headers)) . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    protected function addRows($sheet, array $rows, int $colCount): void
    {
        $rowIdx = 2;
        foreach ($rows as $row) {
            foreach ($row as $i => $value) {
                $sheet->setCellValue(chr(65 + $i) . $rowIdx, $value ?? '');
            }
            $rowIdx++;
        }

        $lastRow = $rowIdx - 1;
        if ($lastRow >= 2) {
            $range = 'A2:' . chr(64 + $colCount) . $lastRow;
            $sheet->getStyle($range)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
        }
    }
}
