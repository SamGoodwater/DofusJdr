<?php
class Npc extends Content
{
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='PNJ';
        private $_classe='';
        private $_story='';
        private $_historical='';
        private $_alignment='';
        private $_level=1;
        private $_trait='';
        private $_other_info='';
        
        private $_age='25';
        private $_size='1m70';
        private $_weight='70 kg';

        private $_life=30;
        private $_pa=6;
        private $_pm=3;
        private $_po=0;
        private $_ini=0;
        private $_invocation=0;
        private $_touch=0;
        private $_ca=0;
        private $_dodge_pa=0;
        private $_dodge_pm=0;
        private $_fuite=0;
        private $_tacle=0;
        private $_vitality=0;
        private $_sagesse=0;
        private $_strong=0;
        private $_intel=0;
        private $_agi=0;
        private $_chance=0;
        private $_res_neutre=0;
        private $_res_terre=0;
        private $_res_feu=0;
        private $_res_air=0;
        private $_res_eau=0;
        private $_acrobatie=0;
        private $_discretion=0;
        private $_escamotage=0;
        private $_athletisme=0;
        private $_intimidation=0;
        private $_arcane=0;
        private $_histoire=0;
        private $_investigation=0;
        private $_nature=0;
        private $_religion=0;
        private $_dressage=0;
        private $_medecine=0;
        private $_perception=0;
        private $_perspicacite=0;
        private $_survie=0;
        private $_persuasion=0;
        private $_representation=0;
        private $_supercherie=0;

        private $_kamas='';
        private $_drop_='';

        private $_other_equipment='';
        private $_other_consomable='';
        private $_other_spell='';
        
        // équipement
        // consomable
        // Spell

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥

        public function getName(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Nom</p>
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'name');" 
                                placeholder="Nom du ou de la PNJ" 
                                maxlength="50"
                                type="text" 
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                value="<?=$this->_name?>">
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_name;
            }
        }
        public function getClasse(int $format = Content::FORMAT_BRUT){
            $manager = New ClasseManager;
            if(!empty($this->_classe)){
                if($manager->existsUniqid($this->_classe)){
                    $object = $manager->getFromUniqid($this->_classe);
                } else {
                    $object = new Classe([]);
                }
            } else {
                $object = new Classe([]);
            }

            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="dropdown">
                            <a class="" type="button" id="dropdownDisplay<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getClasse(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="dropdownDisplay<?=$this->getId()?>"> <?php
                                foreach ($manager->getAll() AS $classe) { ?>
                                    <a class="dropdown-item" onclick="Npc.update('<?=$this->getUniqid()?>', '<?=$classe->getUniqid()?>', 'classe', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-main-d-2'><?=$classe->getName()?></span></a>
                                <?php } ?>
                            </div>
                        </div> <?php
                    return ob_get_clean();

                case Content::FORMAT_OBJECT:
                    return $object;

                case Content::FORMAT_BADGE:
                    return "<span class='badge back-main-d-2 data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Classe du ou de la PNJ\">{$object->getName()}</span>";
                
                default:
                    return $this->_classe;
            }
        }
        public function getStory(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1">
                            <p>Histoire</p>
                            <div  id="story<?=$this->getUniqid()?>"><?=html_entity_decode($this->_story)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Npc.update('<?=$this->getUniqid()?>', CKEDITOR5['story<?=$this->getUniqid()?>'].getData(), 'story', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#story<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Npc.update('<?=$this->getUniqid()?>', editor.getData(), 'story', IS_VALUE);
                                        }
                                    },
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
                                    CKEDITOR5['story<?=$this->getUniqid()?>'] = newEditor;
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
                    return html_entity_decode($this->_story);
            }
        }
        public function getHistorical(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Historique</p>
                            <textarea 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'historical');" 
                                placeholder="Histoire du ou de la PNJ" 
                                maxlength="255"
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                rows="3"><?=$this->_historical?></textarea>
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_historical;
            }
        }
        public function getAlignment(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Alignement</p>
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'alignment');" 
                                placeholder="Alignement du ou de la PNJ" 
                                maxlength="500"
                                type="text" 
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                value="<?=$this->_alignment?>">
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    return "<span class='badge back-grey-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Alignement\">{$this->_alignment}</span>";

                default:
                    return $this->_alignment;
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-<?=View::getColorFromLetter($this->_level, true)?>-d-3">
                            <label>Niveau du ou de la PNJ</label>
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'level');" 
                                data-bs-toggle='tooltip' data-bs-placement='bottom' title="Niveau du ou de la PNJ"
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_level?>">
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-".View::getColorFromLetter($this->_level, true)."-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Niveau du ou de la PNJ\">Niveau {$this->_level}</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-".View::getColorFromLetter($this->_level, true)."-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Niveau du ou de la PNJ\">{$this->_level}</span>";
                
                default:
                    return $this->_level;
            }
        }
        public function getTrait(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'trait');" 
                                placeholder="Traits du joueur" 
                                maxlength="3000" 
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_trait?>">
                            <label class="size-0-8">Traits du ou de la PNJ</label>
                        </div>
                        <span class="size-0-8 text-grey">Séparer les différents traits par des virgules.</span>
                        <?php if(!empty($this->getClasse(Content::FORMAT_OBJECT)->getTrait())){ ?>
                            <p>Traits propre à la classe : <?=$this->getClasse(Content::FORMAT_OBJECT)->getTrait(Content::FORMAT_BADGE)?></p>
                        <?php }
                    return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-around"> <?php
                            foreach (explode(",", $this->_trait) as $trait) { ?>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Trait <?=$trait?>" class="badge back-main-d-1"><?=$trait?></span>
                            <?php } ?>                            
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_VIEW:
                    return $this->getClasse(Content::FORMAT_OBJECT)->getTrait(Content::FORMAT_BADGE) . $this->getTrait(Content::FORMAT_BADGE);

                default:
                    return $this->_trait;
            }

        }
        public function getOther_info(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1">
                            <p>Caractères et autres informations</p>
                            <div  id="other_info<?=$this->getUniqid()?>"><?=html_entity_decode($this->_other_info)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Npc.update('<?=$this->getUniqid()?>', CKEDITOR5['other_info<?=$this->getUniqid()?>'].getData(), 'other_info', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#other_info<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Npc.update('<?=$this->getUniqid()?>', editor.getData(), 'other_info', IS_VALUE);
                                        }
                                    },
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
                                    CKEDITOR5['other_info<?=$this->getUniqid()?>'] = newEditor;
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
                    return html_entity_decode($this->_other_info);
            }
        }
        public function getAge(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Age</p>
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'age');" 
                                placeholder="Age du ou de la PNJ" 
                                maxlength="50"
                                type="text" 
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                value="<?=$this->_age?>">
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span>Age : {$this->_age} ans</span>";
                   
                default:
                    return $this->_age;
            }
        }
        public function getSize(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Taille</p>
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'size');" 
                                placeholder="Taille du ou de la PNJ" 
                                maxlength="50"
                                type="text" 
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                value="<?=$this->_size?>">
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span>Taille : {$this->_size}</span>";
                   
                default:
                    return $this->_size;
            }
        }
        public function getWeight(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Poids</p>
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'weight');" 
                                placeholder="Poids du ou de la PNJ" 
                                maxlength="50"
                                type="text" 
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                value="<?=$this->_weight?>">
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span>Poids : {$this->_weight}</span>";
                   
                default:
                    return $this->_weight;
            }
        }

        public function getLife(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="text-life size-0-9">Poins de vie</p>
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'life');" 
                                placeholder="Points de vie du ou de la PNJ" 
                                maxlength="50"
                                type="text" 
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                value="<?=$this->_life?>">
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-life border-1 border-solid">
                            <div class="m-2">
                                <h6 class="m-0 text-life"><?=$this->_life?> points de vie</h6>
                                <p class="text-grey-d-2 size-0-8">Dès de classe + mod. Vitalité * niveau + bonus d'équipement</p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

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
                        <div class="m-1 text-pa">
                            <label class="text-pa size-0-9">PA</label>
                            <div class="input-group">
                                <div class="input-group-text back-pa text-white"><img class='icon' src='medias/icons/pa.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'pa');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="PA du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_pa?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-pa' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"PA du ou de la PNJ\">PA {$this->_pa}</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pa' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"PA du ou de la PNJ\">{$this->_pa} <img class='icon' src='medias/icons/pa.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-pa border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-pa"><?=$this->_pa?> PA</h6>
                                <p class="text-grey-d-2 size-0-8">Bonus d'équipement</p>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_pa;
            }
        }
        public function getPm(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1 text-pm">
                            <label class="text-pm size-0-9">PM</label>
                            <div class="input-group">
                                <div class="input-group-text back-pm text-white"><img class='icon' src='medias/icons/pm.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'pm');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="PM du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_pm?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-pm' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"PM du ou de la PNJ\">PM {$this->_pm}</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pm' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"PM du ou de la PNJ\">{$this->_pm} <img class='icon' src='medias/icons/pm.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-pm border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-pm"><?=$this->_pm?> PM</h6>
                                <p class="text-grey-d-2 size-0-8">Bonus d'équipement</p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();

                default:
                    return $this->_pm;
            }
        }
        public function getPo(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1 text-pm">
                            <label class="text-po size-0-9">Bonus de PO</label>
                            <div class="input-group">
                                <div class="input-group-text back-po text-white"><img class='icon' src='medias/icons/po.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'po');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="PO du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_po?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-po' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"PO du ou de la PNJ\">PO {$this->_po}</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-po' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"PO du ou de la PNJ\">{$this->_po} <img class='icon' src='medias/icons/po.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-po border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-po"><?=$this->_po?> PO</h6>
                                <p class="text-grey-d-2 size-0-8">Bonus d'équipement</p>
                            </div> 
                        </div>       
                    <?php return ob_get_clean();

                default:
                    return $this->_po;
            }
        }
        public function getIni(int $format = Content::FORMAT_BRUT){
            $total = $this->_ini + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1 text-ini">
                            <label class="size-0-9">Initiative</label>
                            <div class="input-group">
                                <div class="input-group-text back-ini text-white"><img class='icon' src='medias/icons/ini.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'ini');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus d'initiative du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_ini?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9"><?=$this->getIntel(Content::FORMAT_BADGE)?> + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-ini' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'initiative du ou de la PNJ\">{$this->_ini} Bonus d'initiative</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-ini' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'initiative du ou de la PNJ\">{$this->_ini} <img class='icon' src='medias/icons/ini.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-ini border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-ini"><?=$total?> Ini</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getIntel(Content::FORMAT_BADGE)?> + <?=$this->getIni(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();

                default:
                    return $this->_ini;
            }
        }
        public function getInvocation(int $format = Content::FORMAT_BRUT){
            $total = $this->_invocation + 1;
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1 text-invocation">
                            <label class="size-0-9">Invocation</label>
                            <div class="input-group">
                                <div class="input-group-text back-invocation text-white"><img class='icon' src='medias/icons/invocation.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'invocation');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre d'invocation du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_ini?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">1 + Bonus de l'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-invocation' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Nombre d'invocation du ou de la PNJ\">{$total} Invocations</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-invocation' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Nombre d'invocation du ou de la PNJ\">{$total} <img class='icon' src='medias/icons/invocation.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-invocation border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-invocation"><?=$total?> Invocation</h6>
                                <p class="text-grey-d-2 size-0-8">1 + Bonus de l'équipement</p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();
                default:
                    return $this->_invocation;
            }
        }
        public function getTouch(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1 text-touch">
                            <label class="size-0-9">Touche</label>
                            <div class="input-group">
                                <div class="input-group-text back-touch text-white"><img class='icon' src='medias/icons/touch.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'touch');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de touche du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_touch?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus de l'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-touch' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de touche du ou de la PNJ\">+ {$this->_touch} Touche</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-touch' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de touche du ou de la PNJ\">{$this->_touch} <img class='icon' src='medias/icons/touch.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-touch border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-touch">+ <?=$this->_touch?> Touche</h6>
                                <p class="text-grey-d-2 size-0-8">Bonus de l'équipement</p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();
                default:
                    return $this->_touch;
            }
        }
        public function getCa(int $format = Content::FORMAT_BRUT){
            $total = 10 + $this->_ca + $this->getVitality();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-ca">
                            <label>Bonus de Classe d'armure</label>
                            <div class="input-group">
                                <div class="input-group-text back-grey text-white"><img class='icon' src='medias/icons/ca.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'ca');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de la classe d'armure du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_ca?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">10 + <?=$this->getVitality(Content::FORMAT_BADGE)?> + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-ca' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de la classe d'armure du ou de la PNJ\">10 + {$this->_ca} Bonus de CA</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-ca' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de la classe d'armure du ou de la PNJ\">10 + {$this->_ca} <img class='icon' src='medias/icons/ca.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-ca border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-ca"><?=$total?> CA</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getVitality(Content::FORMAT_BADGE)?> + <?=$this->getCa(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();
                
                default:
                    return $this->_ca;
            }
        }
        public function getDodge_pa(int $format = Content::FORMAT_BRUT){
            $total = 10 + $this->_dodge_pa + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-pa">
                            <label>Bonus d'Esquive PA</label>
                            <div class="input-group">
                                <div class="input-group-text back-pa text-white"><img class='icon' src='medias/icons/dodge_pa.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'dodge_pa');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus d'Esquive PA du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_dodge_pa?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">10 + <?=$this->getSagesse(Content::FORMAT_BADGE)?> + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-pa' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'Esquive PA du ou de la PNJ\">10 + {$this->_dodge_pa} Bonus d'Esquive PA</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pa' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'Esquive PA du ou de la PNJ\">10 + {$this->_dodge_pa} <img class='icon' src='medias/icons/dodge_pa.png'></span>";
               
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-pa border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-pa"><?=$total?> Esquive PA</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getSagesse(Content::FORMAT_BADGE)?> + <?=$this->getDodge_pa(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();

                default:
                    return $this->_dodge_pa;
            }
        }
        public function getDodge_pm(int $format = Content::FORMAT_BRUT){
            $total = 10 + $this->_dodge_pm + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-pm">
                            <label>Bonus d'Esquive PM</label>
                            <div class="input-group">
                                <div class="input-group-text back-pm text-white"><img class='icon' src='medias/icons/dodge_pm.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'dodge_pm');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus d'Esquive PM du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_dodge_pm?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">10 + <?=$this->getSagesse(Content::FORMAT_BADGE)?> + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-pm' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'Esquive PM du ou de la PNJ\">10 + {$this->_dodge_pm} Bonus d'Esquive PM</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pm' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'Esquive PM du ou de la PNJ\">10 + {$this->_dodge_pm} <img class='icon' src='medias/icons/dodge_pm.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-pm border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-pm"><?=$total?> Esquive PM</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getSagesse(Content::FORMAT_BADGE)?> + <?=$this->getDodge_pm(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();    

                default:
                    return $this->_dodge_pm;
            }
        }
        public function getFuite(int $format = Content::FORMAT_BRUT){
            $total = $this->_fuite + $this->getAgi();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-fuite">
                            <label>Bonus Fuite</label>
                            <div class="input-group">
                                <div class="input-group-text back-fuite text-white"><img class='icon' src='medias/icons/fuite.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'fuite');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de fuite du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_fuite?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9"><?=$this->getAgi(Content::FORMAT_BADGE)?> + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-fuite' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de fuite du ou de la PNJ\">{$this->_fuite} Bonus de fuite</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-fuite' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de fuite du ou de la PNJ\">{$this->_fuite} <img class='icon' src='medias/icons/fuite.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-fuite border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-fuite"><?=$total?> Fuite</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getAgi(Content::FORMAT_BADGE)?> + <?=$this->getFuite(Content::FORMAT_BADGE)?></p>
                            </div>    
                        </div>    
                    <?php return ob_get_clean();
                
                default:
                    return $this->_fuite;
            }
        }
        public function getTacle(int $format = Content::FORMAT_BRUT){
            $total = $this->_tacle + $this->getChance();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-tacle">
                            <label>Bonus Tacle</label>
                            <div class="input-group">
                                <div class="input-group-text back-tacle text-white"><img class='icon' src='medias/icons/tacle.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'tacle');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de tacle du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_tacle?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9"><?=$this->getChance(Content::FORMAT_BADGE)?> + Bonus</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-tacle' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de tacle du ou de la PNJ\">{$this->_tacle} Bonus de tacle</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-tacle' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de tacle du ou de la PNJ\">{$this->_tacle} <img class='icon' src='medias/icons/tacle.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-tacle border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-tacle"><?=$total?> Tacle</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getChance(Content::FORMAT_BADGE)?> + <?=$this->getTacle(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();
                
                default:
                    return $this->_tacle;
            }
        }
        public function getVitality(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-vitality">
                            <label>Mod. Vitalité</label>
                            <div class="input-group">
                                <div class="input-group-text back-vitality text-white"><img class='icon' src='medias/icons/vitality.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'vitality');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur de vitalité du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_vitality?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-vitality' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de valité du ou de la PNJ\">{$this->_vitality} Mod. Vitalité</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-vitality' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de vitalité du ou de la PNJ\">{$this->_vitality} <img class='icon' src='medias/icons/vitality.png'></span>";
                    
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-vitality border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-vitality"><?=$this->_vitality?> Mod. Vitalité</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div> 
                        </div>       
                    <?php return ob_get_clean();

                default:
                    return $this->_vitality;
            }
        }
        public function getSagesse(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-sagesse">
                            <label>Mod. Sagesse</label>
                            <div class="input-group">
                                <div class="input-group-text back-sagesse text-white"><img class='icon' src='medias/icons/sagesse.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'sagesse');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur de sagesse du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_sagesse?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-sagesse' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de sagesse du ou de la PNJ\">{$this->_sagesse} Mod. Sagesse</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-sagesse' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de sagesse du ou de la PNJ\">{$this->_sagesse} <img class='icon' src='medias/icons/sagesse.png'></span>";
                    
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-sagesse border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-sagesse"><?=$this->_sagesse?> Mod. Sagesse</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div> 
                        </div>       
                    <?php return ob_get_clean();

                default:
                    return $this->_sagesse;
            }
        }
        public function getStrong(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-strong">
                            <label>Mod. Force</label>
                            <div class="input-group">
                                <div class="input-group-text back-strong text-white"><img class='icon' src='medias/icons/force.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'strong');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur de force du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_strong?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-strong' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de force du ou de la PNJ\">{$this->_strong} Mod. Force</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-strong' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de force du ou de la PNJ\">{$this->_strong} <img class='icon' src='medias/icons/force.png'></span>";
                    
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-strong border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-strong"><?=$this->_strong?> Mod. Force</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus de Force</p>
                            </div>  
                        </div>      
                    <?php return ob_get_clean();

                default:
                    return $this->_strong;
            }
        }
        public function getIntel(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-intel">
                            <label>Mod. Intelligence</label>
                            <div class="input-group">
                                <div class="input-group-text back-intel text-white"><img class='icon' src='medias/icons/intel.png'></i></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'intel');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur d'intelligence du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_intel?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-intel' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur d'intelligence du ou de la PNJ\">{$this->_intel} Mod. Intelligence</span>";
                    
                case Content::FORMAT_ICON:
                    return "<span class='text-intel' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur d'intelligence du ou de la PNJ\">{$this->_intel} <img class='icon' src='medias/icons/intel.png'></span>";
                    
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-intel border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-intel"><?=$this->_intel?> Mod. Intelligence</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_intel;
            }
        }
        public function getAgi(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-agi">
                            <label>Mod. Agilité</label>
                            <div class="input-group">
                                <div class="input-group-text back-agi text-white"><img class='icon' src='medias/icons/agi.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'agi');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur d'agilité du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_agi?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-agi' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur d'agilité du ou de la PNJ\">{$this->_agi} Mod. Agilité</span>";
                    
                case Content::FORMAT_ICON:
                    return "<span class='text-agi' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur d'agilité du ou de la PNJ\">{$this->_agi} <img class='icon' src='medias/icons/agi.png'></span>";
                    
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-agi border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-agi"><?=$this->_agi?> Mod. Agilité</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_agi;
            }
        }
        public function getChance(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-chance">
                            <label>Mod. Chance</label>
                            <div class="input-group">
                                <div class="input-group-text back-chance text-white"><img class='icon' src='medias/icons/chance.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'chance');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur de chance du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_chance?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-chance' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de chance du ou de la PNJ\">{$this->_chance} Mod. Chance</span>";
                    
                case Content::FORMAT_ICON:
                    return "<span class='text-chance' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Calcul du modificateur de chance du ou de la PNJ\">{$this->_chance} <img class='icon' src='medias/icons/chance.png'></span>";
                    
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-chance border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-chance"><?=$this->_chance?> Mod. Chance</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_chance;
            }
        }  
        public function getRes_neutre(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-neutre">
                            <label>Resistance neutre</label>
                            <div class="input-group">
                                <div class="input-group-text back-neutre text-white"><img class='icon' src='medias/icons/res_neutre.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'res_neutre');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance neutre du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_neutre?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-neutre' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance neutre du ou de la PNJ\">{$this->_res_neutre} Résistance Neutre</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-neutre' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance neutre du ou de la PNJ\">{$this->_res_neutre} <img class='icon' src='medias/icons/res_neutre.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-neutre border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="text-neutre"><?=$this->_res_neutre?> Res. neutre</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div>
                        </div> 
                    <?php return ob_get_clean();

                default:
                    return $this->_res_neutre;
            }
        }
        public function getRes_terre(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-terre">
                            <label>Resistance terre</label>
                            <div class="input-group">
                                <div class="input-group-text back-terre text-white"><img class='icon' src='medias/icons/res_terre.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'res_terre');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance terre du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_terre?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-terre' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance terre du ou de la PNJ\">{$this->_res_terre} Résistance Terre</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-terre' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance terre du ou de la PNJ\">{$this->_res_terre} <img class='icon' src='medias/icons/res_terre.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-terre border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-terre"><?=$this->_res_terre?> Res. Force</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_res_terre;
            }
        }
        public function getRes_feu(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-feu">
                            <label>Resistance feu</label>
                            <div class="input-group">
                                <div class="input-group-text back-feu text-white"><img class='icon' src='medias/icons/res_feu.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'res_feu');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance feu du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_feu?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-feu' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance feu du ou de la PNJ\">{$this->_res_feu} Résistance Feu</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-feu' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance feu du ou de la PNJ\">{$this->_res_feu} <img class='icon' src='medias/icons/res_feu.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-feu border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-feu"><?=$this->_res_feu?> Res. Feu</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_res_feu;
            }
        }
        public function getRes_air(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-air">
                            <label>Resistance air</label>
                            <div class="input-group">
                                <div class="input-group-text back-air text-white"><img class='icon' src='medias/icons/res_air.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'res_air');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance air du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_air?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-air' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance air du ou de la PNJ\">{$this->_res_air} Résistance Air</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-air' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance air du ou de la PNJ\">{$this->_res_air} <img class='icon' src='medias/icons/res_air.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-air border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-air"><?=$this->_res_air?> Res. Air</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_res_air;
            }
        }
        public function getRes_eau(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-eau">
                            <label>Resistance eau</label>
                            <div class="input-group">
                                <div class="input-group-text back-eau text-white"><img class='icon' src='medias/icons/res_eau.png'></div>
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'res_eau');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance eau du ou de la PNJ"
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$this->_res_eau?>">
                            </div>
                            <p class="text-grey-d-1 size-0-9">Bonus d'équipement</p>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-eau' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance eau du ou de la PNJ\">{$this->_res_eau} Résistance Eau</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-eau' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Résistance eau du ou de la PNJ\">{$this->_res_eau} <img class='icon' src='medias/icons/res_eau.png'></span>";
                
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-eau border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-eau"><?=$this->_res_eau?> Res. Eau</h6>
                                <p class="text-grey-d-2 size-0-8">Bonnus d'équipement</p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_res_eau;
            }
        }
        public function getAcrobatie(int $format = Content::FORMAT_BRUT){
            $total = $this->_acrobatie + $this->getAgi();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-agi">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'acrobatie');" 
                                placeholder="Bonus d'acrobatie" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_acrobatie?>">
                            <label class="size-0-8">Bonus d'acrobatie</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-agi' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'acrobatie du ou de la PNJ\">{$this->_acrobatie} Bonus d'acrobatie</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-agi border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-agi"><?=$total?> Acrobatie</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getAgi(Content::FORMAT_BADGE)?> + <?=$this->getAcrobatie(Content::FORMAT_BADGE)?></p>
                            </div>        
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_acrobatie;
            }
        }
        public function getDiscretion(int $format = Content::FORMAT_BRUT){
            $total = $this->_discretion + $this->getAgi();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-agi">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'discretion');" 
                                placeholder="Bonus de discretion" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_discretion?>">
                            <label class="size-0-8">Bonus de discretion</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-agi' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de discretion du ou de la PNJ\">{$this->_discretion} Bonus de discretion</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-agi border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-agi"><?=$total?> Discretion</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getAgi(Content::FORMAT_BADGE)?> + <?=$this->getDiscretion(Content::FORMAT_BADGE)?></p>
                            </div>  
                        </div>      
                    <?php return ob_get_clean();
                
                default:
                    return $this->_discretion;
            }
        }
        public function getEscamotage(int $format = Content::FORMAT_BRUT){
            $total = $this->_escamotage + $this->getAgi();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-agi">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'escamotage');" 
                                placeholder="Bonus d'escamotage" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_escamotage?>">
                            <label class="size-0-8">Bonus d'escamotage</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-agi' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'escamotage du ou de la PNJ\">{$this->_escamotage} Bonus d'escamotage</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-agi border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-agi"><?=$total?> Escamotage</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getAgi(Content::FORMAT_BADGE)?> + <?=$this->getEscamotage(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();

                default:
                    return $this->_escamotage;
            }
        }
        public function getAthletisme(int $format = Content::FORMAT_BRUT){
            $total = $this->_athletisme + $this->getStrong();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-for">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'athletisme');" 
                                placeholder="Bonus d'athletisme" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_athletisme?>">
                            <label class="size-0-8">Bonus d'athletisme</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-force' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'athletisme du ou de la PNJ\">{$this->_athletisme} Bonus d'athletisme</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-strong border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-strong"><?=$total?> Athletisme</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getStrong(Content::FORMAT_BADGE)?> + <?=$this->getAthletisme(Content::FORMAT_BADGE)?></p>
                            </div> 
                        </div>       
                    <?php return ob_get_clean();

                default:
                    return $this->_athletisme;
            }
        }
        public function getIntimidation(int $format = Content::FORMAT_BRUT){
            $total = $this->_intimidation + $this->getStrong();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-strong">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'intimidation');" 
                                placeholder="Bonus d'intimidation" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_intimidation?>">
                            <label class="size-0-8">Bonus d'intimidation</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-strong' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'intimidation du ou de la PNJ\">{$this->_intimidation} Bonus d'intimidation</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-strong border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-strong"><?=$total?> Intimidation</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getStrong(Content::FORMAT_BADGE)?> + <?=$this->getIntimidation(Content::FORMAT_BADGE)?></p>
                            </div>   
                        </div>     
                    <?php return ob_get_clean();

                default:
                    return $this->_intimidation;
            }
        }
        public function getArcane(int $format = Content::FORMAT_BRUT){
            $total = $this->_arcane + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-intel">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'arcane');" 
                                placeholder="Bonus d'arcane" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_arcane?>">
                            <label class="size-0-8">Bonus d'arcane</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-intel' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'arcane du ou de la PNJ\">{$this->_arcane} Bonus d'arcane</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-intel border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-intel"><?=$total?> Arcane</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getIntel(Content::FORMAT_BADGE)?> + <?=$this->getArcane(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();

                default:
                    return $this->_arcane;
            }
        }
        public function getHistoire(int $format = Content::FORMAT_BRUT){
            $total = $this->_histoire + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-intel">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'histoire');" 
                                placeholder="Bonus d'histoire" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_histoire?>">
                            <label class="size-0-8">Bonus d'histoire</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-intel' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'histoire du ou de la PNJ\">{$this->_histoire} Bonus d'histoire</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-intel border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-intel"><?=$total?> Histoire</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getIntel(Content::FORMAT_BADGE)?> + <?=$this->getHistoire(Content::FORMAT_BADGE)?></p>
                            </div>    
                        </div>    
                    <?php return ob_get_clean();

                default:
                    return $this->_histoire;
            }
        }
        public function getInvestigation(int $format = Content::FORMAT_BRUT){
            $total = $this->_investigation + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-intel">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'investigation');" 
                                placeholder="Bonus d'investigation" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_investigation?>">
                            <label class="size-0-8">Bonus d'investigation</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-intel' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus d'investigation du ou de la PNJ\">{$this->_investigation} Bonus d'investigation</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-intel border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-intel"><?=$total?> Investigation</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getIntel(Content::FORMAT_BADGE)?> + <?=$this->getInvestigation(Content::FORMAT_BADGE)?></p>
                            </div>    
                        </div>    
                    <?php return ob_get_clean();

                default:
                    return $this->_investigation;
            }
        }
        public function getReligion(int $format = Content::FORMAT_BRUT){
            $total = $this->_religion + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-intel">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'religion');" 
                                placeholder="Bonus de religion" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_religion?>">
                            <label class="size-0-8">Bonus de religion</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-intel' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de religion du ou de la PNJ\">{$this->_religion} Bonus de religion</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-intel border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-intel"><?=$total?> Religion</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getIntel(Content::FORMAT_BADGE)?> + <?=$this->getReligion(Content::FORMAT_BADGE)?></p>
                            </div>  
                        </div>      
                    <?php return ob_get_clean();

                default:
                    return $this->_religion;
            }
        }
        public function getNature(int $format = Content::FORMAT_BRUT){
            $total = $this->_nature + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-intel">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'nature');" 
                                placeholder="Bonus de nature" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_nature?>">
                            <label class="size-0-8">Bonus de nature</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-intel' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de nature du ou de la PNJ\">{$this->_nature} Bonus de nature</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-intel border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-intel"><?=$total?> Nature</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getIntel(Content::FORMAT_BADGE)?> + <?=$this->getNature(Content::FORMAT_BADGE)?></p>
                            </div>       
                        </div> 
                    <?php return ob_get_clean();

                default:
                    return $this->_nature;
            }
        }
        public function getDressage(int $format = Content::FORMAT_BRUT){
            $total = $this->_dressage + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-sagesse">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'dressage');" 
                                placeholder="Bonus de dressage" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_dressage?>">
                            <label class="size-0-8">Bonus de dressage</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-sagesse' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de dressage du ou de la PNJ\">{$this->_dressage} Bonus de dressage</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-sagesse border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-sagesse"><?=$total?> Dressage</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getSagesse(Content::FORMAT_BADGE)?> + <?=$this->getDressage(Content::FORMAT_BADGE)?></p>
                            </div> 
                        </div>       
                    <?php return ob_get_clean();

                default:
                    return $this->_dressage;
            } 
        }
        public function getMedecine(int $format = Content::FORMAT_BRUT){
            $total = $this->_medecine + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-sagesse">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'medecine');" 
                                placeholder="Bonus de médecine" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_medecine?>">
                            <label class="size-0-8">Bonus de médecine</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-sagesse' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de dressage du ou de la PNJ\">{$this->_medecine} Bonus de dressage</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-sagesse border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-sagesse"><?=$total?> Dressage</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getSagesse(Content::FORMAT_BADGE)?> + <?=$this->getMedecine(Content::FORMAT_BADGE)?></p>
                            </div> 
                        </div>       
                    <?php return ob_get_clean();

                default:
                    return $this->_medecine;
            }
        }
        public function getPerception(int $format = Content::FORMAT_BRUT){
            $total = $this->_perception + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-sagesse">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'perception');" 
                                placeholder="Bonus de perception" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_perception?>">
                            <label class="size-0-8">Bonus de perception</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-sagesse' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de perception du ou de la PNJ\">{$this->_perception} Bonus de perception</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-sagesse border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-sagesse"><?=$total?> Perception</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getSagesse(Content::FORMAT_BADGE)?> + <?=$this->getPerception(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>        
                    <?php return ob_get_clean();

                default:
                    return $this->_perception;
            }
        }
        public function getPerspicacite(int $format = Content::FORMAT_BRUT){
            $total = $this->_perspicacite + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-sagesse">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'perspicacite');" 
                                placeholder="Bonus de perspicacité" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_perspicacite?>">
                            <label class="size-0-8">Bonus de perspicacité</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-sagesse' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de perspicacité du ou de la PNJ\">{$this->_perspicacite} Bonus de perspicacité</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-sagesse border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-sagesse"><?=$total?> Perspicacité</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getSagesse(Content::FORMAT_BADGE)?> + <?=$this->getPerspicacite(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_perspicacite;
            }
        }
        public function getSurvie(int $format = Content::FORMAT_BRUT){
            $total = $this->_survie + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-sagesse">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'survie');" 
                                placeholder="Bonus de survie" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_survie?>">
                            <label class="size-0-8">Bonus de survie</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-sagesse' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de survie du ou de la PNJ\">{$this->_survie} Bonus de survie</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-sagesse border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-sagesse"><?=$total?> Survie</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getSagesse(Content::FORMAT_BADGE)?> + <?=$this->getSurvie(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_survie;
            }
        }
        public function getPersuasion(int $format = Content::FORMAT_BRUT){
            $total = $this->_persuasion + $this->getChance();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-chance">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'persuasion');" 
                                placeholder="Bonus de persuasion" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_persuasion?>">
                            <label class="size-0-8">Bonus de persuasion</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-chance' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de persuasion du ou de la PNJ\">{$this->_persuasion} Bonus de persuasion</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-chance border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-chance"><?=$total?> Persuasion</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getChance(Content::FORMAT_BADGE)?> + <?=$this->getPersuasion(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_persuasion;
            }
        }
        public function getRepresentation(int $format = Content::FORMAT_BRUT){
            $total = $this->_representation + $this->getChance();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-chance">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'representation');" 
                                placeholder="Bonus de représentation" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_representation?>">
                            <label class="size-0-8">Bonus de représentation</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-chance' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de représentation du ou de la PNJ\">{$this->_representation} Bonus de représentation</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-chance border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-chance"><?=$total?> Représentation</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getChance(Content::FORMAT_BADGE)?> + <?=$this->getRepresentation(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_representation;

            }   
        }
        public function getSupercherie(int $format = Content::FORMAT_BRUT){
            $total = $this->_supercherie + $this->getChance();
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1 text-chance">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'supercherie');" 
                                placeholder="Bonus de supercherie" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_supercherie?>">
                            <label class="size-0-8">Bonus de supercherie</label>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-chance' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Bonus de supercherie du ou de la PNJ\">{$this->_supercherie} Bonus de supercherie</span>";
                   
                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div class="card border-chance border-1 border-solid" style="width: 18rem;">
                            <div class="m-2">
                                <h6 class="m-0 text-chance"><?=$total?> Supercherie</h6>
                                <p class="text-grey-d-2 size-0-8"><?=$this->getChance(Content::FORMAT_BADGE)?> + <?=$this->getSupercherie(Content::FORMAT_BADGE)?></p>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_supercherie;

            }
        }
        public function getKamas(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                    <div class="m-1">
                        <div class="form-floating mb-1 text-kamas">
                                <input 
                                    onchange="Npc.update('<?=$this->getUniqid();?>', this, 'kamas');" 
                                    placeholder="Kamas" 
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_kamas?>">
                                <label class="size-0-8">Kamas</label>
                            </div>
                            <p class="text-grey-d-1 size-0-9">Kamas possédés par le ou la PNJ</p>
                    </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-kamas' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Kamas possédés par le ou la PNJ\">{$this->_kamas} Kamas</span>";

                case Content::FORMAT_ICON:
                    return "<span class='badge back-kamas' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Kamas possédés par le ou la PNJ\">{$this->_kamas} <img class='icon' src='medias/icons/kamas.png'></span>";
    
                default:
                    return $this->_kamas;

            }
        }
        public function getDrop_(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                    <div class="w-100 m-1">
                        <div class="form-floating mb-1 text-grey-d-2">
                            <input 
                                onchange="Npc.update('<?=$this->getUniqid();?>', this, 'drop_');" 
                                placeholder="Objets récupérables" 
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_drop_?>">
                            <label class="size-0-8">Objets récupérables</label>
                        </div>
                        <p class="text-grey-d-1 size-0-9">Objets récupérables du ou de la PNJ</p>
                    </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-grey-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Objets récupérables du ou de la PNJ\">{$this->_drop_}</span>";

                default:
                    return $this->_drop_;

            }
        }

        public function getOther_equipment(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1">
                            <p>Autres équipements</p>
                            <div  id="other_equipment<?=$this->getUniqid()?>"><?=html_entity_decode($this->_other_equipment)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Npc.update('<?=$this->getUniqid()?>', CKEDITOR5['other_equipment<?=$this->getUniqid()?>'].getData(), 'other_equipment', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#other_equipment<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Npc.update('<?=$this->getUniqid()?>', editor.getData(), 'other_equipment', IS_VALUE);
                                        }
                                    },
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
                                    CKEDITOR5['other_equipment<?=$this->getUniqid()?>'] = newEditor;
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
                    return html_entity_decode($this->_other_equipment);
            }
        }
        public function getOther_spell(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1">
                            <p>Autres sorts</p>
                            <div  id="other_spell<?=$this->getUniqid()?>"><?=html_entity_decode($this->_other_spell)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Npc.update('<?=$this->getUniqid()?>', CKEDITOR5['other_spell<?=$this->getUniqid()?>'].getData(), 'other_spell', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#other_spell<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Npc.update('<?=$this->getUniqid()?>', editor.getData(), 'other_spell', IS_VALUE);
                                        }
                                    },
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
                                    CKEDITOR5['other_spell<?=$this->getUniqid()?>'] = newEditor;
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
                    return html_entity_decode($this->_other_spell);
            }
        }
        public function getOther_consomable(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1">
                            <p>Autres consommables</p>
                            <div  id="other_consomable<?=$this->getUniqid()?>"><?=html_entity_decode($this->_other_consomable)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Npc.update('<?=$this->getUniqid()?>', CKEDITOR5['other_consomable<?=$this->getUniqid()?>'].getData(), 'other_consomable', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#other_consomable<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Npc.update('<?=$this->getUniqid()?>', editor.getData(), 'other_consomable', IS_VALUE);
                                        }
                                    },
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
                                    CKEDITOR5['other_consomable<?=$this->getUniqid()?>'] = newEditor;
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
                    return html_entity_decode($this->_other_consomable);
            }
        }

        public function getVisual(int $display = Content::DISPLAY_CARD, int $size = 300){
            $user = ControllerConnect::getCurrentUser();
            $bookmark_icon = "far";
            if($user->in_bookmark($this)){
                $bookmark_icon = "fas";
            }
            $classe = $this->getClasse(Content::FORMAT_OBJECT);

            //OPTIONS
            if($size < 100){$size = 300;}

            switch ($display) {
                case Content::DISPLAY_MODIFY:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <p class='size-0-7 m-1'>PNJ <?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$this->getTimestamp_updated(Content::DATE_FR);?></p>
                                <div class="row m-2">
                                    <div class="col-auto">
                                        <h6 class="text-center">Classe</h6>
                                        <?=$this->getClasse(Content::FORMAT_OBJECT)->getVisual(Content::DISPLAY_RESUME)?>
                                        <p class="mt-4 text-center"><a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=<?=$this->getUniqid()?>'><i class='fas fa-file-pdf'></i> Générer un pdf</a></p>
                                    </div>
                                    <div class="col ms-4">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <p class="text-center"><?=$this->getLevel(Content::FORMAT_MODIFY)?></p>
                                        </div>
                                        <div class="d-flex flex-row">
                                            <?=$this->getAge(Content::FORMAT_MODIFY)?>
                                            <?=$this->getSize(Content::FORMAT_MODIFY)?>
                                            <?=$this->getWeight(Content::FORMAT_MODIFY)?>
                                        </div>
                                        <?=$this->getAlignment(Content::FORMAT_MODIFY)?>
                                        <?=$this->getHistorical(Content::FORMAT_MODIFY)?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Caractèristiques</h4>
                                    <div class="d-flex flex-row justify-content-between">
                                        <?=$this->getLife(Content::FORMAT_MODIFY)?>
                                        <?=$this->getPA(Content::FORMAT_MODIFY)?>
                                        <?=$this->getPM(Content::FORMAT_MODIFY)?>
                                        <?=$this->getPO(Content::FORMAT_MODIFY)?>
                                        <?=$this->getIni(Content::FORMAT_MODIFY)?>
                                        <?=$this->getInvocation(Content::FORMAT_MODIFY)?>
                                        <?=$this->getTouch(Content::FORMAT_MODIFY)?>
                                    </div>
                                    <div class="row justify-content-around">
                                        <div class="col-auto">
                                            <?=$this->getVitality(Content::FORMAT_MODIFY)?>
                                            <?=$this->getSagesse(Content::FORMAT_MODIFY)?>
                                            <?=$this->getStrong(Content::FORMAT_MODIFY)?>
                                            <?=$this->getIntel(Content::FORMAT_MODIFY)?>
                                            <?=$this->getAgi(Content::FORMAT_MODIFY)?>
                                            <?=$this->getChance(Content::FORMAT_MODIFY)?>
                                        </div>
                                        <div class="col-auto">
                                            <?=$this->getCa(Content::FORMAT_MODIFY)?>
                                            <?=$this->getDodge_pa(Content::FORMAT_MODIFY)?>
                                            <?=$this->getDodge_pm(Content::FORMAT_MODIFY)?>
                                            <?=$this->getFuite(Content::FORMAT_MODIFY)?>
                                            <?=$this->getTacle(Content::FORMAT_MODIFY)?>
                                        </div>
                                        <div class="col-auto">
                                            <?=$this->getRes_neutre(Content::FORMAT_MODIFY)?>
                                            <?=$this->getRes_terre(Content::FORMAT_MODIFY)?>
                                            <?=$this->getRes_feu(Content::FORMAT_MODIFY)?>
                                            <?=$this->getRes_air(Content::FORMAT_MODIFY)?>
                                            <?=$this->getRes_eau(Content::FORMAT_MODIFY)?>
                                        </div>
                                    </div>

                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Compétences</h4>
                                    <div class="row">
                                        <div class="col-auto my-2">
                                            <p class="text-agi">Dépendant de l'agilité</p>
                                            <p class="text-grey-d-1 size-0-9 mb-2"><?=$this->getAgi(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                                            <?=$this->getAcrobatie(Content::FORMAT_MODIFY)?>
                                            <?=$this->getDiscretion(Content::FORMAT_MODIFY)?>
                                            <?=$this->getEscamotage(Content::FORMAT_MODIFY)?>
                                            <p class="text-force mt-2">Dépendant de la Force</p>
                                            <p class="text-grey-d-1 size-0-9 mb-2"><?=$this->getStrong(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                                            <?=$this->getAthletisme(Content::FORMAT_MODIFY)?>
                                            <?=$this->getIntimidation(Content::FORMAT_MODIFY)?>
                                        </div>
                                        <div class="col-auto my-2">
                                            <p class="text-intel">Dépendant de l'Intelligence</p>
                                            <p class="text-grey-d-1 size-0-9 mb-2"><?=$this->getIntel(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                                            <?=$this->getArcane(Content::FORMAT_MODIFY)?>
                                            <?=$this->getHistoire(Content::FORMAT_MODIFY)?>
                                            <?=$this->getInvestigation(Content::FORMAT_MODIFY)?>
                                            <?=$this->getNature(Content::FORMAT_MODIFY)?>
                                            <?=$this->getReligion(Content::FORMAT_MODIFY)?>
                                        </div>
                                        <div class="col-auto my-2">
                                            <p class="text-sagesse">Dépendant de la Sagesse</p>
                                            <p class="text-grey-d-1 size-0-9 mb-2"><?=$this->getSagesse(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                                            <?=$this->getDressage(Content::FORMAT_MODIFY)?>
                                            <?=$this->getMedecine(Content::FORMAT_MODIFY)?>
                                            <?=$this->getPerception(Content::FORMAT_MODIFY)?>
                                            <?=$this->getPerspicacite(Content::FORMAT_MODIFY)?>
                                            <?=$this->getSurvie(Content::FORMAT_MODIFY)?>
                                        </div>
                                        <div class="col-auto my-2">
                                            <p class="text-chance">Dépendant de la Chance</p>
                                            <p class="text-grey-d-1 size-0-9 mb-2"><?=$this->getChance(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                                            <?=$this->getPersuasion(Content::FORMAT_MODIFY)?>
                                            <?=$this->getRepresentation(Content::FORMAT_MODIFY)?>
                                            <?=$this->getSupercherie(Content::FORMAT_MODIFY)?>
                                        </div>
                                    </div>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Informations</h4>
                                    <p class="card-text my-2"><?=$this->getStory(Content::FORMAT_MODIFY);?></p>
                                    <p class="card-text my-2"><?=$this->getOther_info(Content::FORMAT_MODIFY);?></p>
                                    <div class="d-flex flex-row justify-content-between my-2">
                                        <?=$this->getDrop_(Content::FORMAT_MODIFY)?>
                                        <?=$this->getKamas(Content::FORMAT_MODIFY)?>
                                    </div>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Sorts</h4>
                                    <p class="card-text my-2"><?=$this->getClasse(Content::FORMAT_OBJECT)->getSpell(Content::FORMAT_BADGE);?></p>
                                    <p class="card-text my-2"><?=$this->getOther_spell(Content::FORMAT_MODIFY);?></p>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Equipements</h4>
                                    <p class="card-text my-2"><?=$this->getOther_equipment(Content::FORMAT_MODIFY);?></p>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Consommables</h4>
                                    <p class="card-text my-2"><?=$this->getOther_consomable(Content::FORMAT_MODIFY);?></p>
                                </div>
                                <p class="text-right font-size-0-8 m-1"><a class='text-red-d-2 text-red-l-3-hover' onclick="Npc.remove('<?=$this->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
                        </div>
                    <?php return ob_get_clean();

                case  Content::DISPLAY_CARD:      
                    ob_start(); ?>
                        <div class="card mb-3">
                                <div class="d-flex flex-row justify-content-between align-items-start m-2">
                                    <div>
                                        <h6 class="text-center">Classe</h6>
                                        <?=$this->getClasse(Content::FORMAT_OBJECT)->getVisual(Content::DISPLAY_RESUME)?>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h4><?=$this->getName()?></h4>
                                            <p class="text-center"><?=$this->getLevel(Content::FORMAT_BADGE)?></p>
                                        </div>
                                        <p>
                                            <?=$this->getAge(Content::FORMAT_BADGE)?>
                                            <?=$this->getSize(Content::FORMAT_BADGE)?>
                                            <?=$this->getWeight(Content::FORMAT_BADGE)?>
                                        </p>
                                        <p><span class="size-0-8 text-grey-d-2">Alignement : </span><?=$this->getAlignment()?></p>
                                        <p><span class="size-0-8 text-grey-d-2">Historique : </span><?=$this->getHistorical()?></p>
                                    </div>
                                    <div class="m-2">
                                        <?php if($user->getRight('npc', User::RIGHT_WRITE)){ ?>
                                            <p><a class='text-main-d-2 text-main-l-3-hover' onclick="Npc.open('<?=$this->getUniqid()?>', Controller.DISPLAY_MODIFY)"><i class='far fa-edit'></i> Modifier</a></p>
                                        <?php } ?>
                                        <p><a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=<?=$this->getUniqid()?>'><i class='fas fa-file-pdf'></i> Générer un pdf</a></p>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Caractèristiques</h4>
                                    <div class="m-1 d-flex flex-row justify-content-between align-items-center">
                                        <p class="m-1"><?=$this->getLife(Content::FORMAT_VIEW)?></p>
                                        <p class="m-1"><?=$this->getPA(Content::FORMAT_VIEW)?></p>
                                        <p class="m-1"><?=$this->getPM(Content::FORMAT_VIEW)?></p>
                                        <p class="m-1"><?=$this->getPO(Content::FORMAT_VIEW)?></p>
                                    </div>
                                    <div class="m-1 mb-2 d-flex flex-row justify-content-between align-items-center">
                                        <p class="m-1"><?=$this->getIni(Content::FORMAT_VIEW)?></p>
                                        <p class="m-1"><?=$this->getInvocation(Content::FORMAT_VIEW)?></p>
                                        <p class="m-1"><?=$this->getTouch(Content::FORMAT_VIEW)?></p>
                                    </div>
                                    <div class="row justify-content-around">
                                        <div class="col-auto">
                                            <p class="m-1"><?=$this->getVitality(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getSagesse(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getStrong(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getIntel(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getAgi(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getChance(Content::FORMAT_VIEW)?></p>
                                        </div>
                                        <div class="col-auto">
                                            <p class="m-1"><?=$this->getCa(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getDodge_pa(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getDodge_pm(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getFuite(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getTacle(Content::FORMAT_VIEW)?></p>
                                        </div>
                                        <div class="col-auto">
                                            <p class="m-1"><?=$this->getRes_neutre(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getRes_terre(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getRes_feu(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getRes_air(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getRes_eau(Content::FORMAT_VIEW)?></p>
                                        </div>
                                    </div>

                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Compétences</h4>
                                    <div class="row">
                                        <div class="col-auto my-2">
                                            <p class="text-agi mb-2">Dépendant de l'agilité</p>
                                            <p class="m-1"><?=$this->getAcrobatie(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getDiscretion(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getEscamotage(Content::FORMAT_VIEW)?></p>
                                            <p class="text-force my-2">Dépendant de la Force</p>
                                            <p class="m-1"><?=$this->getAthletisme(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getIntimidation(Content::FORMAT_VIEW)?></p>
                                        </div>
                                        <div class="col-auto mb-2 mt-3">
                                            <p class="text-intel mb-2">Dépendant de l'Intelligence</p>
                                            <p class="m-1"><?=$this->getArcane(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getHistoire(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getInvestigation(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getNature(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getReligion(Content::FORMAT_VIEW)?></p>
                                        </div>
                                        <div class="col-auto my-2">
                                            <p class="text-sagesse mb-2">Dépendant de la Sagesse</p>
                                            <p class="m-1"><?=$this->getDressage(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getMedecine(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getPerception(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getPerspicacite(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getSurvie(Content::FORMAT_VIEW)?></p>
                                        </div>
                                        <div class="col-auto my-2">
                                            <p class="text-chance mb-2">Dépendant de la Chance</p>
                                            <p class="m-1"><?=$this->getPersuasion(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getRepresentation(Content::FORMAT_VIEW)?></p>
                                            <p class="m-1"><?=$this->getSupercherie(Content::FORMAT_VIEW)?></p>
                                        </div>
                                    </div>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Informations</h4>
                                    <p class="card-text"><?=$this->getStory();?></p>
                                    <p class="card-text"><?=$this->getOther_info();?></p>
                                    <div class="d-flex flex-row justify-content-between">
                                        <?=$this->getDrop_()?>
                                        <?=$this->getKamas()?>
                                    </div>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Sorts</h4>
                                    <p class="card-text"><?=$this->getClasse(Content::FORMAT_OBJECT)->getSpell(Content::FORMAT_LIST);?></p>
                                    <p class="card-text"><?=$this->getOther_spell();?></p>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Equipements</h4>
                                    <p class="card-text"><?=$this->getOther_equipment();?></p>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <h4 class="text-main-d-1 text-center">Consommables</h4>
                                    <p class="card-text"><?=$this->getOther_consomable();?></p>
                                </div>
                        </div>
                    <?php return ob_get_clean();

                case Content::DISPLAY_RESUME:
                    ob_start(); ?>
                        <div style="width: <?=$size?>px;">
                            <div style="position:relative;">
                                <div ondblclick="Npc.open('<?=$this->getUniqid()?>');" class="card-hover-linked card p-2 m-1 border-secondary-d-2 border" style="width: <?=$size?>px;" >
                                    <div class="d-flex flew-row flex-nowrap justify-content-start">
                                        <div>
                                            <?=$classe->getPath_img(Content::FORMAT_IMAGE, "img-back-50")?>
                                        </div>
                                        <div class="m-1">
                                            <p class="bold ms-1"><?=$this->getName()?></p>
                                            <div class="d-flex align-items-baseline">
                                                <h6>Classe : <?=$this->getClasse(Content::FORMAT_OBJECT)->getName()?></h6>
                                                <div class="ms-3 text-center short-badge-150"><?=$this->getLevel(Content::FORMAT_BADGE)?></div>
                                            </div>
                                        </div>
                                        <div class="ms-auto align-self-end">
                                            <a onclick='User.changeBookmark(this);' data-classe='npc' data-uniqid='<?=$this->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                                            <p><a class="btn-text-secondary" title="Afficher les sorts" onclick="Classe.getSpellList('<?=$classe->getUniqid()?>');"><i class="fas fa-magic"></i></a></p>
                                            <p><a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=<?=$this->getUniqid()?>'><i class='fas fa-file-pdf'></i></a></p>
                                        </div>
                                    </div>
                                    <div class="justify-content-center d-flex short-badge-150 flex-wrap"><?=$this->getTrait(Content::FORMAT_BADGE)?></div>
                                    <div class="card-hover-showed">
                                        <p class="size-0-8 short-badge-150">
                                            <?=$this->getAge(Content::FORMAT_BADGE)?>
                                            <?=$this->getSize(Content::FORMAT_BADGE)?>
                                            <?=$this->getWeight(Content::FORMAT_BADGE)?>
                                        </p>
                                        <p class="size-0-8"><span class="size-0-8 text-grey-d-2">Alignement : </span><?=$this->getAlignment()?></p>
                                        <p class="size-0-8"><span class="size-0-8 text-grey-d-2">Historique : </span><?=$this->getHistorical()?></p>
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
                                        <div class="row">
                                            <div class="col-auto my-1">
                                                <p class="m-1"><?=$this->getAcrobatie(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getDiscretion(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getEscamotage(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getAthletisme(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getIntimidation(Content::FORMAT_VIEW)?></p>
                                            </div>
                                            <div class="col-auto my-1">
                                                <p class="m-1"><?=$this->getArcane(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getHistoire(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getInvestigation(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getNature(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getReligion(Content::FORMAT_VIEW)?></p>
                                            </div>
                                            <div class="col-auto my-1">
                                                <p class="m-1"><?=$this->getDressage(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getMedecine(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getPerception(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getPerspicacite(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getSurvie(Content::FORMAT_VIEW)?></p>
                                            </div>
                                            <div class="col-auto my-1">
                                                <p class="m-1"><?=$this->getPersuasion(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getRepresentation(Content::FORMAT_VIEW)?></p>
                                                <p class="m-1"><?=$this->getSupercherie(Content::FORMAT_VIEW)?></p>
                                            </div>
                                    </div>
                                        <p class="card-text text-grey-d-2"><?=$this->getStory();?></p>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                            <p class="card-text text-grey"><?=$this->getOther_info();?></p>
                                            <div class="d-flex flex-row justify-content-between">
                                                <?=$this->getDrop_()?>
                                                <?=$this->getKamas()?>
                                            </div>
                                        </div>
                                </div>
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
        public function setClasse($data){
            $this->_classe = $data;
            return true;
        }
        public function setStory($data){
            $this->_story = $data;
            return true;
        }
        public function setHistorical($data){
            $this->_historical = $data;
            return true;
        }
        public function setAlignment($data){
            $this->_alignment = $data;
            return true;
        }
        public function setLevel($data){
            $this->_level = $data;
            return true;
        }
        public function setTrait($data){
            $this->_trait = $data;
            return true;
        }
        public function setOther_info($data){
            $this->_other_info = $data;
            return true;
        }
        public function setAge($data){
            $this->_age = $data;
            return true;
        }
        public function setSize($data){
            $this->_size = $data;
            return true;
        }
        public function setWeight($data){
            $this->_weight = $data;
            return true;
        }
        public function setLife($data){
            $this->_life = $data;
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
        public function setInvocation($data){
            $this->_invocation = $data;
            return true;
        }
        public function setTouch($data){
            $this->_touch = $data;
            return true;
        }
        public function setCa($data){
            $this->_ca = $data;
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
        public function setFuite($data){
            $this->_fuite = $data;
            return true;
        }
        public function setTacle($data){
            $this->_tacle = $data;
            return true;
        }
        public function setVitality($data){
            $this->_vitality = $data;
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
        public function setAcrobatie($data){
            $this->_acrobatie = $data;
            return true;
        }
        public function setDiscretion($data){
            $this->_discretion = $data;
            return true;
        }
        public function setEscamotage($data){
            $this->_escamotage = $data;
            return true;
        }
        public function setAthletisme($data){
            $this->_athletisme = $data;
            return true;
        }
        public function setIntimidation($data){
            $this->_intimidation = $data;
            return true;
        }
        public function setArcane($data){
            $this->_arcane = $data;
            return true;
        }
        public function setHistoire($data){
            $this->_histoire = $data;
            return true;
        }
        public function setInvestigation($data){
            $this->_investigation = $data;
            return true;
        }
        public function setNature($data){
            $this->_nature = $data;
            return true;
        }
        public function setReligion($data){
            $this->_religion = $data;
            return true;
        }
        public function setDressage($data){
            $this->_dressage = $data;
            return true;
        }
        public function setMedecine($data){
            $this->_medecine = $data;
            return true;
        }
        public function setPerception($data){
            $this->_perception = $data;
            return true;
        }
        public function setPerspicacite($data){
            $this->_perspicacite = $data;
            return true;
        }
        public function setSurvie($data){
            $this->_survie = $data;
            return true;
        }
        public function setPersuasion($data){
            $this->_persuasion = $data;
            return true;
        }
        public function setRepresentation($data){
            $this->_representation = $data;
            return true;
        }
        public function setSupercherie($data){
            $this->_supercherie = $data;
            return true;
        }
        public function setKamas($data){
            $this->_kamas = $data;
            return true;
        }
        public function setDrop_($data){
            $this->_drop_ = $data;
            return true;
        }
        public function setOther_equipement($data){
            $this->_other_consomable = $data;
            return true;
        }
        public function setOther_consomable($data){
            $this->_other_consomable = $data;
            return true;
        }
        public function setOther_spell($data){
            $this->_other_spell = $data;
            return true;
        }
}