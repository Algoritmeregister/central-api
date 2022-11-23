<?php

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function guidv4($data = null) {
    $data = $data ?? random_bytes(16);

    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

$url = "Algoritmeregisters inhoud ophalen.xlsx";
$filename = __DIR__ . "/../cache/". md5($url) . ".xlsx";

//if (!file_exists($filename)) { // caching disabled
    $contents = file_get_contents($url);
    file_put_contents($filename, $contents);
    // FIXME: error handling
//}

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($filename);

// LOAD INFORMATION
$sheetName = "Het Register";
$algoritmes = [];

$worksheet = $spreadsheet->getSheetByName($sheetName);
$rowCounter = 0;
$headers = [];
foreach ($worksheet->getRowIterator() as $row) {
    $rowValues = [];
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    foreach ($cellIterator as $cell) {
        $rowValues[] = trim($cell->getValue());
    }
    switch (++$rowCounter) {
        case 1:
            $properties = $rowValues;
            break;
        case 2:
            $required = $rowValues;
            break;
        case 3:
            $names = $rowValues;
            break;
        case 4:
            $descriptions = $rowValues;
            break;
        default:
            continue;
    }
}

$schema = json_decode(file_get_contents("https://standaard.algoritmeregister.org/schemas/registration-v0.2.nl.schema.json"), true);
foreach ($properties as $i => $property) {
    if (!$property) continue;
    $schema["properties"][$property]["name"] = $names[$i];
    $schema["properties"][$property]["description"] = $descriptions[$i];
    $schema["properties"][$property]["required"] = !!$required[$i];
}

header("Access-Control-Allow-Origin: *");
header("Content-type: text/json");
echo json_encode($schema, JSON_PRETTY_PRINT);
