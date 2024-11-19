<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// 資料庫連線
include_once("dropdown_list/dbcontroller.php");
$db_handle = new DBController();
$pdo = $db_handle->getConnection();

// SQL 查詢分組加總
$query = "
    SELECT name, 
        SUM(CASE WHEN type = 1 THEN carbon ELSE 0 END) AS category1_total,
        SUM(CASE WHEN type = 3 THEN carbon ELSE 0 END) AS category3_total
    FROM count_carbon
    GROUP BY name
";

// 使用 mysqli 查詢
$stmt = $pdo->query($query); // 此處 $pdo 是 mysqli 連接物件
$results = [];  // 初始化空陣列來儲存查詢結果

// 從 mysqli 結果集中獲取數據
while ($row = $stmt->fetch_assoc()) {
    $results[] = $row;  // 把每行數據加入 $results 陣列
}

// 創建新的 Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 填寫表頭
$sheet->setCellValue('A1', '氣體名稱');
$sheet->setCellValue('B1', '類別1 (t-CO2e)');
$sheet->setCellValue('C1', '類別3 (t-CO2e)');
$sheet->setCellValue('D1', '總量 (t-CO2e)');
$sheet->setCellValue('E1', '氣體占比 (%)');

// 設置表頭樣式
$headerStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'font' => [
        'bold' => true,
    ],
];
$sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

// 動態填充數據
$row = 2; // 從第2行開始
$grandTotal = 0;

// 計算所有總量
foreach ($results as $resultRow) {
    $grandTotal += $resultRow['category1_total'] + $resultRow['category3_total'];
}

// 填入數據並計算百分比
foreach ($results as $resultRow) {
    $name = $resultRow['name'];
    $category1 = $resultRow['category1_total'];
    $category3 = $resultRow['category3_total'];
    $total = $category1 + $category3;
    $percentage = ($grandTotal > 0) ? ($total / $grandTotal * 100) : 0;

    $sheet->setCellValue('A' . $row, $name);
    $sheet->setCellValue('B' . $row, $category1);
    $sheet->setCellValue('C' . $row, $category3);
    $sheet->setCellValue('D' . $row, $total);
    $sheet->setCellValue('E' . $row, round($percentage, 2) . '%');
    $row++;
}

// 計算小計
$totalCategory1 = 0;
$totalCategory3 = 0;

foreach ($results as $resultRow) {
    $totalCategory1 += $resultRow['category1_total'];
    $totalCategory3 += $resultRow['category3_total'];
}

$sheet->setCellValue('A' . $row, '小計');
$sheet->setCellValue('B' . $row, $totalCategory1);
$sheet->setCellValue('C' . $row, $totalCategory3);
$sheet->setCellValue('D' . $row, $grandTotal);
$sheet->setCellValue('E' . $row, '100%');

// 設置合計行樣式
$subtotalStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($subtotalStyle);

// 顯示類別占比（小計下方）
$row++; // 移動到下一行
$category1Percentage = ($totalCategory1 > 0) ? ($totalCategory1 / $grandTotal * 100) : 0;
$category3Percentage = ($totalCategory3 > 0) ? ($totalCategory3 / $grandTotal * 100) : 0;

$sheet->setCellValue('A' . $row, '類別占比');
$sheet->setCellValue('B' . $row, round($category1Percentage, 2) . '%');
$sheet->setCellValue('C' . $row, round($category3Percentage, 2) . '%');
$sheet->setCellValue('D' . $row, '');
$sheet->setCellValue('E' . $row, '');

// 設置類別占比樣式
$categoryPercentageStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($categoryPercentageStyle);

// 設置邊框
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];
$sheet->getStyle('A2:E' . $row)->applyFromArray($styleArray);

// 輸出 Excel 文件
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="carbon_report_with_percentage.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>
