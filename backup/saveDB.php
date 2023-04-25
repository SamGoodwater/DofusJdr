<?php
// Paramètres de la requête cURL
$url = "https://jdr.iota21.fr/index.php?c=tools&a=savedb";
file_get_contents('https://jdr.iota21.fr/index.php?c=tools&a=generateAndSaveToken');
$file_contents = file_get_contents(dirname(__FILE__) . '/../medias/safeDir/token.txt');
$token = array_filter(explode("$", $file_contents))[0];
$token = explode("|", $token)[0];
echo "Token : " . substr($token, 0, 3) . "...";

$headers = array(
    "Authorization: Bearer $token",
    "Content-Type: application/json"
);

// Initialisation de la session cURL
$ch = curl_init();

// Configuration des options de la requête cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Utiliser false si vous avez un certificat SSL auto-signé

// Exécution de la requête cURL et récupération de la réponse
$response = curl_exec($ch);

// Vérification des erreurs
if (curl_errno($ch)) {
    echo "Erreur cURL : " . curl_error($ch);
} else {
    // Affichage de la réponse JSON
    echo $response;
}

// Fermeture de la session cURL
curl_close($ch);
?>
