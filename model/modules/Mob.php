<?php
class Mob extends Content
{
    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/mobs/default.svg",
            "dir" => "medias/modules/mobs/",
            "preferential_format" => "svg",
            "naming" => "[uniqid]"
        ]
    ];

    const HOSTILITY = [
        "amicale" => 0,
        "currieux" => 1,
        "neutre" => 2,
        "perreux" => 3,
        "agressif" => 4,
        "hostile" => 5
    ];

    const SIZE = [
        "très petite" => 0,
        "petite" => 1,
        "moyenne" => 2,
        "grande" => 3,
        "très grande" => 4,
        "gigantesque" => 5
    ];


    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_description='';
        private $_level="";
        private $_vitality="";
        private $_pa="";
        private $_pm="";
        private $_po="";
        private $_ini="";
        private $_touch="";
        private $_life="";
        private $_sagesse="";
        private $_strong="";
        private $_intel="";
        private $_agi="";
        private $_chance="";
        private $_ca="";
        private $_fuite="";
        private $_tacle="";
        private $_dodge_pa="";
        private $_dodge_pm="";
        private $_res_neutre="";
        private $_res_terre="";
        private $_res_feu="";
        private $_res_air="";
        private $_res_eau="";
        private $_zone="";
        private $_hostility="";
        private $_trait="";
        private $_size=self::SIZE['moyenne'];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom de la créature",
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description",
                            "label" => "Description",
                            "maxlenght" => "2000",
                            "placeholder" => "",
                            "value" => $this->_description
                        ], 
                        write: false);

                default:
                    return $this->_description;
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "level",
                            "label" => "Niveau",
                            "placeholder" => "Niveau de la créature",
                            "tooltip" => "Niveau de la créature",
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
                            "tooltip" => "Niveau de la créature",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);

                                    
                case Content::FORMAT_LIST:
                    if(preg_match("/\[.*\]/", $this->_level)){
                        ob_start(); ?>
                            <div class="dropdown">
                                <a class="btn btn-text-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><?=$this->getLevel(Content::FORMAT_BADGE)?></a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" onclick="Mob.updateDisplayCaracteristics('<?=$this->getUniqid()?>', 0)"><span class='badge back-<?=Style::getColorFromLetter($this->_level, true)?>-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Niveau des créatures">Niveau <?=$this->_level?></span></a></li>
                                    <?php $level = Content::getMinMaxFromFormule($this->_level);
                                    if($level['same']){ ?>
                                        <li><a class="dropdown-item" onclick="Mob.updateDisplayCaracteristics('<?=$this->getUniqid()?>', '<?=$level['max']?>')"><span class='badge back-<?=Style::getColorFromLetter($level['max'], true)?>-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Générer une créature de niveau <?=$level["max"]?>">Niveau <?=$level["max"]?></span></a></li>
                                    <?php } else {
                                        if($level['min'] == 0){$level['min'] = 1;}
                                        if($level['max'] == 0){$level['max'] = 1;}
                                        for($i=$level['min']; $i<=$level['max']; $i++){ ?>
                                            <li><a class="dropdown-item" onclick="Mob.updateDisplayCaracteristics('<?=$this->getUniqid()?>', '<?=$i?>')"><span class='badge back-<?=Style::getColorFromLetter($i, true)?>-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Générer une créature de niveau <?=$i?>">Niveau <?=$i?></span></a></li>
                                        <?php }
                                    } ?>
                                </ul>
                            </div>
                        <?php return ob_get_clean();
                    } else {
                        return $this->getLevel(Content::FORMAT_BADGE);
                    }
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_level,
                            "color" => "",
                            "tooltip" => "Niveau de la créature",
                            "style" => Style::STYLE_NONE,
                            "class" => "text-".Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                default:
                    return $this->_level;
            }
        }
        public function getLife(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "life",
                            "label" => "Points de vie",
                            "placeholder" => "Points de vie de la créature",
                            "tooltip" => "Calcul des points de vie de la créature",
                            "value" => $this->_life,
                            "color" => "life-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "heart",
                            "style_icon" => Style::ICON_SOLID
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_life."' data-text=' Points de vie'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_life, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_life} Points de vie",
                            "color" => "life-d-2",
                            "tooltip" => "Calcul des points de vie de la créature",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "life"
                        ], 
                        write: false);
                   
                    case Content::FORMAT_ICON:
                        return $view->dispatch(
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_MEDIA,
                                "icon" => "life.png",
                                "color" => "life-d-2",
                                "tooltip" => "Calcul des points de vie de la créature",
                                "content" => $this->_life,
                                "content_placement" => Style::POSITION_LEFT
                            ], 
                            write: false); 
                
                default:
                    return $this->_life;
            }
        }
        public function getPa(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "pa",
                            "label" => "Points d'action",
                            "placeholder" => "Points d'action",
                            "tooltip" => "Calcul des points d'action",
                            "value" => $this->_pa,
                            "color" => "pa-d-2",
                            "style" => Style::INPUT_ICON,
                            "icon" => "pa.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "6 + bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_pa."' data-text=' PA'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_pa, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_pa} PA",
                            "color" => "pa-d-2",
                            "tooltip" => "Calcul des points d'action",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "pa.png",
                            "color" => "pa-d-2",
                            "size" => 50,
                            "tooltip" => "Calcul des points d'action",
                            "content" => $this->_pa,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);
                
                default:
                    return $this->_pa;
            }
        }
        public function getPm(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "pm",
                            "label" => "Points de mouvement",
                            "placeholder" => "Points de mouvement",
                            "tooltip" => "Calcul des points de mouvement",
                            "value" => $this->_pm,
                            "color" => "pm-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "pm.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "3 + bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_pm."' data-text=' PM'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_pm, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_pm} PM",
                            "color" => "pm-d-2",
                            "tooltip" => "Calcul des points de mouvement",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);
    
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "pm.png",
                            "color" => "pm-d-2",
                            "tooltip" => "Calcul des points de mouvement",
                            "content" => $this->_pm,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_pm;
            }
        }
        public function getPo(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "po",
                            "label" => "Bonus de portée",
                            "placeholder" => "Bonus de portée",
                            "tooltip" => "Bonus de portée",
                            "value" => $this->_po,
                            "color" => "po-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "po.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "color_icon" => "po-d-4",
                            "comment" => "1 + bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_po."' data-text=' PO'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_po, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_po} PO",
                            "color" => "po-d-2",
                            "tooltip" => "Bonus de portée",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "po.png",
                            "color" => "po-d-2",
                            "tooltip" => "Bonus de portée",
                            "content" => $this->_po,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_po;
            }
        }
        public function getIni(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "ini",
                            "label" => "Bonus d'initiative",
                            "placeholder" => "Bonus d'initiative",
                            "tooltip" => "Bonus d'initiative",
                            "value" => $this->_ini,
                            "color" => "ini-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "ini.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "1d20 + mod. Intel + Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_ini."' data-text=' Ini'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_ini, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_ini} Initiative",
                            "color" => "ini-d-2",
                            "tooltip" => "Bonus d'initiative",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "ini.png",
                            "color" => "ini-d-2",
                            "tooltip" => "Bonus d'initiative",
                            "content" => $this->_ini,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_ini;
            }
        }
        public function getTouch(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "touch",
                            "label" => "Bonus de touche",
                            "placeholder" => "Bonus de touche",
                            "tooltip" => "Bonus de touche",
                            "value" => $this->_touch,
                            "color" => "touch-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "touch.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_touch."' data-text=' Touche'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_touch, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_touch} Bonus de Touche",
                            "color" => "touch-d-2",
                            "tooltip" => "Bonus de touche",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "touch.png",
                            "color" => "touch-d-2",
                            "tooltip" => "Bonus de touche",
                            "content" => $this->_touch,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_touch;
            }
        }
        public function getVitality(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "vitality",
                            "label" => "Vitalité",
                            "placeholder" => "Vitalité",
                            "tooltip" => "Vitalité",
                            "value" => $this->_vitality,
                            "color" => "touch-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "vitality.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "Bonus"
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_vitality."' data-text=' Vitalité'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_vitality, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_vitality} Vitalité",
                            "color" => "vitality-d-2",
                            "tooltip" => "Vitalité",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "vitality.png",
                            "color" => "vitality-d-2",
                            "tooltip" => "Vitalité",
                            "content" => $this->_vitality,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_vitality;
            }
        }
        public function getSagesse(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "sagesse",
                            "label" => "Sagesse",
                            "placeholder" => "Sagesse",
                            "tooltip" => "Sagesse",
                            "value" => $this->_sagesse,
                            "color" => "sagesse-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "sagesse.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_sagesse."' data-text=' Sagesse'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_sagesse, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_sagesse} Sagesse",
                            "color" => "sagesse-d-2",
                            "tooltip" => "Sagesse",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "sagesse.png",
                            "color" => "sagesse-d-2",
                            "tooltip" => "Sagesse",
                            "content" => $this->_sagesse,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_sagesse;
            }
        }
        public function getStrong(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "strong",
                            "label" => "Force",
                            "placeholder" => "Force",
                            "tooltip" => "Force",
                            "value" => $this->_strong,
                            "color" => "strong-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "force.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "Bonus"
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_strong."' data-text=' Force'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_strong, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_strong} Force",
                            "color" => "strong-d-2",
                            "tooltip" => "Force",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "force.png",
                            "color" => "strong-d-2",
                            "tooltip" => "Force",
                            "content" => $this->_strong,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_strong;
            }
        }
        public function getIntel(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "intel",
                            "label" => "Intelligence",
                            "placeholder" => "Intelligence",
                            "tooltip" => "Intelligence",
                            "value" => $this->_intel,
                            "color" => "intel-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "intel.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_intel."' data-text=' Intel'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_intel, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_intel} Intel",
                            "color" => "intel-d-2",
                            "tooltip" => "Intelligence",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "intel.png",
                            "color" => "intel-d-2",
                            "tooltip" => "Intelligence",
                            "content" => $this->_intel,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);
                default:
                    return $this->_intel;
            }
        }
        public function getAgi(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "agi",
                            "label" => "Agilité",
                            "placeholder" => "Agilité",
                            "tooltip" => "Agilité",
                            "value" => $this->_agi,
                            "color" => "agi-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "agi.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_agi."' data-text=' Agi'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_agi, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_agi} Agi",
                            "color" => "agi-d-2",
                            "tooltip" => "Agilité",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "agi.png",
                            "color" => "agi-d-2",
                            "tooltip" => "Agilité",
                            "content" => $this->_agi,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_agi;
            }
        }
        public function getChance(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "chance",
                            "label" => "Chance",
                            "placeholder" => "Chance",
                            "tooltip" => "Chance",
                            "value" => $this->_chance,
                            "color" => "chance-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "chance.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_chance."' data-text=' Chance'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_chance, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_chance} Chance",
                            "color" => "chance-d-2",
                            "tooltip" => "Chance",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);
 
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "chance.png",
                            "color" => "chance-d-2",
                            "tooltip" => "Chance",
                            "content" => $this->_chance,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_chance;
            }
        }
        public function getCa(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "ca",
                            "label" => "Bonus de classe d'armure",
                            "placeholder" => "Bonus de classe d'armure",
                            "tooltip" => "Bonus de classe d'armure",
                            "value" => $this->_ca,
                            "color" => "ca-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "ca.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "1d20 + mod. Vitalité + Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_ca."' data-text=' CA'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_ca, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_ca} CA",
                            "color" => "ca-d-2",
                            "tooltip" => "Bonus de classe d'armure",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                    return "<span id='ca' ".$data." data-formule='".$this->_ca."' class='badge back-ca-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de la classe d'armure de la créature\">{$this->_ca} CA</span>";
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "ca.png",
                            "color" => "ca-d-2",
                            "tooltip" => "Bonus de classe d'armure",
                            "content" => $this->_ca,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_ca;
            }
        }
        public function getFuite(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "fuite",
                            "label" => "Bonus de fuite",
                            "placeholder" => "Bonus de fuite",
                            "tooltip" => "Bonus de fuite",
                            "value" => $this->_fuite,
                            "color" => "fuite-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "fuite.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "1d20 + mod. Agilité + Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_fuite."' data-text=' Fuite'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_fuite, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_fuite} Fuite",
                            "color" => "fuite-d-2",
                            "tooltip" => "Bonus de fuite",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "fuite.png",
                            "color" => "fuite-d-2",
                            "tooltip" => "Bonus de fuite",
                            "content" => $this->_fuite,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_fuite;
            }
        }
        public function getTacle(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "tacle",
                            "label" => "Bonus de tacle",
                            "placeholder" => "Bonus de tacle",
                            "tooltip" => "Bonus de tacle",
                            "value" => $this->_tacle,
                            "color" => "tacle-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "tacle.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "1d20 + mod. Chance + Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_tacle."' data-text=' Tacle'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_tacle, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_tacle} Tacle",
                            "color" => "tacle-d-2",
                            "tooltip" => "Bonus de tacle",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "tacle.png",
                            "color" => "tacle-d-2",
                            "tooltip" => "Bonus de tacle",
                            "content" => $this->_tacle,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_tacle;
            }
        }
        public function getDodge_pa(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "dodge_pa",
                            "label" => "Bonus d'Esquive PA",
                            "placeholder" => "Bonus d'Esquive PA",
                            "tooltip" => "Bonus d'Esquive PA",
                            "value" => $this->_dodge_pa,
                            "color" => "pa-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "dodge_pa.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "1d20 + mod. Sagesse + Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_dodge_pa."' data-text=' Esquive PA'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_dodge_pa, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_dodge_pa} Esquive PA",
                            "color" => "pa-d-2",
                            "tooltip" => "Bonus d'Esquive PA",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "dodge_pa.png",
                            "color" => "pa-d-2",
                            "tooltip" => "Bonus d'Esquive PA",
                            "content" => $this->_dodge_pa,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_dodge_pa;
            }
        }
        public function getDodge_pm(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "dodge_pm",
                            "label" => "Bonus d'Esquive PM",
                            "placeholder" => "Bonus d'Esquive PM",
                            "tooltip" => "Bonus d'Esquive PM",
                            "value" => $this->_dodge_pm,
                            "color" => "pm-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "dodge_pm.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "1d20 + mod. Sagesse + Bonus"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_dodge_pm."' data-text=' Esquive PM'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_dodge_pm, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_dodge_pm} Esquive PM",
                            "color" => "pm-d-2",
                            "tooltip" => "Bonus d'Esquive PM",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "dodge_pm.png",
                            "color" => "pm-d-2",
                            "tooltip" => "Bonus d'Esquive PM",
                            "content" => $this->_dodge_pm,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_dodge_pm;
            }
        }
        public function getRes_neutre(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_neutre",
                            "label" => "Résistance neutre",
                            "placeholder" => "Résistance neutre",
                            "tooltip" => "Résistance neutre",
                            "value" => $this->_res_neutre,
                            "color" => "black",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_neutre.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_res_neutre."' data-text=' Résistance Neutre'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_neutre, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_neutre} Résistance Neutre",
                            "color" => "grey-d-4",
                            "tooltip" => "Résistance neutre",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_neutre.png",
                            "color" => "grey-d-4",
                            "tooltip" => "Résistance neutre",
                            "content" => $this->_res_neutre,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_res_neutre;
            }
        }
        public function getRes_terre(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_terre",
                            "label" => "Résistance terre",
                            "placeholder" => "Résistance terre",
                            "tooltip" => "Résistance terre",
                            "value" => $this->_res_terre,
                            "color" => "force-d-4",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_terre.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_res_terre."' data-text=' Résistance Terre'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_terre, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_terre} Résistance Terre",
                            "color" => "force-d-4",
                            "tooltip" => "Résistance terre",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_terre.png",
                            "color" => "force-d-4",
                            "tooltip" => "Résistance terre",
                            "content" => $this->_res_terre,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_res_terre;
            }
        }
        public function getRes_feu(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_feu",
                            "label" => "Résistance feu",
                            "placeholder" => "Résistance feu",
                            "tooltip" => "Résistance feu",
                            "value" => $this->_res_feu,
                            "color" => "intel-d-4",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_feu.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_res_feu."' data-text=' Résistance Feu'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_feu, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_feu} Résistance Feu",
                            "color" => "intel-d-4",
                            "tooltip" => "Résistance feu",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_feu.png",
                            "color" => "intel-d-4",
                            "tooltip" => "Résistance feu",
                            "content" => $this->_res_feu,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_res_feu;
            }
        }
        public function getRes_air(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_air",
                            "label" => "Résistance air",
                            "placeholder" => "Résistance air",
                            "tooltip" => "Résistance air",
                            "value" => $this->_res_air,
                            "color" => "agi-d-4",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_air.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_res_air."' data-text=' Résistance Air'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_air, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_air} Résistance Air",
                            "color" => "agi-d-4",
                            "tooltip" => "Résistance air",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_air.png",
                            "color" => "agi-d-4",
                            "tooltip" => "Résistance air",
                            "content" => $this->_res_air,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_res_air;
            }
        }
        public function getRes_eau(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_eau",
                            "label" => "Résistance eau",
                            "placeholder" => "Résistance eau",
                            "tooltip" => "Résistance eau",
                            "value" => $this->_res_eau,
                            "color" => "chance-d-4",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_eau.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$this->_res_eau."' data-text=' Résistance Eau'";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_eau, $i)."' ";
                        }
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_eau} Résistance Eau",
                            "color" => "chance-d-4",
                            "tooltip" => "Résistance eau",
                            "style" => Style::STYLE_OUTLINE,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_eau.png",
                            "color" => "chance-d-4",
                            "tooltip" => "Résistance eau",
                            "content" => $this->_res_eau,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_res_eau;
            }
        }
        public function getZone(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "zone",
                            "label" => "Zone de vie",
                            "placeholder" => "Zone de vie",
                            "tooltip" => "Zone de vie",
                            "value" => $this->_zone,
                            "color" => "main-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "map-marker-alt",
                            "style_icon" => Style::ICON_SOLID
                        ], 
                        write: false);
                
                default:
                    return $this->_zone;
            }

        }
        public function getHostility(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Mob::HOSTILITY as $name => $value) {
                        $items[] = [
                            "onclick" => "Mob.update('".$this->getUniqid()."', ".$value.", 'hostility', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge-outline text-".Style::getColorFromLetter($value, true)."-d-4 border-".Style::getColorFromLetter($value, true)."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Hostilité de la créature",
                            "label" => $this->getHostility(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items,
                            "comment" => "<span class='mx-2'>Hostilité : " . implode(", ", array_keys(Mob::HOSTILITY))."</span>",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_hostility, Mob::HOSTILITY)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => array_search($this->_hostility, Mob::HOSTILITY),
                                "color" => Style::getColorFromLetter($this->_hostility, true)."-d-2",
                                "tooltip" => "Agressivité de la créature : " . implode(", ", array_keys(Mob::HOSTILITY)),
                                "style" => Style::STYLE_OUTLINE
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($this->_hostility, Mob::HOSTILITY)){
                        return array_search($this->_hostility, Mob::HOSTILITY);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_hostility;
            }
        }

        public function getTrait(int $format = Content::FORMAT_BRUT){
            $view = new View();
            if(!empty($this->_trait)){
                $traits = explode(",", $this->_trait);
                if(!is_array($traits) || count($traits) == 0){
                    $traits = array();
                }
            } else {
                $traits = array();
            }

            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Mob",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "trait",
                            "label" => "Traits",
                            "value" => $this->_trait,
                            "placeholder" => "Traits",
                            "style" => Style::INPUT_FLOATING,
                            "comment" => "Séparer les différents traits par des virgules."
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-around"> <?php
                            foreach ($traits as $trait) { 
                                $view->dispatch(
                                    template_name : "badge",
                                    data : [
                                        "color" => Style::getColorFromLetter($trait) . "-d-1",
                                        "content" => $trait,
                                        "style" => Style::STYLE_BACK,
                                        "tooltip" => "Trait ".$trait,
                                        "tooltip_placement" => "top"
                                    ], 
                                    write: true);
                                ?>
                                <?php } ?>                            
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_trait;
            }

        }
        public function getSize(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Mob::SIZE as $name => $value) {
                        $items[] = [
                            "onclick" => "Mob.update('".$this->getUniqid()."', ".$value.", 'size', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".Style::getColorFromLetter($value, true)."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Taille de la créature",
                            "label" => ucfirst($this->getSize(Content::FORMAT_BADGE)),
                            "size" => Style::SIZE_SM,
                            "items" => $items,
                            "comment" => "<span class='mx-2'>Taille : " . implode(", ", array_keys(Mob::SIZE))."</span>",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_size, Mob::SIZE)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_size, Mob::SIZE)),
                                "color" => Style::getColorFromLetter($this->_size, true)."-d-2",
                                "tooltip" => "Taille de la créature : " . array_search($this->_size, Mob::SIZE),
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }
                case Content::FORMAT_ICON:
                    if(in_array($this->_size, Mob::SIZE)){
                        $expr = '/(?<=\s|^)[a-z]/i'; preg_match_all($expr, array_search($this->_size, Mob::SIZE), $matches);
                        $short_name = strtoupper(implode('', $matches[0]));
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => $short_name,
                                "color" => Style::getColorFromLetter($this->_size, true)."-d-2",
                                "tooltip" => "Taille de la créature : " . array_search($this->_size, Mob::SIZE),
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($this->_size, Mob::SIZE)){
                        return array_search($this->_size, Mob::SIZE);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_size;
            }
        }

        public function getSpell(int $format = Content::FORMAT_BRUT, $display_remove = false, $size = 300){
            $manager = new MobManager();
            $spells = $manager->getLinkSpell($this);
            
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View();
                    $html = $view->dispatch(
                        template_name : "input/search",
                        data : [
                            "id" => "addSpell" . $this->getUniqid(),
                            "title" => "Ajouter un sort",
                            "label" => "Rechercher un sort",
                            "placeholder" => "Rechercher un sort",
                            "search_in" => ControllerModule::SEARCH_IN_SPELL,
                            "parameter" => $this->getUniqid(),
                            "action" => ControllerModule::SEARCH_DONE_ADD_SPELL_TO_MOB,
                        ], 
                        write: false);

                    return $html . $this->getSpell(Content::DISPLAY_RESUME, true);

                case Content::DISPLAY_RESUME:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($spells)){
                        $new_spells = [];
                        foreach ($spells as $spell) {
                            $new_spells[] = $spell;
                        }
                        $spells = $new_spells;
                        return $view->dispatch(
                            template_name : "spell/list",
                            data : [
                                "spells" => $spells,
                                "is_removable" => $display_remove,
                                "uniqid" => $this->getUniqid(),
                                "class_name" => "Mob",
                                "size" => $size
                            ], 
                            write: false);
                    }
                    return "";

                case Content::DISPLAY_LIST:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($spells)){
                        ob_start();
                            ?> <ul class="list-unstyled"> <?php
                                foreach ($spells as $spell) {?>
                                    <li>
                                        <?php $view->dispatch(
                                            template_name : "spell/text",
                                            data : [
                                                "obj" => $spell,
                                                "is_link" => true
                                            ], 
                                            write: true); ?>
                                    </li> <?php
                                }
                            ?> </ul> <?php
                        return ob_get_clean();
                    }
                    return "";

                case Content::FORMAT_ARRAY:
                    if(!empty($spells)){
                        return $spells;
                    }
                    return [];
                default:
                    return $spells;
            }
        }

        public function getCapability(int $format = Content::FORMAT_BRUT, bool $display_remove = false, $size = 300){
            $manager = new MobManager();
            $capabilities = $manager->getLinkCapability($this);
            
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View();
                    $html = $view->dispatch(
                        template_name : "input/search",
                        data : [
                            "id" => "addCapability" . $this->getUniqid(),
                            "title" => "Ajouter une aptitude",
                            "label" => "Rechercher une aptitude",
                            "placeholder" => "Rechercher une aptitude",
                            "search_in" => ControllerModule::SEARCH_IN_CAPABILITY,
                            "parameter" => $this->getUniqid(),
                            "action" => ControllerModule::SEARCH_DONE_ADD_CAPABILITY_TO_MOB,
                        ], 
                        write: false);

                    return $html . $this->getCapability(Content::DISPLAY_RESUME, true);

                case Content::DISPLAY_RESUME:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($capabilities)){
                        return $view->dispatch(
                            template_name : "capability/list",
                            data : [
                                "capabilities" => $capabilities,
                                "is_removable" => $display_remove,
                                "uniqid" => $this->getUniqid(),
                                "class_name" => "Classe",
                                "size" => $size
                            ], 
                            write: false);
                    }
                    return "";

                case Content::DISPLAY_LIST:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($capabilities)){
                        ob_start();
                            ?> <ul class="list-unstyled"> <?php
                                foreach ($capabilities as $capability) {?>
                                    <li>
                                        <?php $view->dispatch(
                                            template_name : "capability/text",
                                            data : [
                                                "obj" => $capability,
                                                "is_link" => true
                                            ], 
                                            write: true); ?>
                                    </li> <?php
                                }
                            ?> </ul> <?php
                        return ob_get_clean();
                    }
                    return "";

                case Content::FORMAT_ARRAY:
                    return $capabilities;
            }
        }

        public function getPowerful(int $format = Content::FORMAT_BRUT){
            $powerful = 0;
            $powerful_spell = 0;
            $n = 0;
            foreach ($this->getSpell(Content::FORMAT_ARRAY) as $spell) {
                $powerful_spell += $spell->getPowerful();
                $n++;
            }
            
            if($n > 0){
                $powerful = round($powerful_spell / $n);
            }

            if(is_numeric($this->getLevel())){
                $level = $this->getLevel();
            } else {
                $level_form = Content::getMinMaxFromFormule($this->getLevel());
                if(!is_numeric($level_form["min"]) || !is_numeric($level_form["max"])){
                    $level = ((int)$level_form["min"] + (int)$level_form["max"]) / 2;
                } else {
                    $level = 1;
                }
            }

            $factor = Controller::calcExp("0,157894737 * (level + 3)", ["level" => $level]);

            $pa_expected = Controller::calcExp(ControllerModule::BALANCE_PA["mob"]["expression"], ["level" => $level]);
            if(is_numeric($this->getPa())){
                $pa = $this->getPa();
            } else {
                $pa_form = Content::getMinMaxFromFormule($this->getPa());
                if(!is_numeric($pa_form["min"]) || !is_numeric($pa_form["max"])){
                    $pa = ((int)$pa_form["min"] + (int)$pa_form["max"]) / 2;
                } else {
                    $pa = ControllerModule::BALANCE_PA['mob']["base"];
                }
            }
            if($pa > $pa_expected + $factor){
                $powerful += 1;
            }elseif($pa < $pa_expected - $factor){
                $powerful -= 1;
            }
            $pm_expected = Controller::calcExp(ControllerModule::BALANCE_PM["mob"]["expression"], ["level" => $level]);
            if(is_numeric($this->getPm())){
                $pm = $this->getPm();
            } else {
                $pm_form = Content::getMinMaxFromFormule($this->getPm());
                if(!is_numeric($pm_form["min"]) || !is_numeric($pm_form["max"])){
                    $pm = ((int)$pm_form["min"] + (int)$pm_form["max"]) / 2;
                } else {
                    $pm = ControllerModule::BALANCE_PM['mob']["base"];
                }
            }
            if($pm > $pm_expected + $factor){
                $powerful += 1;
            }elseif($pm < $pm_expected - $factor){
                $powerful -= 1;
            }

            $factor = Controller::calcExp("2,631578947 * (level + 5)", ["level" => $level]);

            $life_expected = Controller::calcExp(ControllerModule::BALANCE_LIFE["mob"]["expression"], ["level" => $level]);
            if(is_numeric($this->getLife())){
                $life = $this->getLife();
            } else {
                $life_form = Content::getMinMaxFromFormule($this->getLife());
                if(!is_numeric($life_form["min"]) || !is_numeric($life_form["max"])){
                    $life = ((int)$life_form["min"] + (int)$life_form["max"]) / 2;
                } else {
                    $life = ControllerModule::BALANCE_LIFE['mob']["base"];
                }
                
            }
            if($life > $life_expected + $factor){
                $powerful += 1;
            }elseif($life < $life_expected - $factor){
                $powerful -= 1;
            }

            if($powerful > 9){$powerful = 9;}
            if($powerful < 1){$powerful = 1;}
            
            $view = new View();
            switch ($format) {
                case Content::FORMAT_BADGE:
                    if(in_array($powerful, [1,2,3,4,5,6,7,8,9])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "Puissance ".$powerful,
                                "color" => "deep-purple-d-3",
                                "tooltip" => "Puissance d'une créature sur 9 niveaux en fonction de ces sorts",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else {
                        return "";
                    }

                case Content::FORMAT_ICON:
                    if(in_array($powerful, [1,2,3,4,5,6,7,8,9])){
                        return $view->dispatch(
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "fist-raised",
                                "color" => "deep-purple-d-3",
                                "tooltip" => "Puissance d'une créature sur 9 niveaux en fonction de ces sorts",
                                "content" => $powerful,
                                "content_placement" => Style::POSITION_LEFT
                            ], 
                            write: false);

                    } else {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($powerful, [1,2,3,4,5,6,7,8,9])){
                        return $powerful;
                    } else {
                        return "";
                    }

                default:
                    return $powerful;
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName(string | int $data){
            $this->_name = $data;
            return true;
        }
        public function setDescription(string | null $data){
            $this->_description = $data;
            return true;
        }
        public function setLevel(string | int $data){
            $this->_level = $data;
            return true;
        }
        public function setVitality(string | int | null $data){
            $this->_vitality = $data;
            return true;
        }
        public function setPa(string | int | null $data){
            $this->_pa = $data;
            return true;
        }
        public function setPm(string | int | null $data){
            $this->_pm = $data;
            return true;
        }
        public function setPo(string | int | null $data){
            $this->_po = $data;
            return true;
        }
        public function setIni(string | int| null  $data){
            $this->_ini = $data;
            return true;
        }
        public function setTouch(string | int | null $data){
            if(empty($data)){
                $this->_touch = 0;
            } else {
                $this->_touch = $data;
            }
            return true;
        }
        public function setLife(string | int | null $data){
            $this->_life = $data;
            return true;
        }
        public function setSagesse(string | int | null $data){
            $this->_sagesse = $data;
            return true;
        }
        public function setStrong(string | int | null $data){
            $this->_strong = $data;
            return true;
        }
        public function setIntel(string | int | null $data){
            $this->_intel = $data;
            return true;
        }
        public function setAgi(string | int | null $data){
            $this->_agi = $data;
            return true;
        }
        public function setChance(string | int | null $data){
            $this->_chance = $data;
            return true;
        }
        public function setCa(string | int | null $data){
            $this->_ca = $data;
            return true;
        }
        public function setFuite(string | int | null $data){
            $this->_fuite = $data;
            return true;
        }
        public function setTacle(string | int | null $data){
            $this->_tacle = $data;
            return true;
        }
        public function setDodge_pa(string | int | null $data){
            $this->_dodge_pa = $data;
            return true;
        }
        public function setDodge_pm(string | int | null $data){
            $this->_dodge_pm = $data;
            return true;
        }
        public function setRes_neutre(string | int | null $data){
            $this->_res_neutre = $data;
            return true;
        }
        public function setRes_terre(string | int | null $data){
            $this->_res_terre = $data;
            return true;
        }
        public function setRes_feu(string | int | null $data){
            $this->_res_feu = $data;
            return true;
        }
        public function setRes_air(string | int | null $data){
            $this->_res_air = $data;
            return true;
        }
        public function setRes_eau(string | int | null $data){
            $this->_res_eau = $data;
            return true;
        }
        public function setZone(string | int | null $data){
            $this->_zone = $data;
            return true;
        }
        public function setHostility(int | null $data){
            if(in_array($data, Mob::HOSTILITY)){
                $this->_hostility = $data;
                return true;
            } else {
                $this->_hostility = Mob::HOSTILITY["neutre"];
                throw new Exception("Donnée non valide;");
            }
        }
        public function setTrait(string | int | null $data){
            $this->_trait = $data;
            return true;
        }
        public function setSize(int | null $data){
            if(in_array($data, Mob::SIZE)){
                $this->_size = $data;
                return true;
            } else {
                $this->_size = Mob::SIZE["moyenne"];
                throw new Exception("Donnée non valide;");
            }
        }

        /* Data = array(
                        uniqid => id du spell
                    )
            Js : Mob.update(UniqidM,{action:'add|remove|update', uniqid:'uniqIdS'},'spell', IS_VALUE);
        */
        public function setSpell(array $data){ 
            $managerM = new MobManager;
            $managerS = new SpellManager;
            if(!isset($data['uniqid'])){throw new Exception("L'uniqid du sort n'est pas défini");}
            if($managerS->existsUniqid($data['uniqid'])){
                $spell = $managerS->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            return $managerM->addLinkSpell($this, $spell);
               
                        case "remove":
                            return $managerM->removeLinkSpell($this, $spell);

                        default:
                            throw new Exception("L'action n'est pas valide");
                    }

                } else {
                    throw new Exception("Une action est requise.");
                }

            }
        }

                /* Data = array(uniqid => id du capability)
            Js : Classe.update(UniqidM,{action:'add|remove|update', uniqid:'uniqIdS'},'capability', IS_VALUE);
        */
        public function setCapability(array $data){ 
            $manager = new MobManager;
            $managerS = new CapabilityManager;
            if(!isset($data['uniqid'])){throw new Exception("L'uniqid de l'aptitude n'est pas défini");}
            if($managerS->existsUniqid($data['uniqid'])){
                $capability = $managerS->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            if($manager->addLinkCapability($this, $capability)){
                                return true;
                            }else{
                                throw new Exception("Erreur lors de l'ajout de l'aptitude");
                            }
               
                        case "remove":
                            if($manager->removeLinkCapability($this, $capability)){
                                return true;
                            }else{
                                throw new Exception("Erreur lors de la suppression de l'aptitude");
                            }

                        default:
                            throw new Exception("L'action n'est pas valide");
                    }

                } else {
                    throw new Exception("Une action est requise.");
                }

            }
        }
}