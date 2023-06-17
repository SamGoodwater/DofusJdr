<?php
    if(!isset($error_msg)) {$error_msg = "Erreur inconnue";}else{if(!is_string($error_msg)) {$error_msg = "Erreur inconnue";}}
?>

<div>
    <h1>Erreur</h1>
    <p><?=$error_msg?></p>
    <p>Vous pouvez retourner à l'<a onclick="Page.show('home');">accueil</a>.</p>
</div>