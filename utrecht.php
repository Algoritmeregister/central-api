<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// LOAD UTRECHT DATA :)

$url = "https://data.utrecht.nl/sites/default/files/open-data/algoritmeregister-gemeente-utrecht-definitieve-versie.xlsx";
$filename = __DIR__ . "/cache/". md5($url) . ".xlsx";

if (!file_exists($filename)) { // FIXME: refresh daily?
    $contents = file_get_contents($url);
    file_put_contents($filename, $contents);
    // FIXME: error handling
}

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($filename);

// LOAD INFORMATION
$sheetNames = $spreadsheet->getSheetNames();
$rs = [];
foreach ($sheetNames as $sheetName) {
    $rs[$sheetName] = [];
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    $firstRow = true;
    $headers = [];
    foreach ($worksheet->getRowIterator() as $row) {
        $rowValues = [];
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        foreach ($cellIterator as $cell) {
            $rowValues[] = trim($cell->getValue());
        }
        if ($firstRow) {
            $headers = $rowValues;
            $firstRow = false;
        } else {
            $rs[$sheetName][] = array_combine($headers, $rowValues);
        }
    }
}

header("Access-Control-Allow-Origin: *");
header("Content-type: text/json");
echo json_encode($rs);

//$writer = new Xlsx($spreadsheet);
//$writer->save('hello world.xlsx');