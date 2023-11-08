<?php
/* 
__________________________________________________________________________
-------------------------------- TEMPLATE CHATGPT ---------------------------------
    Nom : GetName 
    Version 1
    
    $template_vars['content'] : contenu de la section
    $template_vars['uniqid'] : identifiant de la section
*/
if(!isset($template_vars) || !is_array($template_vars)){$template_vars = array();}

// Laisser les valeurs vides (ou supprimer les variables) pour avoir les valeurs par défauts
$template = array(
    "title" => "GetName",
    "description" => "Ce template permet d'obtenir un nom et un prénom pour une classe donnée. Il est possible de fournir une culture inspirant le nom.",
    "temperature" => 0.7, // 0.7
    "max_tokens" => 70, // 150
    "system_content" => "",
    "model" => "" // gpt-3.5-turbo
);

// Non obligatoire
$template_vars_classe = "";
$template_vars_inspiration_culturel = "";
$template_vars_genre = "nb"; // m = masculin, f = féminin ou nb = non-binaire

if(isset($classe)){ 
    $template_vars_classe = $classe;
}
if(isset($inspiration_culturel)){ 
    $template_vars_inspiration_culturel = $inspiration_culturel;
}
$template_vars_pronom = "un·e";
$template_vars_genre_name = "";
if(isset($genre)){
    if(in_array(strtolower($genre), ['masculin', 'm', 'homme'])){
        $template_vars_genre = "m";
        $template_vars_pronom = "un";
        $template_vars_genre_name = "masculin";
    } elseif (in_array(strtolower($genre), ['féminin', 'f', 'femme'])) {
        $template_vars_genre = "f";
        $template_vars_pronom = "une";
        $template_vars_genre_name = "féminin";
    } elseif (in_array(strtolower($genre), ['non-binaire', 'nb', 'autre', 'non binaire'])) {
        $template_vars_genre = "nb";
        $template_vars_pronom = "un·e";
        $template_vars_genre_name = "non-binaire";
    } else {
        $template_vars_genre = "";
        $template_vars_pronom = "un·e";
        $template_vars_genre_name = "";
    
    }
}

ob_start(); ?>
    Tu es un créateur de personnage et tu vas m'aider à trouver un nom et un prénom pour mon personnage.
    C'est un personnage qui s'inscrit dans l'univers de Dofus et de Wakfu (jeux vidéos, mangas, séries, etc).
    Je veux que tu inventes le nom et le prénom. Ce nom et prénom ne doit pas déjà être utiliser dans cette univers.
    <?php if(!empty($template_vars_classe)){ ?>
        Ce personnage est <?= $template_vars_pronom ." ".$template_vars_classe; ?>.
    <?php } ?>
    <?php if(!empty($template_vars_genre)){ ?>
        Le genre de ce personnage est <?= $template_vars_genre_name; ?>.
    <?php } ?>
    Tu peux t'inspirer de la culture <?= $template_vars_inspiration_culturel; ?> pour t'aider. Cela ne doit être qu'une inspiration, pas une copie.
    Ta réponse doit comprendre que le Prénom suivi du nom et rien d'autre. Ne reformule pas ma demande et ne rajoute rien d'autre.
<?php $template["prompt"] = ob_get_clean();