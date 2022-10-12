<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// LOAD DEN HAAG DATA :)

$url = "https://ckan.dataplatform.nl/dataset/f58f2b0e-8d93-480c-b6f7-da95ed7bbe18/resource/87f94065-ea24-40f0-887b-89cb1414e8a1/download/algoritmeregister-gemeente-den-haag.xlsx";
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
    $skipFirst = true;
    foreach ($worksheet->getRowIterator() as $row) {
        if ($skipFirst) {
            $skipFirst = false;
            continue;
        }
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



header("Content-type: text/json");
echo json_encode($rs);

//$writer = new Xlsx($spreadsheet);
//$writer->save('hello world.xlsx');