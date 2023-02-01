<!-- {class} : les classes CSS à ajouter à la div contenant l'input
{id} : l'identifiant unique de l'input
{label} : le texte à afficher comme étiquette pour l'input
{type} : le type de l'input (text, password, etc.)
{name} : le nom de l'input
{value} : la valeur préremplie de l'input
{placeholder} : le texte à afficher comme placeholder dans l'input
{required} : l'attribut "required" à ajouter si l'input est obligatoire
{extra} : tout autre attribut ou classe à ajouter à l'input
Il est important de noter que les variables doivent être sécurisées pour éviter les risques d'injection de code lors de la génération de l'output. -->

<?php
    $template_path_json = "/model/".strtolower(get_class($this->getModel())).".json";
    $template_properties = [];
    if(file_exists($template_path_json)){
        $template_properties = json_decode(file_get_contents($template_path_json), true);
    }

    $template_property = [];
    if(isset($template_properties[$this->getProperty()])){
        $template_property = $template_properties[$this->getProperty()]; 
    }

    switch ($template_property["type"]) {
        case 'text':
            # code...
        break;
        
        default:
            # code...
        break;
    }