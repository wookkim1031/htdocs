<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;

$mysqli = require __DIR__ . "/database.php";

function fetchData($mysqli, $type, $offset = null, $limit = null) {
    $query = "";
    if ($type === 'books') {
        $query = "SELECT title, author, year, publisher, status.status AS status_name, location.name AS location_name, location.room AS location_room FROM books 
                  JOIN status ON books.status = status.id 
                  JOIN location ON books.location = location.id";
    } elseif ($type === 'magazines') {
        $query = "SELECT title, jahrgang AS year, volumes, standort AS location_name, '' AS room FROM magazines";
    }

    if (!is_null($offset) && !is_null($limit)) {
        $query .= " LIMIT ?, ?";
    }

    $stmt = $mysqli->prepare($query);
    if (!is_null($offset) && !is_null($limit)) {
        $stmt->bind_param("ii", $offset, $limit);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Generate PDF
function generatePDF($data, $type) {
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $html = '<h1>' . ucfirst($type) . ' List</h1><table border="1"><thead><tr>';
    $html .= '<th>Title</th><th>Author/Year</th><th>Year/Volumes</th><th>Publisher/Location</th><th>Status</th><th>Location</th><th>Room</th>';
    $html .= '</tr></thead><tbody>';
    foreach ($data as $item) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($item['title']) . '</td>';
        $html .= '<td>' . htmlspecialchars($item['author'] ?? $item['year']) . '</td>';
        $html .= '<td>' . htmlspecialchars($item['year'] ?? $item['volumes']) . '</td>';
        $html .= '<td>' . htmlspecialchars($item['publisher'] ?? $item['location_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($item['status_name'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($item['location_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($item['room']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    $pdf->writeHTML($html);
    $pdf->Output(ucfirst($type) . '_list.pdf', 'D');
}

// Generate Excel
function generateExcel($data, $type) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle(ucfirst($type) . ' List');

    $sheet->setCellValue('A1', 'Title');
    $sheet->setCellValue('B1', 'Author/Year');
    $sheet->setCellValue('C1', 'Year/Volumes');
    $sheet->setCellValue('D1', 'Publisher/Location');
    $sheet->setCellValue('E1', 'Status');
    $sheet->setCellValue('F1', 'Location');
    $sheet->setCellValue('G1', 'Room');

    $row = 2;
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item['title']);
        $sheet->setCellValue('B' . $row, $item['author'] ?? $item['year']);
        $sheet->setCellValue('C' . $row, $item['year'] ?? $item['volumes']);
        $sheet->setCellValue('D' . $row, $item['publisher'] ?? $item['location_name']);
        $sheet->setCellValue('E' . $row, $item['status_name'] ?? '');
        $sheet->setCellValue('F' . $row, $item['location_name']);
        $sheet->setCellValue('G' . $row, $item['room']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = ucfirst($type) . '_list.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $writer->save('php://output');
}

$type = $_GET['type'] ?? 'books';
$format = $_GET['format'] ?? 'pdf';
$range = $_GET['range'] ?? 'all';
$offset = $_GET['offset'] ?? 0;
$limit = $_GET['limit'] ?? 10;

$data = fetchData($mysqli, $type, $range === 'current' ? $offset : null, $range === 'current' ? $limit : null);

if ($format === 'pdf') {
    generatePDF($data, $type);
} else {
    generateExcel($data, $type);
}
?>
