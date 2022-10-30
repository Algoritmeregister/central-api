<?php

require '../vendor/autoload.php';
include('../simplehtmldom_1_9_1/simple_html_dom.php');
$httpClient = new \GuzzleHttp\Client();
$response = $httpClient->get('https://www.rvig.nl/over-rvig/algoritmeregister');
$htmlString = (string) $response->getBody();
//suppress any warnings
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($htmlString);
$xpath = new DOMXPath($doc);
$items = $xpath->query('//*[contains(@class, "wayfinder__item")]');

$algoritmeregister = [];
foreach ($items as $item) {
    $name = $item->textContent;
    $href = 'https://www.rvig.nl' . $item->getAttribute('href');
    // WARNING reusing existing variables
    $html = file_get_html($href); // QUERY ALL PAGES INDIVIDUALLY
    $accItems = $html->find("div.accordion__item");
    $algoritmeregister[$name] = [
        "Naam" => $name,
        "URL" => $href
    ];
    foreach ($accItems as $accItem) {
        $title = $accItem->find(".accordion__item-title", 0)->innertext();
        $content = $accItem->find(".accordion__item-content p, ul", 0)->innertext();
        $algoritmeregister[$name][$title] = $content;
    }
}
header("Content-type: text/json");
echo json_encode([
    "algoritmeregister" => array_values($algoritmeregister)
]);
