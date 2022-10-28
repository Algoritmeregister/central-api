<?php

echo "<h1>Overzicht ad-hoc scrapers</h1>";

$algoritmeregisters = reset(json_decode(file_get_contents('https://www.algoritmeregister.nl/data/algoritmeregisters.json'), true));

echo "<h2>Geliste registers met url</h2>";

foreach ($algoritmeregisters as $algoritmeregister)
    if ($algoritmeregister["url"])
        echo "<a target='_blank' href='{$algoritmeregister["url"]}'>{$algoritmeregister["title"]}</a><br>";

echo "<h2>Beschikbare scrapers</h2>";

foreach (glob("scrapers/*") as $filepath)
    echo "<a target='_blank' href='{$filepath}'>{$filepath}</a><br>";