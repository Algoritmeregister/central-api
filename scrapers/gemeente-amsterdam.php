<?php

require '../vendor/autoload.php';
$httpClient = new \GuzzleHttp\Client();
$url = 'https://algoritmeregister.amsterdam.nl/';
$filepath = __DIR__ . "/../cache/". md5($url) . ".json";
if (!file_exists($filepath)) {
    $response = $httpClient->get($url);
    $htmlString = (string) $response->getBody();
    libxml_use_internal_errors(true); //suppress any warnings
    $doc = new DOMDocument();
    $doc->loadHTML($htmlString);
    $xpath = new DOMXPath($doc);
    $cards = $xpath->query('//*[@class="saidot-system-card"]');
    $schema = json_decode(file_get_contents("https://standaard.algoritmeregister.org/schemas/registration-v0.2.nl.schema.json"), true);
    $algoritmeregister = [];
    foreach ($cards as $card) {
        $name = $xpath->query('.//h4', $card)[0]->textContent;
        $registratie = new SchemaBasedEntity($schema);
        $registratie->name = $name;
        $registratie->organization = "Gemeente Amsterdam";
        $registratie->department = $xpath->query('.//h5', $card)[0]->textContent;
        $registratie->area = "Gemeente Amsterdam";
        $registratie->domain = $xpath->query('.//h5', $card)[0]->textContent;
        $registratie->description_short = $xpath->query('.//p', $card)[1]->textContent;
        $registratie->website = $xpath->query('.//a', $card)[0]->getAttribute('href');
        $algoritmeregister[$name] = $registratie;
    }
    $contents = json_encode(["algoritmeregister" => array_values($algoritmeregister)]);
    file_put_contents($filepath, $contents);
}

header("Content-type: text/json");
readfile($filepath);