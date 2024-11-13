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

// 取得加總結果
$query = "
    SELECT 
        SUM(CASE WHEN type = 1 THEN carbon ELSE 0 END) AS category1_total,
        SUM(CASE WHEN type = 3 THEN carbon ELSE 0 END) AS category3_total
    FROM count_carbon
";
$stmt = $pdo->query($query);

$result = mysqli_fetch_assoc($stmt);


// 計算總碳排放量
$total_carbon = $result['category1_total'] + $result['category3_total'];

// 創建新的 Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 填入表頭
$sheet->setCellValue('B2', '類別1');
$sheet->setCellValue('C2', '類別3');
$sheet->setCellValue('D2', '小計 (t-CO2e)');
$sheet->mergeCells('D2:E2');

// 設定排放項目
$data = [
    ['CO2排放量(t-CO2e)', $result['category1_total'], $result['category3_total'], $total_carbon],
    ['CH4排放量(t-CO2e)', 0, 0, 0],
    ['N2O排放量(t-CO2e)', 0, 0, 0],
    ['單一類別總量(t-CO2e)', $result['category1_total'], $result['category3_total'], $total_carbon],
    ['占比', 
        $total_carbon > 0 ? round(($result['category1_total'] / $total_carbon) * 100, 2) . '%' : '0%', 
        $total_carbon > 0 ? round(($result['category3_total'] / $total_carbon) * 100, 2) . '%' : '0%', 
        '100.00%'
    ]
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
?>
