<?php

require '../vendor/autoload.php';
$httpClient = new \GuzzleHttp\Client();
$response = $httpClient->get('https://www.uwv.nl/overuwv/algoritmes-bij-uwv/');
$htmlString = (string) $response->getBody();
//suppress any warnings
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($htmlString);
$xpath = new DOMXPath($doc);
$items = $xpath->query('//*[contains(@class, "call-to-action")]');

$algoritmeregister = [];
foreach ($items as $item) {
    $name = $xpath->query('.//*[contains(@class, "call-to-action__title")]', $item)[0]->textContent;
    if (!$name) continue;
    $href = 'https://www.uwv.nl' . $xpath->query('.//*[contains(@class, "call-to-action__link-wrapper")]//a', $item)[0]->getAttribute('href');
    $algoritmeregister[$name] = [
        "Naam" => $name,
        "Organisatie" => "UWV",
        "Omschrijving" => $xpath->query('.//p', $item)[0]->textContent,
        "URL" => $href
    ];
}
header("Content-type: text/json");
echo json_encode([
    "algoritmeregister" => array_values($algoritmeregister)
]);
