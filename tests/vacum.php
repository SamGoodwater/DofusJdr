<?php 


$pages = 1; // 12 pages

const WEAPON = [
    "search" => "weapon",
    "context" => "weapon",
];
const ITEM = [
    "search" => "equipment",
    "context" => "item",
];

$type = WEAPON;

$url = "https://www.dofusbook.net/items/dofus/search/".$type['context']."?context=".$type['context']."&sort=level-asc&page=";

for ($i=1; $i <= $pages; $i++) { 
    $final_url = $url.$i;
    $content = file_get_contents($final_url);
    $content = json_decode($content, true);
    $weapons = $content['data'];
    foreach ($weapons AS $weapon) {
        print_r($weapon);
        echo "<br><br>";
    }
}