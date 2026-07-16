<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf as PdfWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportService
{
    const HEADER_BG = '15803D';
    const HEADER_FG = 'FFFFFF';
    const ROW_EVEN_BG = 'F0FDF4';
    const ROW_ODD_BG = 'FFFFFF';

    protected Spreadsheet $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
    }

    public function export(string $title, array $headers, array $rows, string $format = 'xlsx'): BinaryFileResponse
    {
        return match ($format) {
            'csv' => $this->createCsv($title, $headers, $rows),
            'html' => $this->createHtml($title, $headers, $rows),
            'doc' => $this->createDoc($title, $headers, $rows),
            'pdf' => $this->createPdf($title, $headers, $rows),
            default => $this->createXlsx($title, $headers, $rows),
        };
    }

    protected function buildSpreadsheet(string $title, array $headers, array $rows): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($title, 0, 31));

        $colCount = count($headers);
        $lastCol = Coordinate::stringFromColumnIndex($colCount);

        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('15803D'));
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getRowDimension(1)->setRowHeight(30);

        $this->addHeaders($sheet, $headers, 2);
        $this->addRows($sheet, $rows, $colCount, 3);

        $lastRow = 2 + count($rows);
        foreach ($headers as $i => $header) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($col)->setAutoSize(false);
            $width = mb_strlen($header) + 4;
            $sheet->getColumnDimension($col)->setWidth(min(max($width, 10), 40));
        }

        $sheet->setAutoFilter("A2:{$lastCol}2");
        $sheet->freezePane('A3');
    }

    protected function addHeaders($sheet, array $headers, int $row): void
    {
        $colCount = count($headers);
        $lastCol = Coordinate::stringFromColumnIndex($colCount);

        foreach ($headers as $i => $header) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $row, $header);
        }

        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => self::HEADER_FG],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::HEADER_BG],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '166534']],
            ],
        ]);

        $sheet->getRowDimension($row)->setRowHeight(22);
    }

    protected function addRows($sheet, array $rows, int $colCount, int $startRow): void
    {
        $lastCol = Coordinate::stringFromColumnIndex($colCount);

        foreach ($rows as $idx => $row) {
            $rowNum = $startRow + $idx;
            $bgColor = $idx % 2 === 0 ? self::ROW_EVEN_BG : self::ROW_ODD_BG;

            foreach ($row as $i => $value) {
                $col = Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue($col . $rowNum, $value ?? '');
            }

            $range = "A{$rowNum}:{$lastCol}{$rowNum}";
            $sheet->getStyle($range)->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
            ]);

            $sheet->getRowDimension($rowNum)->setRowHeight(20);
        }
    }

    public function createXlsx(string $title, array $headers, array $rows): BinaryFileResponse
    {
        $this->buildSpreadsheet($title, $headers, $rows);

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

    public function createCsv(string $title, array $headers, array $rows): BinaryFileResponse
    {
        $this->buildSpreadsheet($title, $headers, $rows);

        $path = tempnam(sys_get_temp_dir(), 'export_') . '.csv';
        $writer = new Csv($this->spreadsheet);
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setSheetIndex(0);
        $writer->save($path);

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $title . '_' . now()->format('Y-m-d') . '.csv'
        );

        return $response;
    }

    public function createHtml(string $title, array $headers, array $rows): BinaryFileResponse
    {
        $this->buildSpreadsheet($title, $headers, $rows);

        $path = tempnam(sys_get_temp_dir(), 'export_') . '.html';
        $writer = new HtmlWriter($this->spreadsheet);
        $writer->save($path);

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $title . '_' . now()->format('Y-m-d') . '.html'
        );

        return $response;
    }

    public function createDoc(string $title, array $headers, array $rows): BinaryFileResponse
    {
        $this->buildSpreadsheet($title, $headers, $rows);

        $path = tempnam(sys_get_temp_dir(), 'export_') . '.doc';
        $writer = new HtmlWriter($this->spreadsheet);
        $writer->save($path);

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $title . '_' . now()->format('Y-m-d') . '.doc'
        );

        return $response;
    }

    public function createPdf(string $title, array $headers, array $rows): BinaryFileResponse
    {
        $this->buildSpreadsheet($title, $headers, $rows);

        $path = tempnam(sys_get_temp_dir(), 'export_') . '.pdf';
        $writer = new PdfWriter($this->spreadsheet);
        $writer->save($path);

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $title . '_' . now()->format('Y-m-d') . '.pdf'
        );

        return $response;
    }
}
