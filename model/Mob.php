<?php
class Mob extends Content
{
    public function __construct(array $donnees){
        $this->hydrate($donnees);

        if(file_exists("medias/mobs/".$this->getUniqid().".svg")){
            $this->setPath_image("medias/mobs/".$this->getUniqid().".svg");
        }
    }

    const HOSTILITY = [
        "amicale" => 0,
        "currieux" => 1,
        "neutre" => 2,
        "perreux" => 3,
        "agressif" => 4,
        "hostile" => 5
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
        private $_path_img="medias/mobs/default.svg";
        private $_usable=false;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <input 
                                onchange="Mob.update('<?=$this->getUniqid();?>', this, 'name');" 
                                placeholder="Nom de la créature" 
                                maxlength="300" 
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_name?>">
                            <label class="size-0-8">Nom de la créature</label>
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_name;
            }

        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <textarea 
                                placeholder=""
                                onchange="Mob.update('<?=$this->getUniqid();?>', this, 'description');" 
                                class="form-control form-control-main-focus" 
                                maxlength="20000"><?=$this->_description?></textarea>
                            <label class="size-0-8">Description</label>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_description;
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-<?=View::getColorFromLetter($this->_level, true)?>-d-3">
                            <label>Niveau</label>
                            <input 
                                onchange="Mob.update('<?=$this->getUniqid();?>', this, 'level');" 
                                data-bs-toggle='tooltip' data-bs-placement='bottom' title="Niveau de la créature"
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_level?>">
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span id='level' data-formule='".$this->_level."' class='badge back-".View::getColorFromLetter($this->_level, true)."-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Niveau de la créature\">Niveau {$this->_level}</span>";

                                    
                case Content::FORMAT_LIST:
                    if(preg_match("/\[.*\]/", $this->_level)){
                        ob_start(); ?>
                            <div class="dropdown">
                                <a class="btn btn-text-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><?=$this->getLevel(Content::FORMAT_BADGE)?></a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" onclick="Mob.updateDisplayCaracteristics('<?=$this->getUniqid()?>', 0)"><span class='badge back-<?=View::getColorFromLetter($this->_level, true)?>-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Niveau des créatures">Niveau <?=$this->_level?></span></a></li>
                                    <?php $level = Content::getMinMaxFromFormule($this->_level);
                                    if($level['same']){ ?>
                                        <li><a class="dropdown-item" onclick="Mob.updateDisplayCaracteristics('<?=$this->getUniqid()?>', '<?=$level['max']?>')"><span class='badge back-<?=View::getColorFromLetter($level['max'], true)?>-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Générer une créature de niveau <?=$level["max"]?>">Niveau <?=$level["max"]?></span></a></li>
                                    <?php } else {
                                        if($level['min'] == 0){$level['min'] = 1;}
                                        if($level['max'] == 0){$level['max'] = 1;}
                                        for($i=$level['min']; $i<=$level['max']; $i++){ ?>
                                            <li><a class="dropdown-item" onclick="Mob.updateDisplayCaracteristics('<?=$this->getUniqid()?>', '<?=$i?>')"><span class='badge back-<?=View::getColorFromLetter($i, true)?>-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Générer une créature de niveau <?=$i?>">Niveau <?=$i?></span></a></li>
                                        <?php }
                                    } ?>
                                </ul>
                            </div>
                        <?php return ob_get_clean();
                    } else {
                        return $this->getLevel(Content::FORMAT_BADGE);
                    }
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-".View::getColorFromLetter($this->_level, true)."-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Niveau de la créature\">{$this->_level}</span>";
                
                default:
                    return $this->_level;
            }
        }
        public function getLife(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-life-d-2">
                            <label>Points de vie</label>
                            <input 
                                onchange="Mob.update('<?=$this->getUniqid();?>', this, 'life');" 
                                data-bs-toggle='tooltip' data-bs-placement='bottom' title="Calcul des points de vie de la créature"
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_life?>">
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_life, $i)."' ";
                        }
                    }

                    return "<span id='life' ".$data." data-formule='".$this->_life."' class='badge back-life-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul des points de vie de la créature\">{$this->_life} Points de vie</span>";
                   
                    case Content::FORMAT_ICON:
                        return "<span class='text-life' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Points de vie\">{$this->_life} <img class='icon' src='medias/icons/life.svg'></span>";
                
                default:
                    return $this->_life;
            }
        }
        public function getPa(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-pa-d-4">
                            <label>Points d'actione</label>
                            <div class="input-group">
                                <div class="input-group-text back-pa-d-2 text-white"><img class='icon' src='medias/icons/pa.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'pa');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Calcul des PA de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_pa?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_pa, $i)."' ";
                        }
                    }

                    return "<span id='pa' ".$data." data-formule='".$this->_pa."' class='badge back-pa-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul des PA de la créature\">{$this->_pa} PA</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pa-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul des PA de la créature\">{$this->_pa} <img class='icon' src='medias/icons/pa.png'></span>";
                
                default:
                    return $this->_pa;
            }
        }
        public function getPm(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-pm-d-4">
                            <label>Points de mouvement</label>
                            <div class="input-group">
                                <div class="input-group-text back-pm-d-2 text-white"><img class='icon' src='medias/icons/pm.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'pm');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Calcul des PM de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_pm?>">        
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_pm, $i)."' ";
                        }
                    }

                    return "<span id='pm' ".$data." data-formule='".$this->_pm."' class='badge back-pm-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul des PM de la créature\">{$this->_pm} PM</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pm-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul des PM de la créature\">{$this->_pm} <img class='icon' src='medias/icons/pm.png'></span>";
                
                default:
                    return $this->_pm;
            }
        }
        public function getPo(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-po-d-4">
                            <label>Bonus de portée</label>
                            <div class="input-group">
                                <div class="input-group-text back-po-d-2 text-white"><img class='icon' src='medias/icons/po.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'po');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de portée de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_po?>">        
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_po, $i)."' ";
                        }
                    }

                    return "<span id='po' ".$data." data-formule='".$this->_po."' class='badge back-po-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de portée de la créature\">{$this->_po} PO</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-po-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de portée de la créature\">{$this->_po} <img class='icon' src='medias/icons/po.png'></span>";
                
                default:
                    return $this->_po;
            }
        }
        public function getIni(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-ini-d-4">
                            <label>Bonus d'Initiative</label>
                            <div class="input-group">
                                <div class="input-group-text back-ini-d-2 text-white"><img class='icon' src='medias/icons/ini.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'ini');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus d'initiative de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_ini?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">1d20 + mod. Intel + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_ini, $i)."' ";
                        }
                    }

                    return "<span id='ini' ".$data." data-formule='".$this->_ini."' class='badge back-ini-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'initiative de la créature\">{$this->_ini} Initiative</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-ini-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'initiative de la créature\">{$this->_ini} <img class='icon' src='medias/icons/ini.png'></span>";
                
                default:
                    return $this->_ini;
            }
        }
        public function getTouch(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-touch-d-4">
                            <label>Bonus de Touche</label>
                            <div class="input-group">
                                <div class="input-group-text back-touch text-white"><img class='icon' src='medias/icons/touch.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'touch');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de touche de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_touch?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_touch, $i)."' ";
                        }
                    }

                    return "<span id='touch' ".$data." data-formule='".$this->_touch."' class='badge back-touch' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de touche de la créature\">{$this->_touch} Bonus de Touche</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-touch-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de touche de la créature\">{$this->_touch} <img class='icon-sm' src='medias/icons/touch.png'></span>";
                
                default:
                    return $this->_touch;
            }
        }
        public function getVitality(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-vitality-d-4">
                            <label>Mod. Vitalité</label>
                            <div class="input-group">
                                <div class="input-group-text back-vitality-d-4 text-white"><img class='icon' src='medias/icons/vitality.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'vitality');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Calcul du modificateur de vitalité de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_vitality?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_vitality, $i)."' ";
                        }
                    }

                    return "<span id='vitality' ".$data." data-formule='".$this->_vitality."' class='badge back-vitality-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de vitalité de la créature\">{$this->_vitality} Mod. Vitalité</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-vitality-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de vitalité de la créature\">{$this->_vitality} <img class='icon' src='medias/icons/vitality.png'></span>";
                
                default:
                    return $this->_vitality;
            }
        }
        public function getSagesse(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-sagesse-d-4">
                            <label>Mod. Sagesse</label>
                            <div class="input-group">
                                <div class="input-group-text back-sagesse-d-4 text-white"><img class='icon' src='medias/icons/sagesse.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'sagesse');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Calcul du modificateur de sagesse de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_sagesse?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_sagesse, $i)."' ";
                        }
                    }

                    return "<span id='sagesse' ".$data." data-formule='".$this->_sagesse."' class='badge back-sagesse-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de sagesse de la créature\">{$this->_sagesse} Mod. Sagesse</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-sagesse-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de sagesse de la créature\">{$this->_sagesse} <img class='icon' src='medias/icons/sagesse.png'></span>";
                
                default:
                    return $this->_sagesse;
            }
        }
        public function getStrong(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-force-d-4">
                            <label>Mod. Force</label>
                            <div class="input-group">
                                <div class="input-group-text back-force-d-4 text-white"><img class='icon' src='medias/icons/force.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'strong');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Calcul du modificateur de force de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_strong?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_strong, $i)."' ";
                        }
                    }

                    return "<span id='strong' ".$data." data-formule='".$this->_strong."' class='badge back-force-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de force de la créature\">{$this->_strong} Mod. Force</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-force-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de force de la créature\">{$this->_strong} <img class='icon' src='medias/icons/force.png'></span>";
                
                default:
                    return $this->_strong;
            }
        }
        public function getIntel(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-intel-d-4">
                            <label>Mod. Intelligence</label>
                            <div class="input-group">
                                <div class="input-group-text back-intel-d-4 text-white"><img class='icon' src='medias/icons/intel.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'intel');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Calcul du modificateur d'intelligence de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_intel?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_intel, $i)."' ";
                        }
                    }

                    return "<span id='intel' ".$data." data-formule='".$this->_intel."' class='badge back-intel-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur d'intelligence de la créature\">{$this->_intel} Mod. Intel</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-intel-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur d'intelligence de la créature\">{$this->_intel} <img class='icon' src='medias/icons/intel.png'></span>";
                
                default:
                    return $this->_intel;
            }
        }
        public function getAgi(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-agi-d-4">
                            <label>Mod. Agilité</label>
                            <div class="input-group">
                                <div class="input-group-text back-agi-d-4 text-white"><img class='icon' src='medias/icons/agi.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'agi');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Calcul du modificateur d'agilité de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_agi?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_agi, $i)."' ";
                        }
                    }

                    return "<span id='agi' ".$data." data-formule='".$this->_agi."' class='badge back-agi-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur d'agilité de la créature\">{$this->_agi} Mod. Agi</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-agi-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur d'agilité de la créature\">{$this->_agi} <img class='icon' src='medias/icons/agi.png'></span>";
                
                default:
                    return $this->_agi;
            }
        }
        public function getChance(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-chance-d-4">
                            <label>Mod. Chance</label>
                            <div class="input-group">
                                <div class="input-group-text back-chance-d-4 text-white"><img class='icon' src='medias/icons/chance.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'chance');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Calcul du modificateur de chance de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_chance?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_chance, $i)."' ";
                        }
                    }

                    return "<span id='chance' ".$data." data-formule='".$this->_chance."' class='badge back-chance-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de chance de la créature\">{$this->_chance} Mod. Chance</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-chance-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de chance de la créature\">{$this->_chance} <img class='icon' src='medias/icons/chance.png'></span>";
                
                default:
                    return $this->_chance;
            }
        }
        public function getCa(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-ca-d-4">
                            <label>Bonus de Classe d'armure</label>
                            <div class="input-group">
                                <div class="input-group-text back-grey text-white"><img class='icon' src='medias/icons/ca.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'ca');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de la classe d'armure de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_ca?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">1d20 + mod. Vitalité + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_ca, $i)."' ";
                        }
                    }

                    return "<span id='ca' ".$data." data-formule='".$this->_ca."' class='badge back-ca-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de la classe d'armure de la créature\">{$this->_ca} CA</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-ca-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de la classe d'armure de la créature\">{$this->_ca} <img class='icon' src='medias/icons/ca.png'></span>";
                
                default:
                    return $this->_ca;
            }
        }
        public function getFuite(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-fuite-d-4">
                            <label>Bonus de Fuite</label>
                            <div class="input-group">
                                <div class="input-group-text back-fuite-d-4 text-white"><img class='icon' src='medias/icons/fuite.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'fuite');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de fuite de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_fuite?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">1d20 + mod. Agilité + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_fuite, $i)."' ";
                        }
                    }

                    return "<span id='fuite' ".$data." data-formule='".$this->_fuite."' class='badge back-fuite-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de fuite de la créature\">{$this->_fuite} Fuite</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-fuite-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de fuite de la créature\">{$this->_fuite} <img class='icon' src='medias/icons/fuite.png'></span>";
                
                default:
                    return $this->_fuite;
            }
        }
        public function getTacle(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-tacle-d-4">
                            <label>Bonus de Tacle</label>
                            <div class="input-group">
                                <div class="input-group-text back-tacle-d-4 text-white"><img class='icon' src='medias/icons/tacle.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'tacle');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de tacle de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_tacle?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">1d20 + mod. Chance + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_tacle, $i)."' ";
                        }
                    }

                    return "<span id='tacle' ".$data." data-formule='".$this->_tacle."' class='badge back-tacle-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de tacle de la créature\">{$this->_tacle} Tacle</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-tacle-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de tacle de la créature\">{$this->_tacle} <img class='icon' src='medias/icons/tacle.png'></span>";
                
                default:
                    return $this->_tacle;
            }
        }
        public function getDodge_pa(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-pa-d-4">
                            <label>Bonus d'Esquive PA</label>
                            <div class="input-group">
                                <div class="input-group-text back-pa-d-4 text-white"><img class='icon' src='medias/icons/dodge_pa.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'dodge_pa');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus d'Esquive PA de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_dodge_pa?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">1d20 + mod. Sagesse + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_dodge_pa, $i)."' ";
                        }
                    }

                    return "<span id='dodge_pa' ".$data." data-formule='".$this->_dodge_pa."' class='badge back-pa-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'Esquive PA de la créature\">{$this->_dodge_pa} Esquive PA</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pa-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'Esquive PA de la créature\">{$this->_dodge_pa} <img class='icon' src='medias/icons/dodge_pa.png'></span>";
                
                default:
                    return $this->_dodge_pa;
            }
        }
        public function getDodge_pm(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-pm-d-4">
                            <label>Bonus d'Esquive PM</label>
                            <div class="input-group">
                                <div class="input-group-text back-pm-d-4 text-white"><img class='icon' src='medias/icons/dodge_pm.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'dodge_pm');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus d'Esquive PM de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_dodge_pm?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">1d20 + mod. Sagesse + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_dodge_pm, $i)."' ";
                        }
                    }

                    return "<span id='dodge_pm' ".$data." data-formule='".$this->_dodge_pm."' class='badge back-pm-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'Esquive PM de la créature\">{$this->_dodge_pm} Esquive PM</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pm-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'Esquive PM de la créature\">{$this->_dodge_pm} <img class='icon' src='medias/icons/dodge_pm.png'></span>";
                
                default:
                    return $this->_dodge_pm;
            }
        }
        public function getRes_neutre(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-black">
                            <label>Resistance neutre</label>
                            <div class="input-group">
                                <div class="input-group-text back-grey-d-4 text-white"><img class='icon' src='medias/icons/res_neutre.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'res_neutre');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance neutre de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_neutre?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_neutre, $i)."' ";
                        }
                    }

                    return "<span id='res_neutre' ".$data." data-formule='".$this->_res_neutre."' class='badge back-grey-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance neutre de la créature\">{$this->_res_neutre} Résistance Neutre</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-black' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance neutre de la créature\">{$this->_res_neutre} <img class='icon' src='medias/icons/res_neutre.png'></span>";
                
                default:
                    return $this->_res_neutre;
            }
        }
        public function getRes_terre(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-force-d-4">
                            <label>Resistance terre</label>
                            <div class="input-group">
                                <div class="input-group-text back-force-d-4 text-white"><img class='icon' src='medias/icons/res_terre.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'res_terre');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance terre de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_terre?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_terre, $i)."' ";
                        }
                    }

                    return "<span id='res_terre' ".$data." data-formule='".$this->_res_terre."' class='badge back-force-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance terre de la créature\">{$this->_res_terre} Résistance terre</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-force-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance terre de la créature\">{$this->_res_terre} <img class='icon' src='medias/icons/res_terre.png'></span>";
                
                default:
                    return $this->_res_terre;
            }
        }
        public function getRes_feu(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-intel-d-4">
                            <label>Resistance feu</label>
                            <div class="input-group">
                                <div class="input-group-text back-intel-d-4 text-white"><img class='icon' src='medias/icons/res_feu.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'res_feu');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance feu de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_feu?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_feu, $i)."' ";
                        }
                    }

                    return "<span id='res_feu' ".$data." data-formule='".$this->_res_feu."' class='badge back-intel-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance feu de la créature\">{$this->_res_feu} Résistance feu</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-intel-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance feu de la créature\">{$this->_res_feu} <img class='icon' src='medias/icons/res_feu.png'></span>";
                
                default:
                    return $this->_res_feu;
            }
        }
        public function getRes_air(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-agi-d-4">
                            <label>Resistance air</label>
                            <div class="input-group">
                                <div class="input-group-text back-agi-d-4 text-white"><img class='icon' src='medias/icons/res_air.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'res_air');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance air de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_air?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_air, $i)."' ";
                        }
                    }

                    return "<span id='res_air' ".$data." data-formule='".$this->_res_air."' class='badge back-agi-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance air de la créature\">{$this->_res_air} Résistance air</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-agi-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance air de la créature\">{$this->_res_air} <img class='icon' src='medias/icons/res_air.png'></span>";
                
                default:
                    return $this->_res_air;
            }
        }
        public function getRes_eau(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-chance-d-4">
                            <label>Resistance eau</label>
                            <div class="input-group">
                                <div class="input-group-text back-chance-d-4 text-white"><img class='icon' src='medias/icons/res_eau.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'res_eau');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance eau de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_eau?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    $level = Content::getMinMaxFromFormule($this->getLevel());$data = "";
                    if($level['same'] != true){
                        for($i=$level['min']; $i<=$level['max']; $i++){
                            $data .= " data-level".$i."='".Content::getValueFromFormule($this->_res_eau, $i)."' ";
                        }
                    }

                    return "<span id='res_eau' ".$data." data-formule='".$this->_res_eau."' class='badge back-chance-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance eau de la créature\">{$this->_res_eau} Résistance eau</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-chance-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance eau de la créature\">{$this->_res_eau} <img class='icon' src='medias/icons/res_eau.png'></span>";
                
                default:
                    return $this->_res_eau;
            }
        }
        public function getZone(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <div class="input-group">
                                <div class="input-group-text back-main-d-2 text-white"><i class="fas fa-map-marker-alt"></i></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'zone');" 
                                    placeholder="Zone de vie de la créature"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_zone?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_TEXT:
                    ob_start(); ?>
                        <p>
                            <i class="fas fa-map-marker-alt text-main-d-2 me-2"></i>
                            <?=$this->_zone?>
                        </p>
                    <?php ob_get_clean(); 
                
                default:
                    return $this->_zone;
            }

        }
        public function getHostility(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="dropdown">
                            <a class="" type="button" id="hostility<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getHostility(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="hostility<?=$this->getId()?>"> <?php
                                foreach (Mob::HOSTILITY as $name => $hostility) { ?>
                                    <a class="dropdown-item" onclick="Mob.update('<?=$this->getUniqid()?>', <?=$hostility?>, 'hostility', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-main-d-2'><?=$name?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php return ob_get_clean();
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_hostility, Mob::HOSTILITY)){
                        return "<span class='badge back-white border border-main-d-3 text-main-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Agréssivité de la créature\">".array_search($this->_hostility, Mob::HOSTILITY)."</span>";
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
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <input 
                                onchange="Mob.update('<?=$this->getUniqid();?>', this, 'trait');" 
                                placeholder="Traits de la classe" 
                                maxlength="3000" 
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_trait?>">
                            <label class="size-0-8">Traits de la créature</label>
                        </div>
                        <span class="size-0-8 text-grey">Séparer les différents traits par des virgules.</span>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="mt-1 d-flex flex-row justify-content-start"> <?php
                            foreach (explode(",", $this->_trait) as $trait) { ?>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Trait <?=$trait?>" class="me-1 badge back-<?=View::getColorFromLetter($trait)?>-d-1"><?=ucfirst(trim($trait))?></span>
                            <?php } ?>                            
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_trait;
            }

        }
        public function getPath_img(int $format = Content::FORMAT_BRUT, $css = ""){
            if(file_exists("medias/mobs/".$this->getUniqid().".svg")){
                $this->setPath_image("medias/mobs/".$this->getUniqid().".svg");
            }
            
            if(!empty($this->_path_img) || file_exists($this->_path_img)){
                $path = $this->_path_img;
            } else {
                $path = "medias/mobs/default.svg";
            }

            switch ($format) {
                case  Content::FORMAT_BRUT:
                    return $path;
                break;
                case  Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="text-center">
                            <div id='showFile_path_image' class='m-2 d-flex justify-content-center'><?=$this->getPath_img(Content::FORMAT_FANCY, 'img-back-180');?></div>
                            <a type="button" 
                                class="btn btn-border-secondary btn-sm me-2" 
                                onclick=''>
                                <i class="fas fa-cloud-upload-alt"></i> Modifier
                            </a>
                        </div>
                    <?php return ob_get_clean();
                break;
                case  Content::FORMAT_IMAGE:
                    if($css == "") { $css = "img-back-90 rounded-circle";}
                    ob_start(); ?>
                        <div class='<?=$css?>' style="background-image:url('<?=$path?>')"></div>
                    <?php return ob_get_clean();
                break;
                case Content::FORMAT_FANCY:
                    $svg_plugin = "";
                    if(strtolower(pathinfo($path)['extension']) == "svg"){
                        $svg_plugin = "data-type='iframe'";
                    }
                    if($css == "") { $css = "img-back-30 rounded-circle";}
                    ob_start(); ?>
                        <a data-fancybox="gallery" <?=$svg_plugin?> href='<?=$path?>' class="d-flex justify-content-center"><div class='<?=$css?>' style='background-image:url("<?=$path?>")'></div></a>
                    <?php return ob_get_clean();
                break;
                
                default:
                    return $path;
                break;
            }
        }
        public function getUsable(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    $checked = ""; 
                    if($this->_usable){ $checked = "checked"; } else { $checked = ""; }
                    ob_start(); ?>
                        <div style="width:initial;" class="form-check form-switch my-1">
                            <input onchange="Mob.update('<?=$this->getUniqid();?>', this, 'usable', <?=Controller::IS_CHECKBOX?>);"  type="checkbox" class="form-check-input back-main-d-1 border-main-d-1" <?=$checked?> id="customSwitchUsable<?=$this->getId()?>">
                            <label class="custom-control-label" for="customSwitchUsable<?=$this->getUniqid()?>"><?=$this->getUsable(Content::FORMAT_BADGE);?></label>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($this->_usable){ 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title=\"L'objet a été adapté au jdr\" class='badge back-green-d-3'>Adapté au jdr</span>";
                    } else { 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title=\"L'objet n'a pas encore été adapté au jdr - N'hésitez pas à le modifier\" class='badge back-red-d-3'>Non adapté au jdr</span>";
                    }

                case Content::FORMAT_ICON:
                    if($this->_usable){ 
                        return "<i data-bs-toggle='tooltip' data-bs-placement='top' title=\"L'objet a été adapté au jdr\" class='fas fa-check text-green-d-3'></i>";
                    } else { 
                        return "<i data-bs-toggle='tooltip' data-bs-placement='top' title=\"L'objet n'a pas encore été adapté au jdr - N'hésitez pas à le modifier\" class='fas fa-times text-red-d-3'></i>";
                    }
                    
                default:
                    return $this->_usable;
            }
        }

        public function getSpell(int $format = Content::FORMAT_BRUT, $display_remove = false){
            $manager = new MobManager();
            $spells = $manager->getLinkSpell($this);
            
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <h6 class="mt-1">Ajouter des sorts</h6>
                        <div class="d-flex flex-row justify-content-evenly align-items-baseline mb-3 position-relative">
                            <div class="form-floating w-100">
                                <input  type="text" 
                                        data-url = "index.php?c=search&a=search"
                                        data-search_in = <?=ControllerSearch::SEARCH_IN_SPELL?>
                                        data-minlenght = 3
                                        data-parameter = "<?=$this->getUniqid()?>"
                                        data-action = <?=ControllerSearch::SEARCH_DONE_ADD_SPELL_TO_MOB?>
                                        data-limit = 10
                                        data-only_usable = false
                                        class="form-control form-control-main-focus" 
                                        id="addSpell<?=$this->getUniqid()?>" 
                                        placeholder="Rechercher un sort">
                                <label for="addSpell<?=$this->getUniqid()?>">Rechercher un sort</label>
                            </div>
                            <span id="search-sign"></span>
                        </div>
                        <script>autocomplete_load("#addSpell<?=$this->getUniqid()?>");</script>
                        <?=$this->getSpell(Content::DISPLAY_RESUME, true)?>
                    <?php return ob_get_clean();

                case Content::DISPLAY_RESUME:
                    ob_start(); 
                    if(!empty($spells)){?>
                        <div>
                            <div class="d-flex flex-row justify-content-around flex-wrap">
                                <?php foreach ($spells as $spell) { ?>
                                    <div class="m-2" style="position:relative;width:300px;">
                                        <?php if($display_remove){ ?>
                                            <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                                                <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher ce sort de cette créature" class="p-4 <?=View::getCss(View::TYPE_BTN_UNDERLINE, "red")?>" onclick="if (confirm('Etes vous sûr d\'étacher le sort de cette créature ?')){Mob.update('<?=$this->getUniqid()?>',{action:'remove', uniqid:'<?=$spell['obj']->getUniqid()?>'},'spell', IS_VALUE);}"><i class="fas fa-times"></i></a>
                                            </div>
                                        <?php } ?>
                                        <?= $spell['obj']->getVisual(Content::DISPLAY_RESUME);?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php }
                    return ob_get_clean();

                case Content::FORMAT_ARRAY:
                    if(!empty($spells)){return $spells;}else{return [];}
                        
            }
        }

        public function getPowerful(int $format = Content::FORMAT_BRUT){
            $powerful = 0;
            $powerful_spell = 0;
            $n = 0;
            foreach ($this->getSpell(Content::FORMAT_ARRAY) as $spell) {
                $powerful_spell += $spell['obj']->getPowerful();
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

            $pa_expected = Controller::calcExp(Controller::BALANCE_PA["mob"]["expression"], ["level" => $level]);
            if(is_numeric($this->getPa())){
                $pa = $this->getPa();
            } else {
                $pa_form = Content::getMinMaxFromFormule($this->getPa());
                if(!is_numeric($pa_form["min"]) || !is_numeric($pa_form["max"])){
                    $pa = ((int)$pa_form["min"] + (int)$pa_form["max"]) / 2;
                } else {
                    $pa = Controller::BALANCE_PA['mob']["base"];
                }
            }
            if($pa > $pa_expected + $factor){
                $powerful += 1;
            }elseif($pa < $pa_expected - $factor){
                $powerful -= 1;
            }
            $pm_expected = Controller::calcExp(Controller::BALANCE_PM["mob"]["expression"], ["level" => $level]);
            if(is_numeric($this->getPm())){
                $pm = $this->getPm();
            } else {
                $pm_form = Content::getMinMaxFromFormule($this->getPm());
                if(!is_numeric($pm_form["min"]) || !is_numeric($pm_form["max"])){
                    $pm = ((int)$pm_form["min"] + (int)$pm_form["max"]) / 2;
                } else {
                    $pm = Controller::BALANCE_PM['mob']["base"];
                }
            }
            if($pm > $pm_expected + $factor){
                $powerful += 1;
            }elseif($pm < $pm_expected - $factor){
                $powerful -= 1;
            }

            $factor = Controller::calcExp("2,631578947 * (level + 5)", ["level" => $level]);

            $life_expected = Controller::calcExp(Controller::BALANCE_LIFE["mob"]["expression"], ["level" => $level]);
            if(is_numeric($this->getLife())){
                $life = $this->getLife();
            } else {
                $life_form = Content::getMinMaxFromFormule($this->getLife());
                if(!is_numeric($life_form["min"]) || !is_numeric($life_form["max"])){
                    $life = ((int)$life_form["min"] + (int)$life_form["max"]) / 2;
                } else {
                    $life = Controller::BALANCE_LIFE['mob']["base"];
                }
                
            }
            if($life > $life_expected + $factor){
                $powerful += 1;
            }elseif($life < $life_expected - $factor){
                $powerful -= 1;
            }

            if($powerful > 7){$powerful = 7;}
            if($powerful < 1){$powerful = 1;}
            
            switch ($format) {
                case Content::FORMAT_BADGE:
                    if(in_array($powerful, [1,2,3,4,5,6,7])){
                        return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Puissance d'une créature sur 7 niveaux en fonction de ces sorts\" class='badge back-deep-purple-d-3'>Puissance ".$powerful."</span>";
                    } else {
                        return "";
                    }

                case Content::FORMAT_ICON:
                    if(in_array($powerful, [1,2,3,4,5,6,7])){
                        return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Puissance d'une créature sur 7 niveaux en fonction de ces sorts\" class='badge back-deep-purple-d-3'><i class='fas fa-fist-raised'></i> ".$powerful."</span>";
                    } else {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($powerful, [1,2,3,4,5,6,7])){
                        return $powerful;
                    } else {
                        return "";
                    }

                default:
                    return $powerful;
            }
        }

        public function getVisual(int $display = Content::DISPLAY_CARD, int $size = 300){

            $user = ControllerConnect::getCurrentUser();
            $bookmark_icon = "far";
            if($user->in_bookmark($this)){
                $bookmark_icon = "fas";
            }

            //OPTIONS
                if($size < 100){$size = 300;}

            switch ($display) {
                case Content::DISPLAY_MODIFY:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-auto">
                                    <?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-200")?>
                                    <div class="text-center mt-2">
                                        <?=$this->getPowerful(Content::FORMAT_BADGE)?>
                                        <?=$this->getHostility(Content::FORMAT_MODIFY)?>
                                    </div>
                                </div>
                                <div class="col">
                                        <div class="d-flex justify-content-between">
                                            <a class="text-grey-d-3" data-bs-toggle="collapse" href="#collapse<?=$this->getUniqid()?>" role="button" aria-expanded="false" aria-controls="collapseExample">Comment calculer les caractéristiques ?</a>
                                            <?=$this->getUsable(Content::FORMAT_MODIFY)?>
                                        </div>
                                        <div class="collapse mb-2 size-0-9 text-grey-d-1" id="collapse<?=$this->getUniqid()?>">
                                            <p>
                                                Certaines caractéristiques découlent des autres caractéristiques notamment du niveau de la créature.<br>
                                                Pour les calculer, les formules sont indiqués sous la forme suivant [n * + / ou - caractéristique].
                                            </p>
                                            <ul class="size-0-9 text-grey-d-1">
                                                <li>n est nombre entier</li>
                                                <li>L'opérateur est * ou / pour les mutiplications ou divisions et + ou - pour les additions ou soustractions</li>
                                                <li>La caractéristique fait référence à la valeur d'une autre caractéristique.</li>
                                            </ul>
                                            <p>
                                                Lorsque le résultat n'est pas un nombre entier, il faut troncaturer le résultat, c'est à dire arrondir à l'inférieur.
                                                Par exemple, si pour les PM de la créature la formule est [niveau / 3] et le niveau est 11, alors le résultat sera 11/3 = 3,66, soit 3 PM.
                                            </p>
                                        </div>

                                        <div class="row">
                                            <div class="col-auto">
                                                <?=$this->getLevel(Content::FORMAT_MODIFY)?>
                                                <?=$this->getIni(Content::FORMAT_MODIFY)?>
                                                <?=$this->getLife(Content::FORMAT_MODIFY)?>
                                                <?=$this->getPa(Content::FORMAT_MODIFY)?>
                                                <?=$this->getPm(Content::FORMAT_MODIFY)?>
                                                <?=$this->getPo(Content::FORMAT_MODIFY)?>
                                                <?=$this->getTouch(Content::FORMAT_MODIFY)?>
                                            </div>  
                                            <div class="col-auto">
                                                <?=$this->getVitality(Content::FORMAT_MODIFY);?>
                                                <?=$this->getSagesse(Content::FORMAT_MODIFY);?>
                                                <?=$this->getStrong(Content::FORMAT_MODIFY);?>
                                                <?=$this->getIntel(Content::FORMAT_MODIFY);?>
                                                <?=$this->getAgi(Content::FORMAT_MODIFY);?>
                                                <?=$this->getChance(Content::FORMAT_MODIFY);?>
                                            </div>   
                                            <div class="col-auto">
                                                <?=$this->getCa(Content::FORMAT_MODIFY);?>
                                                <?=$this->getFuite(Content::FORMAT_MODIFY);?>
                                                <?=$this->getTacle(Content::FORMAT_MODIFY);?>
                                                <?=$this->getDodge_pa(Content::FORMAT_MODIFY);?>
                                                <?=$this->getDodge_pm(Content::FORMAT_MODIFY);?>
                                            </div> 
                                            <div class="col-auto">
                                                <?=$this->getRes_neutre(Content::FORMAT_MODIFY);?>
                                                <?=$this->getRes_terre(Content::FORMAT_MODIFY);?>
                                                <?=$this->getRes_feu(Content::FORMAT_MODIFY);?>
                                                <?=$this->getRes_air(Content::FORMAT_MODIFY);?>
                                                <?=$this->getRes_eau(Content::FORMAT_MODIFY);?>
                                            </div>            
                                        </div>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                        <p class='size-0-7 mb-1'>Mob <?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$this->getTimestamp_updated(Content::DATE_FR);?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-text my-2"><?=$this->getTrait(Content::FORMAT_MODIFY);?></div>
                            <div class="card-text my-2"><?=$this->getDescription(Content::FORMAT_MODIFY);?></div>
                            <div class="card-text my-2"><?=$this->getZone(Content::FORMAT_MODIFY);?></div>
                            <div class="card-text my-2"><?=$this->getSpell(Content::FORMAT_MODIFY)?></div>
                            <div class="text-right font-size-0-8 m-1"><a class='text-red-d-2 text-red-l-3-hover' onclick="Mob.remove('<?=$this->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
                        </div>
                    <?php return ob_get_clean();
                break;

                case Content::DISPLAY_RESUME:
                    ob_start(); ?>
                        <div style="width: <?=$size?>px;">
                            <div style="position:relative;">
                                <div ondblclick="Mob.open('<?=$this->getUniqid()?>');" class="card-hover-linked card border-secondary-d-2 border p-2 m-1" style="width: <?=$size?>px;" >
                                    <div class="d-flex flew-row flex-nowrap justify-content-start">
                                        <div>
                                            <?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-50")?>
                                        </div>
                                        <div class="m-1 p-0">
                                            <p class="bold ms-1"><?=$this->getName()?></p>
                                            <div class="d-flex flex-wrap justify-content-around align-items-start">
                                                <p class="mt-1 text-level short-badge-150"><?=$this->getLevel(Content::FORMAT_BADGE)?></p> 
                                                <div> <?=$this->getPowerful(Content::FORMAT_ICON)?></div>
                                                <p class="short-badge-100"><?=$this->getHostility(Content::FORMAT_BADGE)?></p>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-between ms-auto">
                                            <a onclick='User.changeBookmark(this);' data-classe='mob' data-uniqid='<?=$this->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                                            <p class="align-self-end"><a class="btn-text-secondary" title="Afficher les sorts" onclick="Mob.getSpellList('<?=$this->getUniqid()?>');"><i class="fas fa-magic"></i></a></p>
                                        </div>
                                    </div>
                                    <div class="justify-content-center flex-wrap d-flex short-badge-150"><?=$this->getTrait(Content::FORMAT_BADGE)?></div>
                                    <div class="card-hover-showed">
                                        <div class="d-flex justify-content-around flex-wrap">
                                            <div class="col-auto">
                                                <div><?=$this->getPa(Content::FORMAT_ICON)?></div>
                                                <div><?=$this->getPm(Content::FORMAT_ICON)?></div>
                                                <div><?=$this->getPo(Content::FORMAT_ICON)?></div>
                                                <div><?=$this->getIni(Content::FORMAT_ICON)?></div>
                                                <div><?=$this->getLife(Content::FORMAT_ICON)?></div>
                                                <div><?=$this->getTouch(Content::FORMAT_ICON)?></div>
                                            </div>
                                            <div class="col-auto">
                                                <div><?=$this->getVitality(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getSagesse(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getStrong(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getIntel(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getAgi(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getChance(Content::FORMAT_ICON);?></div>
                                            </div>
                                            <div class="col-auto">
                                                <div><?=$this->getCa(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getFuite(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getTacle(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getDodge_pa(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getDodge_pm(Content::FORMAT_ICON);?></div>
                                            </div> 
                                            <div class="col-auto">
                                                <div><?=$this->getRes_neutre(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getRes_terre(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getRes_feu(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getRes_air(Content::FORMAT_ICON);?></div>
                                                <div><?=$this->getRes_eau(Content::FORMAT_ICON);?></div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                break;

                case  Content::DISPLAY_CARD:      
                    ob_start(); ?>
                        <div class="card mb-3" id="mob<?=$this->getUniqid()?>">
                            <div class="row g-0">
                                <div class="col-auto">
                                    <a style="position:relative;top:5px;left:5px;" href="<?=$this->getPath_img()?>" download="<?=$this->getName().'.'.substr(strrchr($this->getPath_img(),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
                                    <?=$this->getPath_img(Content::FORMAT_FANCY, "img-back-200")?>
                                </div>
                                <div class="col">
                                    <div class="card-body">
                                        <div class="row justify-content-between">
                                            <div class="col-auto">
                                                <div><?=$this->getLevel(Content::FORMAT_LIST)?></div>
                                                <div><?=$this->getLife(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getTouch(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getHostility(Content::FORMAT_BADGE)?></div>
                                                <div> <?=$this->getPowerful(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getTrait(Content::FORMAT_BADGE);?></div>
                                            </div>
                                            <div class="col-auto">
                                                <div><?=$this->getPa(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getPm(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getPo(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getIni(Content::FORMAT_BADGE)?></div>
                                            </div>
                                            <div class="col-auto">
                                                <div><?=$this->getVitality(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getSagesse(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getStrong(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getIntel(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getAgi(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getChance(Content::FORMAT_BADGE);?></div>
                                            </div>
                                            <div class="col-auto">
                                                <div><?=$this->getCa(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getFuite(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getTacle(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getDodge_pa(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getDodge_pm(Content::FORMAT_BADGE);?></div>
                                            </div> 
                                            <div class="col-auto">
                                                <div><?=$this->getRes_neutre(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getRes_terre(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getRes_feu(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getRes_air(Content::FORMAT_BADGE);?></div>
                                                <div><?=$this->getRes_eau(Content::FORMAT_BADGE);?></div>
                                            </div>                
                                        </div>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                        <div class="d-flex justify-content-between">
                                            <h3><?=$this->getName()?></h3>
                                            <div>
                                                <?=$this->getUsable(Content::FORMAT_BADGE)?>
                                                <?php if($user->getRight('mob', User::RIGHT_WRITE)){ ?>
                                                    <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Mob.open('<?=$this->getUniqid()?>', Controller.DISPLAY_MODIFY);"><i class='far fa-edit'></i></a>
                                                <?php } ?>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mx-3">
                                <p class="card-text my-2"><?=$this->getDescription()?></p>
                                <p class="card-text my-2"><small class="text-muted">Zone : <?=$this->getZone(Content::FORMAT_TEXT)?></small></p>
                                <p class="card-text my-2"><?=$this->getSpell(Content::DISPLAY_RESUME)?></p>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                break;

                default:
                    return "Erreur : format de display non reconnu";
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName($data){
            $this->_name = $data;
            return true;
        }
        public function setDescription($data){
            $this->_description = $data;
            return true;
        }
        public function setLevel($data){
            $this->_level = $data;
            return true;
        }
        public function setVitality($data){
            $this->_vitality = $data;
            return true;
        }
        public function setPa($data){
            $this->_pa = $data;
            return true;
        }
        public function setPm($data){
            $this->_pm = $data;
            return true;
        }
        public function setPo($data){
            $this->_po = $data;
            return true;
        }
        public function setIni($data){
            $this->_ini = $data;
            return true;
        }
        public function setTouch($data){
            if(empty($data)){
                $this->_touch = 0;
            } else {
                $this->_touch = $data;
            }
            return true;
        }
        public function setLife($data){
            $this->_life = $data;
            return true;
        }
        public function setSagesse($data){
            $this->_sagesse = $data;
            return true;
        }
        public function setStrong($data){
            $this->_strong = $data;
            return true;
        }
        public function setIntel($data){
            $this->_intel = $data;
            return true;
        }
        public function setAgi($data){
            $this->_agi = $data;
            return true;
        }
        public function setChance($data){
            $this->_chance = $data;
            return true;
        }
        public function setCa($data){
            $this->_ca = $data;
            return true;
        }
        public function setFuite($data){
            $this->_fuite = $data;
            return true;
        }
        public function setTacle($data){
            $this->_tacle = $data;
            return true;
        }
        public function setDodge_pa($data){
            $this->_dodge_pa = $data;
            return true;
        }
        public function setDodge_pm($data){
            $this->_dodge_pm = $data;
            return true;
        }
        public function setRes_neutre($data){
            $this->_res_neutre = $data;
            return true;
        }
        public function setRes_terre($data){
            $this->_res_terre = $data;
            return true;
        }
        public function setRes_feu($data){
            $this->_res_feu = $data;
            return true;
        }
        public function setRes_air($data){
            $this->_res_air = $data;
            return true;
        }
        public function setRes_eau($data){
            $this->_res_eau = $data;
            return true;
        }
        public function setZone($data){
            $this->_zone = $data;
            return true;
        }
        public function setHostility($data){
            if(in_array($data, Mob::HOSTILITY)){
                $this->_hostility = $data;
                return true;
            } else {
                $this->_hostility = Mob::HOSTILITY["neutre"];
                return "Erreur : donnée non valide;";
            }
        }
        public function setTrait($data){
            $this->_trait = $data;
            return true;
        }
        public function setPath_image($data){
            if(is_file($data)){
                $file = New File($data);
                if(FileManager::isImage($file)){
                    $this->_path_img = $data;
                    return true;
                } else {
                    return "Le fichier doit être une image.";
                }
            } else {
                return "Le fichier n'est pas valide.";
            }
        }
        public function setUsable($data){
            $this->_usable = $this->returnBool($data);
            return true;
        }

        /* Data = array(
                        uniqid => id du spell
                    )
            Js : Mob.update(UniqidM,{action:'add|remove|update', uniqid:'uniqIdS'},'spell', IS_VALUE);
        */
        public function setSpell(array $data){ 
            $managerM = new MobManager;
            $managerS = new SpellManager;
            if(!isset($data['uniqid'])){return "L'uniqid du sort n'est pas défini";}
            if($managerS->existsUniqid($data['uniqid'])){
                $spell = $managerS->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            return $managerM->addLinkSpell($this, $spell);
               
                        case "remove":
                            return $managerM->removeLinkSpell($this, $spell);

                        default:
                            return "L'action n'est pas valide";
                    }

                } else {
                    return "Une action est requise.";
                }

            }
        }
}