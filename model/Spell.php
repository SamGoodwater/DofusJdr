<?php
class Spell extends Content
{
    public function __construct(array $donnees){
        $this->hydrate($donnees);
        if(file_exists("medias/spells/".$this->getUniqid().".svg")){
            $this->setPath_image("medias/spells/".$this->getUniqid().".svg");
        }
    }

    const ELEMENT_NEUTRE = 0;
    const ELEMENT_VITALITY = 1;
    const ELEMENT_SAGESSE = 2;
    const ELEMENT_TERRE = 3;
    const ELEMENT_FEU = 4;
    const ELEMENT_AIR = 5;
    const ELEMENT_EAU = 6;

    const ELEMENT_TERRE_FEU = 7;
    const ELEMENT_TERRE_AIR = 8;
    const ELEMENT_TERRE_EAU = 9;

    const ELEMENT_FEU_AIR= 10;
    const ELEMENT_FEU_EAU= 11;

    const ELEMENT_AIR_EAU= 12;

    const ELEMENT_TERRE_FEU_AIR= 13;
    const ELEMENT_TERRE_FEU_EAU= 14;
    const ELEMENT_TERRE_AIR_EAU= 15;
    const ELEMENT_FEU_AIR_EAU= 16;

    const ELEMENT_TERRE_FEU_AIR_EAU= 17;

    const ELEMENT = [
        "neutre" => self::ELEMENT_NEUTRE,
        "vitalité" => self::ELEMENT_VITALITY,
        "sagesse" => self::ELEMENT_SAGESSE,
        "terre" => self::ELEMENT_TERRE,
        "feu" => self::ELEMENT_FEU,
        "air" => self::ELEMENT_AIR,
        "eau" => self::ELEMENT_EAU,
        "terre-feu" => self::ELEMENT_TERRE_FEU,
        "terre-air" => self::ELEMENT_TERRE_AIR,
        "terre-eau" => self::ELEMENT_TERRE_EAU,
        "feu-air" => self::ELEMENT_FEU_AIR,
        "feu-eau" => self::ELEMENT_FEU_EAU,
        "air-eau" => self::ELEMENT_AIR_EAU,
        "terre-feu-air" => self::ELEMENT_TERRE_FEU_AIR,
        "terre-feu-eau" => self::ELEMENT_TERRE_FEU_EAU,
        "terre-air-eau" => self::ELEMENT_TERRE_AIR_EAU,
        "feu-air-eau" => self::ELEMENT_FEU_AIR_EAU,
        "terre-feu-air-eau" => self::ELEMENT_TERRE_FEU_AIR_EAU
    ];

    const TYPE_DAMAGE = 0;
    const TYPE_PROTECT = 1;
    const TYPE_BUFF = 2;
    const TYPE_DEBUFF = 3;
    const TYPE_INVOCATION = 4;   
    const TYPE_PLACEMENT = 5;   
    const TYPE_MANIPULATION = 6;   
    const TYPE_TRANSFORMATION = 7;   

    const TYPE = [
        "Dommage" => self::TYPE_DAMAGE,
        "Protection" => self::TYPE_PROTECT,
        "Boost" => self::TYPE_BUFF,
        "Retrait" => self::TYPE_DEBUFF,
        "Invocation" => self::TYPE_INVOCATION,
        "Placement" => self::TYPE_PLACEMENT,
        "Manipulation" => self::TYPE_MANIPULATION,
        "Transformation" => self::TYPE_TRANSFORMATION
    ];

    const CATEGORY_CLASS = 1;
    const CATEGORY_MOB = 0;
    const CATEGORY_LEARNABLE = 2;
    const CATEGORY_CONSUMABLE = 3;

    const CATEGORY = [
        "Sort de classe" => self::CATEGORY_CLASS,
        "Sort de créature" => self::CATEGORY_MOB,
        "Sort apprenable" => self::CATEGORY_LEARNABLE,
        "Sort consommable" => self::CATEGORY_CONSUMABLE
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_description='';
        private $_effect="";
        private $_level=1;
        private $_po=1;
        private $_po_editable=true;
        private $_pa=1;
        private $_cast_per_turn=1;
        private $_sight_line=false;
        private $_number_between_two_cast=0;
        private $_element = Spell::ELEMENT_NEUTRE;
        private $_category = Spell::CATEGORY_MOB;
        private $_id_invocation = "";
        private $_is_magic = true;
        private $_powerful = 1;
        private $_path_img="medias/spells/default.svg";
        private $_usable=false;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <input 
                                onchange="Spell.update('<?=$this->getUniqid();?>', this, 'name');" 
                                placeholder="Nom du sort" 
                                maxlength="300" 
                                type="text" 
                                class="form-control" 
                                value="<?=$this->_name?>">
                            <label class="size-0-8">Nom du sort</label>
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
                        <div class="mb-1">
                            <p>Description</p>
                            <div  id="description<?=$this->getUniqid()?>"><?=html_entity_decode($this->_description)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Spell.update('<?=$this->getUniqid()?>', CKEDITOR5['description<?=$this->getUniqid()?>'].getData(), 'description', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#description<?=$this->getUniqid()?>'), { 
                                    toolbar: {
                                        items: [
                                            'undo',
                                            'redo',
                                            '|',
                                            'heading',
                                            'alignment',
                                            'fontSize',
                                            'fontFamily',
                                            '|',
                                            'fontColor',
                                            'fontBackgroundColor',
                                            'highlight',
                                            '|',
                                            'link',
                                            'insertTable',
                                            'imageInsert',
                                            '|',
                                            'bold',
                                            'italic',
                                            'strikethrough',
                                            'underline',
                                            'subscript',
                                            'superscript',
                                            '|',
                                            'bulletedList',
                                            'numberedList',
                                            'todoList',
                                            '|',
                                            'outdent',
                                            'indent',
                                            '|',
                                            'specialCharacters',
                                            'imageUpload',
                                            '|',
                                            'mediaEmbed',
                                            'horizontalLine',
                                            'blockQuote',
                                            '|',
                                            'removeFormat',
                                            'htmlEmbed',
                                            'code',
                                            'sourceEditing',
                                            'findAndReplace'
                                        ]
                                    },
                                    language: 'fr',
                                    image: {
                                        toolbar: [
                                            'imageTextAlternative',
                                            'imageStyle:inline',
                                            'imageStyle:block',
                                            'imageStyle:side',
                                            'linkImage'
                                        ]
                                    },
                                    table: {
                                        contentToolbar: [
                                            'tableColumn',
                                            'tableRow',
                                            'mergeTableCells',
                                            'tableCellProperties',
                                            'tableProperties'
                                        ]
                                    },
                                    licenseKey: '',
                                } )
                                .then( newEditor => {
                                    CKEDITOR5['description<?=$this->getUniqid()?>'] = newEditor;
                                    $(".ck-file-dialog-button button").off("click");
                                    $(".ck-file-dialog-button button").unbind('click');
                                } )
                                .catch( error => {
                                    console.error( 'Oops, something went wrong!' );
                                    console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                                    console.warn( 'Build id: 2jnb9i33ls8a-f2lnu5o5jd3g' );
                                    console.error( error );
                                } );
                        </script>
                    <?php return ob_get_clean();

                default:
                    return html_entity_decode($this->_description);
            }
        }
        public function getEffect(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1">
                            <p>Effets</p>
                            <div  id="effect<?=$this->getUniqid()?>"><?=html_entity_decode($this->_effect)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Spell.update('<?=$this->getUniqid()?>', CKEDITOR5['effect<?=$this->getUniqid()?>'].getData(), 'effect', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#effect<?=$this->getUniqid()?>'), { 
                                    toolbar: {
                                        items: [
                                            'undo',
                                            'redo',
                                            '|',
                                            'heading',
                                            'alignment',
                                            'fontSize',
                                            'fontFamily',
                                            '|',
                                            'fontColor',
                                            'fontBackgroundColor',
                                            'highlight',
                                            '|',
                                            'link',
                                            'insertTable',
                                            'imageInsert',
                                            '|',
                                            'bold',
                                            'italic',
                                            'strikethrough',
                                            'underline',
                                            'subscript',
                                            'superscript',
                                            '|',
                                            'bulletedList',
                                            'numberedList',
                                            'todoList',
                                            '|',
                                            'outdent',
                                            'indent',
                                            '|',
                                            'specialCharacters',
                                            'imageUpload',
                                            '|',
                                            'mediaEmbed',
                                            'horizontalLine',
                                            'blockQuote',
                                            '|',
                                            'removeFormat',
                                            'htmlEmbed',
                                            'code',
                                            'sourceEditing',
                                            'findAndReplace'
                                        ]
                                    },
                                    language: 'fr',
                                    image: {
                                        toolbar: [
                                            'imageTextAlternative',
                                            'imageStyle:inline',
                                            'imageStyle:block',
                                            'imageStyle:side',
                                            'linkImage'
                                        ]
                                    },
                                    table: {
                                        contentToolbar: [
                                            'tableColumn',
                                            'tableRow',
                                            'mergeTableCells',
                                            'tableCellProperties',
                                            'tableProperties'
                                        ]
                                    },
                                    licenseKey: '',
                                } )
                                .then( newEditor => {
                                    CKEDITOR5['effect<?=$this->getUniqid()?>'] = newEditor;
                                    $(".ck-file-dialog-button button").off("click");
                                    $(".ck-file-dialog-button button").unbind('click');
                                } )
                                .catch( error => {
                                    console.error( 'Oops, something went wrong!' );
                                    console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                                    console.warn( 'Build id: 2jnb9i33ls8a-f2lnu5o5jd3g' );
                                    console.error( error );
                                } );
                        </script>
                    <?php return ob_get_clean();

                default:
                    return html_entity_decode($this->_effect);
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-<?=View::getColorFromLetter($this->_level, true)?>-d-3">
                            <label>Niveau du sort</label>
                            <input 
                                onchange="Spell.update('<?=$this->getUniqid();?>', this, 'level');" 
                                data-bs-toggle='tooltip' data-bs-placement='bottom' title="Niveau à partir duquel il est possible d'apprendre le sort"
                                min="0" max="200" 
                                type="number" 
                                class="form-control form-control-sm" 
                                value="<?=$this->_level?>">
                            </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-".View::getColorFromLetter($this->_level, true)."-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Niveau à partir duquel il est possible d'apprendre le sort'>Niveau {$this->_level}</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-".View::getColorFromLetter($this->_level, true)."-d-3 badge-outline border border-".View::getColorFromLetter($this->_level, true)."-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Niveau à partir duquel il est possible d'apprendre le sort\">Niv. {$this->_level}</span>";
                
                default:
                    return $this->_level;
            }
        }
        public function getPo(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-po-d-2">
                            <label>Portée du sort</label>
                            <div class="input-group">
                                <div class="input-group-text back-po text-white"><img class="icon" src="medias/icons/po.png"></div>
                                <input 
                                    onchange="Spell.update('<?=$this->getUniqid();?>', this, 'po');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title='Portée du sort (en case)'
                                    type="text" 
                                    class="form-control form-control-sm" 
                                    value="<?=$this->_po?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    return "<span class='badge back-po-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Portée du sort'>{$this->_po} PO</span>";
                    
                case Content::FORMAT_ICON:
                    return "<span class='text-po-d-2 badge back-white border border-po-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Portée du sort\">{$this->_po} <img class='icon' src='medias/icons/po.png'></span>";

                default:
                    return $this->_po;
            }
        }
        public function getPo_editable(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    $checked = ""; 
                    if($this->_po_editable){ $checked = "checked"; } else { $checked = ""; }
                    ob_start(); ?>
                        <div class="form-check form-switch my-1">
                            <input onchange="Spell.update('<?=$this->getUniqid();?>', this, 'po_editable', <?=Controller::IS_CHECKBOX?>);"  type="checkbox" class="form-check-input back-po-editable border-po-editable" <?=$checked?> id="customSwitchVisible<?=$this->getId()?>">
                            <label class="custom-control-label" for="customSwitchVisible<?=$this->getUniqid()?>">Portée modifiable | actuel : <?=$this->getPo_editable(Content::FORMAT_BADGE)?></label>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($this->_po_editable){ 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title='Portée modificable' class='badge back-po-editable-d-2'>PO modifiable</span>";
                    } elseif ($this->_po_editable == false && $this->getPO() == "1"){ 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title='Sort seulement au corps à corps' class='badge back-po-editable-d-2'>Corps à Corps</span>";
                    } else { 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title='Portée non modifiable' class='badge back-po-editable-d-2'>PO non modifiable</span>";
                    }

                case Content::FORMAT_ICON:
                    if($this->_po_editable){ 
                        return "<img data-bs-toggle='tooltip' data-bs-placement='top' title='Portée modifiable' class='icon-lg' src='medias/icons/po_editable.png'>";
                    } elseif ($this->_po_editable == false && $this->getPO() == "1"){ 
                        return "<img data-bs-toggle='tooltip' data-bs-placement='top' title='Sort seulement au corps à corps' class='icon-lg' src='medias/icons/cac.png'>";
                    } else { 
                        return "<img data-bs-toggle='tooltip' data-bs-placement='top' title='Portée non modifiable' class='icon-lg' src='medias/icons/po_no_editable.png'>";   
                    }

                case Content::FORMAT_PATH:
                    if($this->_po_editable){ 
                        return "/medias/icons/po_editable.png";
                    } elseif ($this->_po_editable == false && $this->getPO() == "1"){ 
                        return "/medias/icons/cac.png";
                    } else { 
                        return "/medias/icons/po_no_editable.png";   
                    }
                    
                default:
                    return $this->_po_editable;
            }
        }
        public function getPa(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-pa-d-2">
                            <label>Coût en point d'action du sort</label>
                            <div class="input-group">
                                <div class="input-group-text back-pa text-white"><img class='icon' src='medias/icons/pa.png'></div>
                                <input 
                                    onchange="Spell.update('<?=$this->getUniqid();?>', this, 'pa');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Coût en point d'action du sort" 
                                    type="text" 
                                    class="form-control form-control-sm" 
                                    value="<?=$this->_pa?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                    case Content::FORMAT_BADGE:
                        return "<span class='badge back-pa-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Coût en point d'action du sort'>{$this->_pa} PA</span>";
                        
                    case Content::FORMAT_ICON:
                        ob_start();?>
                            <div class="text-under-img" data-bs-toggle='tooltip' data-bs-placement='bottom' title="Coût en point d'action du sort">
                                <img class='icon-xl' src='medias/icons/pa.png'>
                                <p class='text-pa-d-5 bold size-1-2'><?=$this->_pa?></p>
                            </div>
                        <?php return ob_get_clean();
                                
                default:
                    return $this->_pa;
            }
        }
        public function getCast_per_turn(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-cast-per-turn-d-2">
                            <label>Nombre de lancer par tour</label>
                            <div class="input-group">
                                <div class="input-group-text back-cast-per-turn text-white"><img class='icon' src='medias/icons/cast_per_turn.png'></div>
                                <input 
                                    onchange="Spell.update('<?=$this->getUniqid();?>', this, 'cast_per_turn');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de fois que le sort peut-être lancer par tour"
                                    type="text" 
                                    class="form-control form-control-sm" 
                                    value="<?=$this->_cast_per_turn?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();

                    case Content::FORMAT_BADGE:
                        return "<span class='badge back-cast-per-turn-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Nombre de fois que le sort peut-être lancer par tour'>{$this->_cast_per_turn} fois / tour</span>";
                        
                    case Content::FORMAT_ICON:
                        return "<span class='text-cast-per-turn-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Nombre de fois que le sort peut-être lancer par tour\">{$this->_cast_per_turn} <img class='icon' src='medias/icons/cast_per_turn.png'></span>";
                                        
                default:
                    return $this->_cast_per_turn;
            }
        }
        public function getSight_line(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    $checked = ""; 
                    if($this->_sight_line){ $checked = "checked"; } else { $checked = ""; }
                    ob_start(); ?>
                        <div class="form-check form-switch my-1">
                            <input onchange="Spell.update('<?=$this->getUniqid();?>', this, 'sight_line', <?=Controller::IS_CHECKBOX?>);"  type="checkbox" class="form-check-input back-sight-line border-sight-line" <?=$checked?> id="customSwitchVisible<?=$this->getId()?>">
                            <label class="custom-control-label" for="customSwitchVisible<?=$this->getUniqid()?>"><?=$this->getSight_line(Content::FORMAT_BADGE)?></label>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($this->_sight_line){ 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title='Ligne de vue obligatoire' class='badge back-sight-line-d-2'>Ligne de vue</span>";
                    } else { 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title='Pas besoin de ligne de vue pour lancer le sort' class='badge back-sight-line-d-2'>Pas de ligne de vue</span>";
                    }

                case Content::FORMAT_ICON:
                    if($this->_sight_line){ 
                        return "<img data-bs-toggle='tooltip' data-bs-placement='top' title='Ligne de vue obligatoire' class='icon-lg' src='medias/icons/sight_line.png'>";
                    } else { 
                        return "<img  data-bs-toggle='tooltip' data-bs-placement='top' title='Pas besoin de ligne de vue pour lancer le sort' class='icon-lg' src='medias/icons/no_sight_line.png'>";
                    }

                case Content::FORMAT_PATH:
                    if($this->_sight_line){ 
                        return "/medias/icons/sight_line.png";
                    } else { 
                        return "/medias/icons/no_sight_line.png";
                    }
                    
                default:
                    return $this->_sight_line;
            }
        }
        public function getNumber_between_two_cast(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-number-between-two-cast-d-2">
                            <label>Nombre de tour entre deux lancer de sort</label>
                            <div class="input-group">
                                <div class="input-group-text back-number-between-two-cast text-white"><img class='icon' src='medias/icons/number_between_two_cast.png'></div>
                                <input 
                                    onchange="Spell.update('<?=$this->getUniqid();?>', this, 'number_between_two_cast');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de tour entre deux lancer de sort" 
                                    type="text" 
                                    class="form-control form-control-sm" 
                                    value="<?=$this->_number_between_two_cast?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    return "<span class='badge back-number-between-two-cast-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Nombre de tour entre deux lancer de sort'>1 fois / {$this->_number_between_two_cast} tour(s)</span>";
                    
                case Content::FORMAT_ICON:
                    return "<span class='text-number-between-two-cast-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Nombre de tour entre deux lancer de sort\">{$this->_number_between_two_cast} <img class='icon' src='medias/icons/number_between_two_cast.png'></span>";
                                        
                default:
                    return $this->_number_between_two_cast;
            }
        }
        public function getElement(int $format = Content::FORMAT_BRUT, $option = ""){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="dropdown">
                            <a type="button" id="dropdownDisplay<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getElement(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="dropdownDisplay<?=$this->getId()?>">
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_NEUTRE?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-neutre'>Neutre</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_VITALITY?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-vitality'>Vitalité</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_SAGESSE?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-sagesse'>Sagesse</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_TERRE?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-terre'>Terre</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_FEU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-feu'>Feu</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_AIR?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-air'>Air</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_EAU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-eau'>Eau</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_TERRE_FEU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-terre-feu'>Terre & Feu</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_TERRE_AIR?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-terre-air'>Terre & Air</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_TERRE_EAU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-terre-eau'>Terre & Eau</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_FEU_AIR?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-feu-air'>Feu & Air</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_FEU_EAU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-feu-eau'>Feu & Eau</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_AIR_EAU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-feu-eau'>Air & Eau</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_TERRE_FEU_AIR?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-terre-feu-air'>Terre & Feu & Air</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_TERRE_FEU_EAU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-terre-feu-eau'>Terre & Feu & Eau</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_TERRE_AIR_EAU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-terre-air-eau'>Terre & Air & Eau</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_FEU_AIR_EAU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-feu-air-eau'>Feu & Air & Eau</span></a>
                                <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=Spell::ELEMENT_TERRE_FEU_AIR_EAU?>, 'element', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-terre-feu-air-eau'>Terre & Feu & Air & Eau</span></a>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    switch ($this->_element) {
                        case Spell::ELEMENT_NEUTRE:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise cet élèment pour être lancer et pour ces effets.' class='badge back-neutre'>Neutre</span>";
                        case Spell::ELEMENT_VITALITY:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise cet élèment pour être lancer et pour ces effets.' class='badge back-vitality'>Vitalité</span>";
                        case Spell::ELEMENT_SAGESSE:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise cet élèment pour être lancer et pour ces effets.' class='badge back-sagesse'>Sagesse</span>";
                        case Spell::ELEMENT_TERRE:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise cet élèment pour être lancer et pour ces effets.' class='badge back-terre'>Terre</span>";
                        case Spell::ELEMENT_FEU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise cet élèment pour être lancer et pour ces effets.' class='badge back-feu'>Feu</span>";
                        case Spell::ELEMENT_AIR:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise cet élèment pour être lancer et pour ces effets.' class='badge back-air'>Air</span>";
                        case Spell::ELEMENT_EAU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise cet élèment pour être lancer et pour ces effets.' class='badge back-eau'>Eau</span>";
                        case Spell::ELEMENT_TERRE_FEU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-terre-feu'>Terre & Feu</span>";
                        case Spell::ELEMENT_TERRE_AIR:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-terre-air'>Terre & Air</span>";
                        case Spell::ELEMENT_TERRE_EAU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-terre-eau'>Terre & Eau</span>";
                        case Spell::ELEMENT_FEU_AIR:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-feu-air'>Feu & Air</span>";
                        case Spell::ELEMENT_FEU_EAU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-feu-eau'>Feu & Eau</span>";
                        case Spell::ELEMENT_AIR_EAU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-air-eau'>Air & Eau</span>"; 
                        case Spell::ELEMENT_TERRE_FEU_AIR:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-terre-feu-air'>Terre & Feu & Air</span>";
                        case Spell::ELEMENT_TERRE_FEU_EAU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-terre-feu-eau'>Terre & Feu & Eau</span>";
                        case Spell::ELEMENT_TERRE_AIR_EAU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-terre-air-eau'>Terre & Air & Eau</span>";
                        case Spell::ELEMENT_FEU_AIR_EAU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-feu-air-eau'>Feu & Air & Eau</span>";
                        case Spell::ELEMENT_TERRE_FEU_AIR_EAU:
                            return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='Le sort utilise ces élèments pour être lancer et pour ces effets.' class='badge back-terre-feu-air-eau'>Terre & Feu & Air & Eau</span>";        
                        default:
                            return "";
                    }

                case Content::FORMAT_COLOR_VERBALE:
                    if($option == "_"){
                        $type = "";
                    } else {
                        $type = "text-"; if(!empty($option)){$type = $option . "-";}
                    }
                    switch ($this->_element) {
                        case Spell::ELEMENT_NEUTRE:
                            return $type."neutre";
                        case Spell::ELEMENT_VITALITY:
                            return $type."vitality";
                        case Spell::ELEMENT_SAGESSE:
                            return $type."sagesse";
                        case Spell::ELEMENT_TERRE:
                            return $type."terre";
                        case Spell::ELEMENT_FEU:
                            return $type."feu";
                        case Spell::ELEMENT_AIR:
                            return $type."air";
                        case Spell::ELEMENT_EAU:
                            return $type."eau";
                        case Spell::ELEMENT_TERRE_FEU:
                            return $type."terre-feu";
                        case Spell::ELEMENT_TERRE_AIR:
                            return $type."terre-air";
                        case Spell::ELEMENT_TERRE_EAU:
                            return $type."terre-eau";
                        case Spell::ELEMENT_FEU_AIR:
                            return $type."feu-air";
                        case Spell::ELEMENT_FEU_EAU:
                            return $type."feu-eau";
                        case Spell::ELEMENT_AIR_EAU:
                            return $type."air-eau"; 
                        case Spell::ELEMENT_TERRE_FEU_AIR:
                            return $type."terre-feu-air";
                        case Spell::ELEMENT_TERRE_FEU_EAU:
                            return $type."terre-feu-eau";
                        case Spell::ELEMENT_TERRE_AIR_EAU:
                            return $type."terre-air-eau";
                        case Spell::ELEMENT_FEU_AIR_EAU:
                            return $type."feu-air-eau";
                        case Spell::ELEMENT_TERRE_FEU_AIR_EAU:
                            return $type."terre-feu-air-eau";        
                        default:
                            return "";
                    }

                default:
                    return $this->_element;
            }
        }
        public function getCategory(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="dropdown">
                            <a type="button" id="dropdownCategory<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getCategory(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="dropdownCategory<?=$this->getId()?>">
                                <?php foreach (Self::CATEGORY as $key => $category) { ?>
                                    <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=$category?>, 'category', <?=Controller::IS_VALUE?>);$('#dropdownCategory<?=$this->getId()?>').html($(this).html());"><span class='badge back-<?=View::getColorFromLetter($category)?>'><?=$key?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    if(in_array($this->_category, Self::CATEGORY)){
                        return "<span class='badge back-".View::getColorFromLetter($this->_category)."'>".array_search($this->_category, self::CATEGORY)."</span>";
                    } else {
                        return "";
                    }

                default:
                    return $this->_category;
            }
        }
        public function getId_invocation(int $format = Content::FORMAT_BRUT){
            $manager = new MobManager;
            if($manager->existsId($this->_id_invocation)){
                $mob = $manager->getFromId($this->_id_invocation);
            }

            switch ($format) { 
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <?php if(!empty($this->_id_invocation)){ ?>
                            <div style="position:relative;width: 300px;">
                                <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                                    <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher cette créature de ce sort" class="p-4 <?=View::getCss(View::TYPE_BTN_UNDERLINE, "red")?>" onclick="if (confirm('Etes vous sûr d\'étacher la créature de ce sort ?')){Spell.update('<?=$this->getUniqid();?>', 0, 'id_invocation', IS_VALUE);}"><i class="fas fa-times"></i></a>
                                </div>
                                <?=$this->getId_invocation(Content::FORMAT_RESUME)?>
                            </div>
                        <?php } ?>
                        <h6 class="mt-1">Attacher une invocation au sort</h6>
                        <div class="d-flex flex-row justify-content-evenly align-items-baseline mb-3 position-relative">
                            <div class="form-floating w-100">
                                <input  type="text" 
                                        data-url = "index.php?c=search&a=search"
                                        data-search_in = <?=ControllerSearch::SEARCH_IN_MOB?>
                                        data-minlenght = 3
                                        data-parameter = "<?=$this->getUniqid()?>"
                                        data-action = <?=ControllerSearch::SEARCH_DONE_ADD_MOB_TO_SPELL?>
                                        data-limit = 10
                                        data-only_usable = false
                                        class="form-control" 
                                        id="addMob<?=$this->getUniqid()?>" 
                                        placeholder="Rechercher un consommable">
                                <label for="addMob<?=$this->getUniqid()?>">Rechercher une créature</label>
                            </div>
                            <span id="search-sign"></span>
                        </div>
                        <script>autocomplete_load("#addMob<?=$this->getUniqid()?>");</script>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_OBJECT:
                    if(isset($mob)){
                        return $mob;
                    } else {
                        return null;
                    }

                case Content::FORMAT_RESUME:
                    if(isset($mob)){
                        return $mob->getVisual(Content::FORMAT_RESUME);
                    }else{
                        return "Aucune invocation attachée au sort";
                    }

                default:
                    if(isset($mob)){
                        return $this->_id_invocation;
                    }else{
                        return "";
                    }
            }   
        }
        public function getIs_magic(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    $checked = ""; 
                    if($this->_is_magic){ $checked = "checked"; } else { $checked = ""; }
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-start align-items-center">
                            <span data-bs-toggle='tooltip' data-bs-placement='top' title="Le sort est non-magique" class='badge back-brown-d-2 me-1'>Physique</span>
                            <div style="width:initial;" class="form-check form-switch my-1">
                                <input onchange="Spell.update('<?=$this->getUniqid();?>', this, 'is_magic', <?=Controller::IS_CHECKBOX?>);"  type="checkbox" class="form-check-input back-main-d-1 border-main-d-1" <?=$checked?> id="customSwitchIsmagic<?=$this->getId()?>">
                            </div>
                            <span data-bs-toggle='tooltip' data-bs-placement='top' title="Le sort est magique" class='badge back-purple-d-2'>Wakfu</span>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($this->_is_magic){ 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title=\"Le sort est magique\" class='badge back-purple-d-2'>Magique</span>";
                    } else { 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title=\"Le sort est non-magique\" class='badge back-brown-d-2'>Physique</span>";
                    }

                case Content::FORMAT_ICON:
                    if($this->_is_magic){ 
                        return "<i data-bs-toggle='tooltip' data-bs-placement='top' title=\"Le sort est magique\" class='fas fa-magic text-purple-d-2'></i>";
                    } else { 
                        return "<i data-bs-toggle='tooltip' data-bs-placement='top' title=\"Le sort est non-magique\" class='fas fa-fist-raised text-brown-d-2'></i>";
                    }
                    
                default:
                    return $this->_is_magic;
            }
        }
        public function getPowerful(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="dropdown">
                            <a type="button" id="dropdownPowerful<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getPowerful(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="dropdownPowerful<?=$this->getId()?>">
                                <?php for($i=1; $i <= 7; $i++) { ?>
                                    <a class="dropdown-item" onclick="Spell.update('<?=$this->getUniqid()?>', <?=$i?>, 'powerful', <?=Controller::IS_VALUE?>);$('#dropdownPowerful<?=$this->getId()?>').html($(this).html());"><span  data-bs-toggle='tooltip' data-bs-placement='bottom' title="Puissance d'un sort sur 7 niveaux"  class='badge back-deep-purple-d-3'>Puissance <?=$i?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    if(in_array($this->_powerful, [1,2,3,4,5,6,7])){
                        return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Puissance d'un sort sur 7 niveaux\" class='badge back-deep-purple-d-3'>Puissance ".$this->_powerful."</span>";
                    } else {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($this->_powerful, [1,2,3,4,5,6,7])){
                        return $this->_powerful;
                    } else {
                        return "";
                    }

                default:
                    return $this->_powerful;
            }
        }

        public function getPath_img(int $format = Content::FORMAT_BRUT, $css = ""){
            if(file_exists("medias/spells/".$this->getUniqid().".svg")){
                $this->setPath_image("medias/spells/".$this->getUniqid().".svg");
            }

            if(!empty($this->_path_img) || file_exists($this->_path_img)){
                $path = $this->_path_img;
            } else {
                $path = "medias/spells/default.svg";
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
                            <input onchange="Spell.update('<?=$this->getUniqid();?>', this, 'usable', <?=Controller::IS_CHECKBOX?>);"  type="checkbox" class="form-check-input back-main-d-1 border-main-d-1" <?=$checked?> id="customSwitchUsable<?=$this->getId()?>">
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

        public function getFrequency(int $format = Content::FORMAT_BRUT){
            $number_between_two_cast = $this->getNumber_between_two_cast();
            if($number_between_two_cast == 0){$number_between_two_cast = "";}

            switch ($format) {
                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="" class='badge back-main-d-1 border-main-d-1'><?=$this->getCast_per_turn()?> fois/<?=$number_between_two_cast?> tour(s)</span>
                    <?php return ob_get_clean();
                
                default:
                    return $this->getCast_per_turn(). " fois/".$number_between_two_cast. " tour(s)";
            }
        }

        public function getType(int $format = Content::FORMAT_BRUT){
            $manager = new SpellManager();
            $types = $manager->getLinkType($this);
            
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div class="btn-group">
                                <a class="btn btn-text-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Ajouter un type au sort</a>
                                <ul class="dropdown-menu"><?php
                                  foreach (Spell::TYPE as $key => $type) { ?>
                                    <li><a class="dropdown-item back-<?=View::getColorFromLetter($type)?>-d-4-hover text-<?=View::getColorFromLetter($type)?>-l-2-hover text-<?=View::getColorFromLetter($type)?>-d-4 badge-outline border-<?=View::getColorFromLetter($type)?>-d-4" onclick="Spell.update('<?=$this->getUniqid()?>',{action:'add', type:'<?=$type?>'},'type', IS_VALUE);"><?=$key?></a></li>
                                  <?php }  
                                ?></ul>
                            </div>
                        </div>
                        <?php if(!empty($types)){?>
                            <div id="list-spell" class="d-flex flex-row justify-content-around flex-wrap">
                                <?php foreach ($types as $type) { ?>
                                   <span ondblclick="Spell.update('<?=$this->getUniqid()?>',{action:'remove', type:'<?=$type?>'},'type', IS_VALUE);$(this).empty();" class="m-1 badge back-<?=View::getColorFromLetter($type)?>-d-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Double-cliquez pour supprimer"><?=array_search($type, Spell::TYPE)?></span>
                                <?php } ?>
                            </div>
                        <?php }
                    return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); 
                        if(!empty($types)){?>
                            <div id="list-spell" class="d-flex flex-row justify-content-around flex-wrap">
                                <?php foreach ($types as $key => $type) { ?>
                                   <span class="m-1 badge-outline border text-<?=View::getColorFromLetter($type)?>-d-4 border-<?=View::getColorFromLetter($type)?>-d-4" data-bs-toggle="tooltip" data-bs-placement="top" title="Type de sort"><?=array_search($type, Spell::TYPE)?></span>
                                <?php } ?>
                            </div>
                        <?php }
                    return ob_get_clean();

                case Content::FORMAT_TEXT:
                    if(!empty($types)){
                        $array = [];
                        foreach ($types as $type) {
                            $array[$type] = array_search($type, Spell::TYPE);
                        }
                        return $array;
                    } else {
                        return [];
                    }
                    
                case Content::FORMAT_ARRAY:
                    return $types;
                
            }
        }

        public function getVisual(int $format = Content::FORMAT_BRUT, $option =""){
            switch ($format) {
                case Content::FORMAT_MODIFY:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-2"><?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-200H-allL")?></div>
                                <div class="col-md-10">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-4 .col-sm-12">
                                                <?=$this->getLevel(Content::FORMAT_MODIFY)?>
                                                <?=$this->getCategory(Content::FORMAT_MODIFY)?>
                                                <?=$this->getType(Content::FORMAT_MODIFY)?>
                                                <?=$this->getElement(Content::FORMAT_MODIFY)?>
                                                <?=$this->getIs_magic(Content::FORMAT_MODIFY)?>
                                                <?=$this->getPowerful(Content::FORMAT_MODIFY)?>
                                                <?=$this->getUsable(Content::FORMAT_MODIFY)?>
                                            </div>  
                                            <div class="col-4 .col-sm-12">
                                                <?=$this->getPa(Content::FORMAT_MODIFY)?>
                                                <?=$this->getPo(Content::FORMAT_MODIFY)?>
                                                <?=$this->getPo_editable(Content::FORMAT_MODIFY)?>
                                            </div>   
                                            <div class="col-4 .col-sm-12">
                                                <?=$this->getCast_per_turn(Content::FORMAT_MODIFY)?>
                                                <?=$this->getNumber_between_two_cast(Content::FORMAT_MODIFY)?>
                                                <?=$this->getSight_line(Content::FORMAT_MODIFY)?>
                                            </div>            
                                        </div>
                                        <div class="nav-item-divider <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
                                        <p class='size-0-7 mb-1'>Sort <?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$this->getTimestamp_updated(Content::DATE_FR);?></p>
                                        <p class="card-text"><?=$this->getEffect(Content::FORMAT_MODIFY)?></p>
                                        <p class="card-text"><?=$this->getDescription(Content::FORMAT_MODIFY)?></p>
                                        <div class="nav-item-divider <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
                                        <p class="card-text"><?=$this->getId_invocation(Content::FORMAT_MODIFY)?></p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-right font-size-0-8 m-1"><a class='text-red-d-2 text-red-l-3-hover' onclick="Spell.remove('<?=$this->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_CARD:      
                    ob_start(); ?>
                        <div class="card p-2 m-2 border-2 <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "border")?>">
                            <div class="row g-0">
                                <div class="col-md-2">
                                    <a style="position:relative;top:5px;left:5px;" href="<?=$this->getPath_img()?>" download="<?=$this->getName().'.'.substr(strrchr($this->getPath_img(),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
                                    <?=$this->getPath_img(Content::FORMAT_FANCY, "img-back-200H-allL")?>
                                </div>
                                <div class="col-md-10">
                                    <div class="card-body">
                                        <div class="row justify-content-between">
                                            <div class="col-auto">
                                                <div><?=$this->getLevel(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getIs_magic(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getCategory(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getPowerful(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getElement(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getType(Content::FORMAT_BADGE)?></div>
                                            </div>
                                            <div class="col-auto">
                                                <div><?=$this->getPa(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getPo_editable(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getPo(Content::FORMAT_BADGE)?></div>
                                            </div>
                                            <div class="col-auto">
                                                <div><?=$this->getCast_per_turn(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getSight_line(Content::FORMAT_BADGE)?></div>
                                                <div><?=$this->getNumber_between_two_cast(Content::FORMAT_BADGE)?></div>
                                            </div>
                                            <div class="col-auto">
                                                <div><a class='text-main-d-2 text-main-l-3-hover' onclick="Spell.open('<?=$this->getUniqid()?>')"><i class='far fa-edit'></i> Modifier</a></div>
                                                <div><?=$this->getUsable(Content::FORMAT_MODIFY)?></div>
                                            </div>                      
                                        </div>
                                        <div class="nav-item-divider <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
                                        <h5 class="card-title"><?=$this->getName()?></h5>
                                        <p class="card-text"><?=$this->getEffect()?></p>
                                        <p class="card-text"><small class="text-muted"><?=$this->getDescription()?></small></p>
                                        <div class="nav-item-divider <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
                                        <p class="card-text"><?=$this->getId_invocation(Content::FORMAT_RESUME)?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php return ob_get_clean();
          
                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="position-relative">
                            <a style="position:absolute;top:-7px;right:0px;z-index:2;" onclick="$(this).parent().remove();"><i class="fas fa-times text-red-d-3 text-red-hover"></i></a> 
                            <div data-uniqid="<?=$this->getUniqid()?>" class=" size-0-9 card m-1 border-solid border-1 <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "border")?>">
                                <div class="d-flex flew-row flex-nowrap align-items-center">
                                    <?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-20 m-1")?>
                                    <div class="card-body m-1 p-0"><p><?=$this->getName()?></p></div>
                                </div>
                            </div>
                        </div>
                   <?php return ob_get_clean();

                case Content::FORMAT_LINK:
                    ob_start(); ?>
                        <div style="position:relative;">
                            <div ondblclick="Spell.open('<?=$this->getUniqid()?>');" onclick="Spell.showResume('<?=$this->getUniqid()?>', '#show-spell<?=$option?>', FORMAT_CARD, true);" class="card-hover-linked card p-2 m-1 <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>-l-5 <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>-l-4-hover border-solid border-2 <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "border")?>-d-2" style="width: 300px;" >
                                <div class="d-flex flew-row flex-nowrap">
                                    <div>
                                        <?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-50")?>
                                        <p class="mt-1"><?=$this->getLevel(Content::FORMAT_ICON)?></p> 
                                    </div>

                                    <div class="card-body m-1 p-0">
                                        <div class="d-flex flew-row justify-content-between ">
                                            <p class="bold"><?=$this->getName()?></p>
                                            <div class="d-flex flex-row align-content-center">
                                                <div style="height:18px;"><?=$this->getPo_editable(Content::FORMAT_ICON)?></div>
                                                <div><?=$this->getPa(Content::FORMAT_ICON)?></div>
                                            </div>
                                        </div>
                                        <p class="d-flex flex-row justify-content-around align-items-center">
                                            <?=$this->getPo(Content::FORMAT_ICON)?> 
                                            <?=$this->getSight_line(Content::FORMAT_ICON)?> 
                                            <?=$this->getFrequency(Content::FORMAT_BADGE)?>
                                        </p>
                                    </div>
                                </div>
                                <div class="card-hover-showed">
                                    <div class="d-flex flex-row justify-content-around align-items-baseline flex-wrap">
                                        <?=$this->getIs_magic(Content::FORMAT_BADGE)?>
                                        <?=$this->getType(Content::FORMAT_BADGE)?>
                                        <?=$this->getCategory(Content::FORMAT_BADGE)?>
                                        <?=$this->getPowerful(Content::FORMAT_BADGE)?>
                                        <?=$this->getElement(Content::FORMAT_BADGE)?>
                                    </div>
                                    <p><?=$this->getEffect()?></p>
                                    <div class="nav-item-divider <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
                                    <p><?=$this->getDescription()?></p>
                                    <?php if(!empty($this->getId_invocation())){ ?>
                                        <div class="nav-item-divider <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
                                        <p><?=$this->getId_invocation(Content::FORMAT_RESUME)?></p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                   <?php return ob_get_clean();

                case Content::FORMAT_RESUME:
                    ob_start(); ?>
                        <div style="position:relative;width: 300px;">
                            <div ondblclick="Spell.open('<?=$this->getUniqid()?>');" class="card-hover-linked card p-2 m-1 <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>-l-5 <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>-l-4-hover border-solid border-2 <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "border")?>-d-2" style="width: 300px;" >
                                <div class="d-flex flew-row flex-nowrap">
                                    <div>
                                        <?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-50")?>
                                        <p class="mt-1"><?=$this->getLevel(Content::FORMAT_ICON)?></p> 
                                    </div>

                                    <div class="card-body m-1 p-0">
                                        <div class="d-flex flew-row justify-content-between ">
                                            <p class="bold"><?=$this->getName()?></p>
                                            <div class="d-flex flex-row align-content-center">
                                                <div style="height:18px;"><?=$this->getPo_editable(Content::FORMAT_ICON)?></div>
                                                <div><?=$this->getPa(Content::FORMAT_ICON)?></div>
                                            </div>
                                        </div>
                                        <p class="d-flex flex-row justify-content-around align-items-center">
                                            <?=$this->getPo(Content::FORMAT_ICON)?> 
                                            <?=$this->getSight_line(Content::FORMAT_ICON)?> 
                                            <?=$this->getFrequency(Content::FORMAT_BADGE)?>
                                        </p>
                                    </div>
                                </div>
                                <div class="card-hover-showed">
                                    <div class="d-flex flex-row justify-content-around align-items-baseline flex-wrap">
                                        <?=$this->getIs_magic(Content::FORMAT_BADGE)?>
                                        <?=$this->getType(Content::FORMAT_BADGE)?>
                                        <?=$this->getCategory(Content::FORMAT_BADGE)?>
                                        <?=$this->getPowerful(Content::FORMAT_BADGE)?>
                                        <?=$this->getElement(Content::FORMAT_BADGE)?>
                                    </div>
                                    <p><?=$this->getEffect()?></p>
                                    <div class="nav-item-divider <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
                                    <p><?=$this->getDescription()?></p>
                                    <?php if(!empty($this->getId_invocation())){ ?>
                                        <div class="nav-item-divider <?=$this->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
                                        <p><?=$this->getId_invocation(Content::FORMAT_RESUME)?></p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php return ob_get_clean();
            }

        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName($data){
            $this->_name = $data;
            return "success";
        }
        public function setDescription($data){
            $this->_description = $data;
            return "success";
        }
        public function setEffect($data){
            $this->_effect = $data;
            return "success";
        }
        public function setLevel($data){
            if(is_numeric($data)){
                $this->_level = $data;
                return "success";
            } else {
                return "La valeur doit être un nombre";
            }
        }
        public function setPo($data){
            $this->_po = $data;
            return "success";
        }
        public function setPo_editable($data){
            $this->_po_editable = $this->returnBool($data);
            return "success";
        }
        public function setPa($data){
            $this->_pa = $data;
            return "success";
        }
        public function setCast_per_turn($data){
            $this->_cast_per_turn = $data;
            return "success";
        }
        public function setSight_line($data){
            $this->_sight_line = $this->returnBool($data);
            return "success";
        }
        public function setNumber_between_two_cast($data){
            $this->_number_between_two_cast = $data;
            return "success";
        }
        public function setElement($data){
            if(in_array($data, self::ELEMENT)){
                $this->_element = $data;
                return "success";
            } else {
                return "Valeur incorrect";
            }
        }
        public function setCategory($data){
            if(in_array($data, self::CATEGORY)){
                $this->_category = $data;
                return "success";
            } else {
                return "Valeur incorrect";
            }
        }
        public function setId_invocation($data){
            $manager = new MobManager;
            if($manager->existsId($data) || $data == 0){
                $this->_id_invocation = $data;
                return "success";
            } else {
                return "Valeur incorrect";
            }
        }
        public function setIs_magic($data){
            $this->_is_magic = $this->returnBool($data);
            return "success";
        }
        public function setPowerful($data){
            if(in_array($data, [1,2,3,4,5,6,7])){
                $this->_powerful = $data;
                return "success";
            } else {
                return "Valeur incorrect";
            }
        }
        public function setPath_image($data){
            if(is_file($data)){
                $file = New File($data);
                if(FileManager::isImage($file)){
                    $this->_path_img = $data;
                    return "success";
                } else {
                    return "Le fichier doit être une image.";
                }
            } else {
                return "Le fichier n'est pas valide.";
            }
        }
        public function setUsable($data){
            $this->_usable = $this->returnBool($data);
            return "success";
        }

        /* Data = array(
                        action => add ou remove,
                        type => numéro du type du sort                        
                    )
            Js : Item.update(Uniqid,{action:'add|remove', type:'type'},'type', IS_VALUE);
        */
        public function setType($data){ 
            if(is_array($data)){
                $manager = new SpellManager;
                if(!isset($data['type'])){return "Le type n'est pas défini";}
                if(in_array($data['type'], Spell::TYPE)){
    
                    if(isset($data['action'])){
                        switch ($data['action']) {
                            case 'add':
                                if($manager->addLinkType($this, $data['type'])){
                                    return "success";
                                }else{
                                    return "Erreur lors de l'ajout du type";
                                }
                   
                            case "remove":
                                if($manager->removeLinkType($this, $data['type'])){
                                    return "success";
                                }else{
                                    return "Erreur lors de la suppression du type";
                                }
    
                            default:
                                return "L'action n'est pas valide";
                        }
    
                    } else {
                        return "Une action est requise.";
                    }
                }
            }
        }
}
