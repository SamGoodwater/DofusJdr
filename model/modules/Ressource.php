<?php

use SebastianBergmann\Type\VoidType;

class Ressource extends Module
{

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ CONST ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        const FILES = [
            "logo" => [
                "type" => FileManager::FORMAT_IMG,
                "default" => "medias/modules/ressources/default.svg",
                "dir" => "medias/modules/ressources/",
                "preferential_format" => "png",
                "naming" => "[uniqid]"
            ]
        ];

        const TYPES = [
            "Ressources diverses" => self::TYPE_RESSOURCES_DIV,
            "Céréale" => self::TYPE_CEREALE,
            "Plante" => self::TYPE_PLANTE,
            "Bois" => self::TYPE_BOIS,
            "Minerai" => self::TYPE_MINERAI,
            "Poudre" => self::TYPE_POUDRE,
            "Poil" => self::TYPE_POIL,
            "Graine" => self::TYPE_GRAINE,
            "Huile" => self::TYPE_HUILE,
            "Légume" => self::TYPE_LEGUME,
            "Liquide" => self::TYPE_LIQUIDE,
            "Fleur" => self::TYPE_FLEUR,
            "Os" => self::TYPE_OS,
            "Pierre brute" => self::TYPE_PIERRE_BRUTE,
            "Métaria" => self::TYPE_METARIA,
            "Matériel d'alchimie" => self::TYPE_MATERIEL_ALCHIMIE,
            "Aile" => self::TYPE_AILE,
            "Vêtement" => self::TYPE_VETEMENT,
            "Roleplay Buffs" => self::TYPE_ROLEPLAY_BUFFS,
            "Personnage suiveur" => self::TYPE_PERSONNAGE_SUIVEUR,
            "Poisson" => self::TYPE_POISSON,
            "Peluche" => self::TYPE_PELUCHE,
            "Pierre d'âme pleine" => self::TYPE_PIERRE_AME_PLEINE,
            "Obsolète" => self::TYPE_OBSOLETE,
            "Objet d'élevage" => self::TYPE_OBJET_ELEVAGE,
            "Sac de ressources" => self::TYPE_SAC_RESSOURCES,
            "Objet de Mutation" => self::TYPE_OBJET_MUTATION,
            "Peau" => self::TYPE_PEAU,
            "Clef" => self::TYPE_CLEF,
            "Prisme" => self::TYPE_PRISME,
            "Relique d'Incarnation" => self::TYPE_RELIQUE_INCARNATION,
            "Pierre d'âme" => self::TYPE_PIERRE_AME,
            "Gelée" => self::TYPE_GELEE,
            "Coffre" => self::TYPE_COFFRE,
            "Pierre précieuse" => self::TYPE_PIERRE_PRECIEUSE,
            "Emballage" => self::TYPE_EMBALLAGE,
            "Rabmablague" => self::TYPE_RABMABLAGUE,
            "Alliage" => self::TYPE_ALLIAGE,
            "Plume" => self::TYPE_PLUME,
            "Planche" => self::TYPE_PLANCHE,
            "Sève" => self::TYPE_SEVE,
            "Pierre magique" => self::TYPE_PIERRE_MAGIQUE,
            "Laine" => self::TYPE_LAINE,
            "Fruit" => self::TYPE_FRUIT,
            "Ressources des Songes" => self::TYPE_RESSOURCES_SONGES,
            "Nowel" => self::TYPE_NOWEL,
            "Conteneur" => self::TYPE_CONTENEUR,
            "Popoche de Havre-Sac" => self::TYPE_POPOCHE_HAVRE_SAC,
            "Haïku" => self::TYPE_HAIKU,
            "Bouataklône" => self::TYPE_BOUATAKLONE,
            "Ressources de Percepteur" => self::TYPE_RESSOURCES_PERCEPTEUR,
            "Globe de lumière" => self::TYPE_GLOBE_LUMIERE,
            "Viande" => self::TYPE_VIANDE,
            "Faux" => self::TYPE_FAUX,
            "Œil" => self::TYPE_OEIL,
            "Patte" => self::TYPE_PATTE,
            "Substrat" => self::TYPE_SUBSTRAT,
            "Gravure de forgemagie" => self::TYPE_GRAVURE_FORGEMAGIE,
            "Cuir" => self::TYPE_CUIR,
            "Œuf" => self::TYPE_OEUF,
            "Carte" => self::TYPE_CARTE,
            "Fragment de carte" => self::TYPE_FRAGMENT_CARTE,
            "Carapace" => self::TYPE_CARAPACE,
            "Ressource de combat" => self::TYPE_RESSOURCE_COMBAT,
            "Essence de gardien de donjon" => self::TYPE_ESSENCE_GARDIEN_DONJON,
            "Écorce" => self::TYPE_ECORCE,
            "Racine" => self::TYPE_RACINE,
            "Bourgeon" => self::TYPE_BOURGEON,
            "Étoffe" => self::TYPE_ETOFFE,
            "Galet" => self::TYPE_GALET,
            "Queue" => self::TYPE_QUEUE,
            "Oreille" => self::TYPE_OREILLE,
            "Filet de capture" => self::TYPE_FILET_CAPTURE,
            "Certificat de Dragodinde" => self::TYPE_CERTIFICAT_DRAGODINDE,
            "Orbe de forgemagie" => self::TYPE_ORBE_FORGEMAGIE,
            "Certificat de Muldo" => self::TYPE_CERTIFICAT_MULDO,
            "Caution" => self::TYPE_CAUTION,
            "Certificat de Volkorne" => self::TYPE_CERTIFICAT_VOLKORNE,
            "Champignon" => self::TYPE_CHAMPIGNON,
            "Coquille" => self::TYPE_COQUILLE,
            "Boîte de fragments" => self::TYPE_BOITE_FRAGMENTS,
            "Ressources des Anomalies Temporelles" => self::TYPE_RESSOURCES_ANOMALIES_TEMPORELLES
        ];

        const TYPE_RESSOURCES_DIV = 1;
        const TYPE_CEREALE = 2;
        const TYPE_PLANTE = 3;
        const TYPE_BOIS = 4;
        const TYPE_MINERAI = 5;
        const TYPE_POUDRE = 6;
        const TYPE_POIL = 7;
        const TYPE_GRAINE = 8;
        const TYPE_HUILE = 9;
        const TYPE_LEGUME = 10;
        const TYPE_LIQUIDE = 11;
        const TYPE_FLEUR = 12;
        const TYPE_OS = 13;
        const TYPE_PIERRE_BRUTE = 14;
        const TYPE_METARIA = 15;
        const TYPE_MATERIEL_ALCHIMIE = 16;
        const TYPE_AILE = 17;
        const TYPE_VETEMENT = 18;
        const TYPE_ROLEPLAY_BUFFS = 19;
        const TYPE_PERSONNAGE_SUIVEUR = 20;
        const TYPE_POISSON = 21;
        const TYPE_PELUCHE = 22;
        const TYPE_PIERRE_AME_PLEINE = 23;
        const TYPE_OBSOLETE = 24;
        const TYPE_OBJET_ELEVAGE = 25;
        const TYPE_SAC_RESSOURCES = 26;
        const TYPE_OBJET_MUTATION = 27;
        const TYPE_PEAU = 28;
        const TYPE_CLEF = 29;
        const TYPE_PRISME = 30;
        const TYPE_RELIQUE_INCARNATION = 31;
        const TYPE_PIERRE_AME = 32;
        const TYPE_GELEE = 33;
        const TYPE_COFFRE = 34;
        const TYPE_PIERRE_PRECIEUSE = 35;
        const TYPE_EMBALLAGE = 36;
        const TYPE_RABMABLAGUE = 37;
        const TYPE_ALLIAGE = 38;
        const TYPE_PLUME = 39;
        const TYPE_PLANCHE = 40;
        const TYPE_SEVE = 41;
        const TYPE_PIERRE_MAGIQUE = 42;
        const TYPE_LAINE = 43;
        const TYPE_FRUIT = 44;
        const TYPE_RESSOURCES_SONGES = 45;
        const TYPE_NOWEL = 46;
        const TYPE_CONTENEUR = 47;
        const TYPE_POPOCHE_HAVRE_SAC = 48;
        const TYPE_HAIKU = 49;
        const TYPE_BOUATAKLONE = 50;
        const TYPE_RESSOURCES_PERCEPTEUR = 51;
        const TYPE_GLOBE_LUMIERE = 52;
        const TYPE_VIANDE = 53;
        const TYPE_FAUX = 54;
        const TYPE_OEIL = 55;
        const TYPE_PATTE = 56;
        const TYPE_SUBSTRAT = 57;
        const TYPE_GRAVURE_FORGEMAGIE = 58;
        const TYPE_CUIR = 59;
        const TYPE_OEUF = 60;
        const TYPE_CARTE = 61;
        const TYPE_FRAGMENT_CARTE = 62;
        const TYPE_CARAPACE = 63;
        const TYPE_RESSOURCE_COMBAT = 64;
        const TYPE_ESSENCE_GARDIEN_DONJON = 65;
        const TYPE_ECORCE = 66;
        const TYPE_RACINE = 67;
        const TYPE_BOURGEON = 68;
        const TYPE_ETOFFE = 69;
        const TYPE_GALET = 70;
        const TYPE_QUEUE = 71;
        const TYPE_OREILLE = 72;
        const TYPE_FILET_CAPTURE = 73;
        const TYPE_CERTIFICAT_DRAGODINDE = 74;
        const TYPE_ORBE_FORGEMAGIE = 75;
        const TYPE_CERTIFICAT_MULDO = 76;
        const TYPE_CAUTION = 77;
        const TYPE_CERTIFICAT_VOLKORNE = 78;
        const TYPE_CHAMPIGNON = 79;
        const TYPE_COQUILLE = 80;
        const TYPE_BOITE_FRAGMENTS = 81;
        const TYPE_RESSOURCES_ANOMALIES_TEMPORELLES = 82;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_description='';
        private $_level=1;
        private $_type = "";
        private $_price = 0;
        private $_weight = 0;
        private $_rarity=5;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "ressource",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom de la ressource",
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "ressource",
                            "id" => "description".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description",
                            "label" => "Description",
                            "value" => $this->_description
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_description)){return "";}
                    return html_entity_decode($this->_description);
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Ressource",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "level",
                            "label" => "Niveau",
                            "placeholder" => "Niveau de la ressource",
                            "tooltip" => "Niveau de la ressource",
                            "value" => $this->_level,
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Niveau {$this->_level}",
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3",
                            "tooltip" => "Niveau de la ressource",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);
                
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_level,
                            "color" => "",
                            "tooltip" => "Niveau de la ressource",
                            "style" => Style::STYLE_NONE,
                            "class" => "text-".Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                default:
                    return $this->_level;
            }
        }
        public function getType(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::TYPES as $name => $type_id) {
                        $items[] = [
                            "display" => "<span class='badge back-grey-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => "Ressource.update('".$this->getUniqid()."', '".$type_id."', 'type',".Controller::IS_VALUE.")"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getType(Content::FORMAT_BADGE),
                            "tooltip" => "Catégorie de la ressource",
                            "items" => $items,
                            "id" => "type_{$this->getUniqid()}",
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    if(in_array($this->_type, self::TYPES)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_type, self::TYPES)),
                                "color" => "grey-d-2",
                                "tooltip" => "Catégorie de la ressource",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_type;
            }
        }
        public function getPrice(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Ressource",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "price",
                            "label" => "Prix estimé",
                            "placeholder" => "Prix estimé de la ressource",
                            "tooltip" => "Prix estimé",
                            "value" => $this->_price,
                            "color" => "kamas-d-3",
                            "style" => Style::INPUT_ICON,
                            'icon' => "kamas.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_price . " kamas",
                            "color" => "kamas-d-4",
                            "tooltip" => "Prix estimé de la ressource : {$this->_price} kamas",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "kamas.png",
                            "color" => "kamas-d-3",
                            "tooltip" => "Prix estimé de la ressource : {$this->_price} kamas",
                            "content" => $this->_price,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false); 
                
                default:
                    return $this->_price;
            }
        }
        public function getWeight(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Ressource",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "weight",
                            "label" => "Poids",
                            "placeholder" => "Poids de la ressource",
                            "tooltip" => "Poids",
                            "value" => $this->_weight,
                            "color" => "lime-d-3",
                            "style" => Style::INPUT_ICON,
                            'icon' => "weight-hanging",
                            "style_icon" => Style::ICON_SOLID
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_weight . " POD",
                            "color" => "lime-d-3",
                            "tooltip" => "Poids de la ressource : {$this->_weight} pod",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_SOLID,
                            "icon" => "weight-hanging",
                            "color" => "lime-d-3",
                            "tooltip" => "Poids de la ressource : {$this->_weight} pod",
                            "content" => $this->_weight,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false); 
                
                default:
                    return $this->_weight;
            }
        }
        public function getRarity(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Item::RARITY_LIST as $name => $rarity) {
                        $items[] = [
                            "display" => "<span class='badge back-".Style::getColorFromLetter($rarity, true)."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => "Ressource.update('{$this->getUniqid()}', {$rarity}, 'rarity', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getRarity(Content::FORMAT_BADGE),
                            "tooltip" => "Rareté de la ressource",
                            "items" => $items,
                            "id" => "rarity_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_rarity, Item::RARITY_LIST)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_rarity, Item::RARITY_LIST)),
                                "color" => Style::getColorFromLetter($this->_rarity, true) . "-d-2",
                                "tooltip" => "Rareté de la ressource",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_rarity;
            }
        }
        
    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName(string | int | null $data){
            $this->_name = $data;
            return true;
        }
        public function setDescription(string | null $data){
            $this->_description = $data;
            return true;
        }
        public function setLevel(int | null $data){
            if(is_numeric($data)){
                $this->_level = $data;
                return true;
            } else {
                throw new Exception("La valeur doit être un nombre");
            }
        }
        public function setType(int | null $data){
            if(in_array($data, self::TYPES) || empty($data)){
                $this->_type = $data;
                return true;
            } else {
                throw new Exception("Valeur incorrect");
            }
        }
        public function setPrice(string | int | null $data){
            $this->_price = $data;
            return true;
        }
        public function setWeight(string | int | null $data){
            $this->_weight = $data;
            return true;
        }
        public function setRarity($data){
            if(in_array($data, Item::RARITY_LIST)){
                $this->_rarity = $data;
                return true;
            } else {
                $this->_rarity = Item::RARITY_LIST["Très répandu"];
                throw new Exception("Rareté incorrecte");
            }
        }

}