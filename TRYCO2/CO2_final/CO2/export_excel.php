<?php
require 'cm_index.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// 創建新的 Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 設定標題和表頭
$sheet->setCellValue('A1', '2023');
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// 填入表頭
$sheet->setCellValue('B2', '類別1');
$sheet->setCellValue('C2', '類別3');
$sheet->setCellValue('D2', '小計 (t-CO2e)');
$sheet->mergeCells('D2:E2');

// 設定排放項目
$data = [
    ['CO2排放量(t-CO2e)', 0, 2.32108, 2.32108],
    ['CH4排放量(t-CO2e)', 0, 0, 0],
    ['N2O排放量(t-CO2e)', 0, 0, 0],
    ['單一類別總量(t-CO2e)', 0, 2.32108, 2.32108],
    ['占比', '0%', '100%', '100.00%']
];

// 將數據寫入表格
$row = 3;
foreach ($data as $item) {
    $sheet->setCellValue('A' . $row, $item[0]);
    $sheet->setCellValue('B' . $row, $item[1]);
    $sheet->setCellValue('C' . $row, $item[2]);
    $sheet->setCellValue('D' . $row, $item[3]);
    $row++;
}

// 設定下一年的數據（例如：2024）
$sheet->setCellValue('A9', '2024');
$sheet->mergeCells('A9:F9');
$sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$data2024 = [
    ['CO2排放量(t-CO2e)', 0, 1.68734, 1.68734],
    ['CH4排放量(t-CO2e)', 0, 0, 0],
    ['N2O排放量(t-CO2e)', 0, 0, 0],
    ['單一類別總量(t-CO2e)', 0, 1.68734, 1.68734],
    ['占比', '0%', '100%', '100.00%']
];

$row = 11;
foreach ($data2024 as $item) {
    $sheet->setCellValue('A' . $row, $item[0]);
    $sheet->setCellValue('B' . $row, $item[1]);
    $sheet->setCellValue('C' . $row, $item[2]);
    $sheet->setCellValue('D' . $row, $item[3]);
    $row++;
}

// 設定邊框
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];
$sheet->getStyle('A2:D16')->applyFromArray($styleArray);

// 輸出 Excel 文件
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="report.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
