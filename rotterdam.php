<?php

require 'vendor/autoload.php';
$httpClient = new \GuzzleHttp\Client();
$response = $httpClient->get('https://www.rotterdam.nl/bestuur-organisatie/algoritmeregister/');
$htmlString = (string) $response->getBody();
//suppress any warnings
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($htmlString);
$xpath = new DOMXPath($doc);
$items = $xpath->query('//*[contains(@class, "answer-component")]');

$algoritmeregister = [];
foreach ($items as $item) {
    $name = $xpath->query('.//h4[contains(@class, "panel-title")]/span', $item)[0]->textContent;
    $algoritmeregister[$name] = [
        "name" => $name,
        "organization" => "Gemeente Rotterdam",
        "department" => "", //$xpath->query('.//h5', $item)[0]->textContent,
        "area" => "Gemeente Rotterdam",
        "domain"=> "", //$xpath->query('.//h5', $item)[0]->textContent,
        "description_short" => $xpath->query('.//*[contains(@class, "text-component")]//p', $item)[0]->textContent,
        "website" => "https://www.rotterdam.nl/bestuur-organisatie/algoritmeregister/", //$xpath->query('.//a', $item)[0]->getAttribute('href'),
        "risk_category" => "niet beschikbaar",
        "type" => "niet beschikbaar",
        "status" => "niet beschikbaar",
        "decision_making_process" => null
    ];
}
header("Content-type: text/json");
echo json_encode([
    "algoritmeregister" => array_values($algoritmeregister)
]);
