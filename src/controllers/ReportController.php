<?php

require_once __DIR__ . '/../models/Report.php';

use Models\Report;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;

class ReportController
{
    public static function index(PDO $db): void
    {
        AuthController::checkAuth();

        $reports = Report::getReports($db);

        include '../src/views/layouts/header.php';
        include '../src/views/reports/index.php';
        include '../src/views/layouts/footer.php';
        exit;
    }

    public static function export(PDO $db): void
    {
        AuthController::checkAuth();

        $type = isset($_GET['type']) ? (string) $_GET['type'] : '';
        $format = isset($_GET['format']) ? (string) $_GET['format'] : '';
        $report = Report::getReport($db, $type);

        if ($report === null) {
            http_response_code(404);
            exit('Отчёт не найден');
        }

        if ($format === 'xlsx') {
            self::exportExcel($report);
        }

        if ($format === 'docx') {
            self::exportWord($report);
        }

        http_response_code(400);
        exit('Неверный формат выгрузки');
    }

    private static function exportExcel(array $report): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Отчёт');
        $sheet->setCellValue('A1', $report['title']);

        $columnIndex = 1;
        $headerRow = 3;

        foreach ($report['columns'] as $label) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex) . $headerRow, $label);
            $columnIndex++;
        }

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $headerRow . ':' . Coordinate::stringFromColumnIndex(count($report['columns'])) . $headerRow)->getFont()->setBold(true);

        $dataRow = 4;

        if ($report['rows'] === []) {
            $sheet->setCellValue('A4', 'Данные для отчёта отсутствуют');
        } else {
            foreach ($report['rows'] as $row) {
                $columnIndex = 1;

                foreach (array_keys($report['columns']) as $key) {
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex) . $dataRow, (string) ($row[$key] ?? ''));
                    $columnIndex++;
                }

                $dataRow++;
            }
        }

        foreach (range(1, count($report['columns'])) as $column) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $report['filename'] . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private static function exportWord(array $report): void
    {
        Settings::setZipClass(Settings::PCLZIP);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText($report['title'], ['bold' => true, 'size' => 16]);
        $section->addText($report['description']);
        $section->addTextBreak(1);

        if ($report['rows'] === []) {
            $section->addText('Данные для отчёта отсутствуют');
        } else {
            $table = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '4a5566',
                'cellMargin' => 80,
            ]);

            $table->addRow();

            foreach ($report['columns'] as $label) {
                $table->addCell(2200)->addText($label, ['bold' => true]);
            }

            foreach ($report['rows'] as $row) {
                $table->addRow();

                foreach (array_keys($report['columns']) as $key) {
                    $table->addCell(2200)->addText((string) ($row[$key] ?? ''));
                }
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="' . $report['filename'] . '.docx"');
        header('Cache-Control: max-age=0');

        $writer = WordIOFactory::createWriter($phpWord, 'Word2007');
        $tempFile = tempnam(sys_get_temp_dir(), 'report_docx_');

        if ($tempFile === false) {
            http_response_code(500);
            exit('Не удалось сформировать временный файл отчёта');
        }

        $docxFile = $tempFile . '.docx';
        $writer->save($docxFile);
        readfile($docxFile);
        @unlink($docxFile);
        @unlink($tempFile);
        exit;
    }
}
