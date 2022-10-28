<?php

require '../vendor/autoload.php';
$httpClient = new \GuzzleHttp\Client();
$response = $httpClient->get('https://algoritmeregister.amsterdam.nl/');
$htmlString = (string) $response->getBody();
//suppress any warnings
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($htmlString);
$xpath = new DOMXPath($doc);
$cards = $xpath->query('//*[@class="saidot-system-card"]');

$algoritmeregister = [];
foreach ($cards as $card) {
    $name = $xpath->query('.//h4', $card)[0]->textContent;
    $algoritmeregister[$name] = [
        "name" => $name,
        "organization" => "Gemeente Amsterdam",
        "department" => $xpath->query('.//h5', $card)[0]->textContent,
        "area" => "Gemeente Amsterdam",
        "domain"=> $xpath->query('.//h5', $card)[0]->textContent,
        "description_short" => $xpath->query('.//p', $card)[1]->textContent,
        "website" => $xpath->query('.//a', $card)[0]->getAttribute('href'),
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
