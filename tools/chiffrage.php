<?php
$alphabet =   [
    "A" => "P",
    "B" => "T",
    "C" => "Y",
    "D" => "E",
    "E" => "J",
    "F" => "C",
    "G" => "F",
    "H" => "L",
    "I" => "Z",
    "J" => "U",
    "K" => "A",
    "L" => "O",
    "M" => "Q",
    "N" => "N",
    "O" => "S",
    "P" => "K",
    "Q" => "R",
    "R" => "X",
    "S" => "D",
    "T" => "M",
    "U" => "V",
    "V" => "B",
    "W" => "W",
    "X" => "H",
    "Y" => "G",
    "Z" => "I"
];

$words = [
    "maison de nologie",
    "maison du piou",
    "palais gouvernemental",
    "musee des armes",
    "bibliotheque",
    "college de bonta",
    "mairie de bonta",
    "stade",
    "maison des Kogue",
    "maison des Pirrutate",
    "concervatoire de musique",
    "maison des Popul",
    "hotel de vente des bijoutiers",
    "place du zaap",
    "banque de bonta",
    "arene de combat",
    "hotel du bouftou blanc",
    "concervatoire de danse",
    "parc de Bonta",
    "le rift",
    "palais royal",
    "maison des associations",
    "le grand restaurant",
    "universite",
    "villa coeur de vey",
    "opera de Bonta",
    "grand aquarium",
    "gymnase",
    "pont nologie",
    "hotel des metiers",
    "grande salle des spectacles",
    "cimetiere",
    "pont des douze",
    "archives de la ville",
    "ecole bonta i"
];

echo "Il y a 46 656 casiers différents<p>";
echo "Les casiers sont numérotés I ou II pour savoir de quel coté il se trouve, puis de A à Z désigne l'étagère et les deux chiffres désignent les coordonnées, en ordonnées puis en abscisse.<p><p>";
$indexes = array_keys($alphabet);

foreach ($words as $word) {
    $new_word = "";
    if(rand(0, 1) == 1){$new_word .= "I-";} else {$new_word .= "II-";}
    $new_word .= $alphabet[$indexes[rand(0, 22)]] . "-" . rand(1, 54) . ";" . rand(1, 18) . " : ";
    foreach (str_split(strtoupper($word)) as $letter) {
        if($letter == " "){
            $new_word .= " ";
        } else {
            $new_word .= $alphabet[$letter];
        }
    }
    echo $new_word . "<p>";
}