<?php

trait NumberFct
{
    function extractBonusValue($inputString) {
        // Supprimer les espaces pour faciliter l'analyse
        $inputString = str_replace(' ', '', $inputString);
        
        // Définir les patterns pour les nombres (avec différentes formes possibles)
        $patterns = array(
            '/(\d*\.?\d+)([kKmMbBtT]|milliardaine|millionnaire|millier|centaine|million)?/u', // Matche les nombres avec leurs unités
            '/(\d+)/u', // Matche les nombres seuls
            '/zéro/u', // Matche le mot "zéro" (pour les cas particuliers)
        );
        
        // Parcourir les patterns et chercher des correspondances dans la chaîne
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $inputString, $matches)) {
                // $matches[0] contient la chaîne correspondante à l'expression régulière
                // Nous allons convertir le résultat en valeur numérique
                $value = $this->normalizeNumber($matches[0]);
                return $value;
            }
        }
        
        // Aucune correspondance trouvée, retourner une valeur par défaut (comme 0)
        return 0;
    }
    
    function normalizeNumber($numberString) {
        // Définir un tableau de correspondance pour les unités
        $units = array(
            'k' => 1000,
            'm' => 1000000,
            'b' => 1000000000,
            't' => 1000000000000,
            'milliardaine' => 1000000000000,
            'millionnaire' => 1000000000,
            'millier' => 1000,
            'centaine' => 100,
            'million' => 1000000,
            'milliard' => 1000000000,
        );
        
        // Vérifier si le nombre est "zéro" (cas particulier)
        if (strcasecmp($numberString, 'zéro') === 0) {
            return 0;
        }
        
        // Extraire la dernière lettre pour vérifier s'il y a une unité
        $lastChar = mb_substr($numberString, -1);
        
        // Vérifier si l'unité existe dans le tableau
        if (array_key_exists(strtolower($lastChar), $units)) {
            // Extraire la partie numérique du nombre (tout sauf la dernière lettre)
            $numberPart = substr($numberString, 0, -1);
            
            // Convertir la partie numérique en float
            $numericValue = floatval($numberPart);
            
            // Multiplier par l'unité appropriée
            return $numericValue * $units[strtolower($lastChar)];
        } else {
            // Si pas d'unité trouvée, convertir le nombre en float directement
            return floatval($numberString);
        }
    }
}
  