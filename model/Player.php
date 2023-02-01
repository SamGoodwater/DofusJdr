<?php
class Player extends Content
{
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }

    public function hydrate(array $donnees){
        foreach($donnees as $champ => $valeur){
          $method = "set".ucfirst($champ);

          if(method_exists($this,$method))
          {
              $this->$method($this->securite($valeur));
          }
        }
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_id ='';
        private $_uniqid ='';
        private $_timestamp_add='';
        private $_timestamp_updated='';
        private $_name='';
        private $_class='';
        private $_story='';
        private $_historical='';
        private $_alignment='';
        private $_level='';
        private $_trait='';
        private $_other_info='';
        
        private $_age='';
        private $_height='';
        private $_weight='';

        private $_bonus=false;
        private $_malus=false;
        private $_life_max="";
        private $_life="";
        private $_shield="";
        private $_temporary_life="";

        private $_pa="";
        private $_pm="";
        private $_po="";
        private $_ini="";
        private $_invocation="";
        private $_touch="";
        private $_CA="";
        private $_vitality="";
        private $_strong="";
        private $_intel="";
        private $_agi="";
        private $_chance="";
        private $_dodge_pa="";
        private $_dodge_pm="";
        private $_res_neutre="";
        private $_res_terre="";
        private $_res_feu="";
        private $_res_air="";
        private $_res_eau="";

        private $_acrobaties='';
        private $_discrétion='';
        private $_escamotage='';
        private $_athlétisme='';
        private $_intimidation='';
        private $_arcanes='';
        private $_histoire='';
        private $_investigation='';
        private $_nature='';
        private $_religion='';
        private $_ressage='';
        private $_medecine='';
        private $_perception='';
        private $_perspicacité='';
        private $_survie='';
        private $_persuasion='';
        private $_représentation='';
        private $_supercherie='';

        private $_kamas='';

        // other_equipment='';
        // other_consomable='';
        // other_spell='';
        
        // équipement
        // consomable
        // Spell

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getId(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_BADGE:
                    return "<span class='badge bg-secondary' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Identifiant de la joueuse ou du joueur N°{$this->_id}'>N°{$this->_id}</span>";
                
                default:
                    return $this->_id;
            }
        }
        public function getUniqid(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_BADGE:
                    return "<span class='badge bg-secondary' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Identifiant unique de la joueuse ou du joueur N°{$this->_uniqid}'>N°{$this->_uniqid}</span>";
                
                default:
                    return $this->_uniqid;
            }
        }
        public function getTimestamp_add($format=NULL){
            if(!empty($format)){
              return date($format, $this->_timestamp_add);
            } else {
                return $this->_timestamp_add;
            }
        }
        public function getTimestamp_updated($format=NULL){
            if(!empty($format)){
              return date($format, $this->_timestamp_updated);
            } else {
                return $this->_timestamp_updated;
            }
        }
        public function getName(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                            <input 
                                onchange="Player.update('<?=$this->getUniqid();?>', this, 'name');" 
                                placeholder="Nom de la joueuse ou du joueur" 
                                maxlength="50"
                                type="text" 
                                class="form-control form-control-sm" 
                                value="<?=$this->_name?>">
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
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Player.update('<?=$this->getUniqid()?>', CKEDITOR5['description<?=$this->getUniqid()?>'].getData(), 'description', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#description<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Player.update('<=$this->getUniqid()?>', editor.getData(), 'description', IS_VALUE);
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

        public function getTrait(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <input 
                                onchange="Player.update('<?=$this->getUniqid();?>', this, 'trait');" 
                                placeholder="Traits du joueur" 
                                maxlength="3000" 
                                type="text" 
                                class="form-control" 
                                value="<?=$this->_trait?>">
                            <label class="size-0-8">Traits de la joueuse ou du joueur</label>
                        </div>
                        <span class="size-0-8 text-grey">Séparer les différents traits par des virgules.</span>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-around"> <?php
                            foreach (explode(",", $this->_trait) as $trait) { ?>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Trait <?=$trait?>" class="badge back-main-d-1"><?=$trait?></span>
                            <?php } ?>                            
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_trait;
            }

        }


        public function getPa(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-pa">
                            <div class="input-group">
                                <div class="input-group-text back-pa text-white"><img class='icon' src='medias/icons/pa.png'></div>
                                <input 
                                    onchange="Mob.update('<?=$this->getUniqid();?>', this, 'pa');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Tranche de point d'action de la joueuse ou du joueur"
                                    type="text" 
                                    class="form-control form-control-sm" 
                                    value="<?=$this->_pa?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-pa' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Points d'action de la joueuse ou du joueur\">PA {$this->_pa}</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pa' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Points d'action de la joueuse ou du joueur\">{$this->_pa} <img class='icon' src='medias/icons/pa.png'></span>";
                
                default:
                    return $this->_pa;
            }
        }

        public function getVisual(int $format = Content::FORMAT_BRUT){

            switch ($format) {
                case Content::FORMAT_MODIFY:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-2"><?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-200H-allL")?></div>
                                <div class="col-md-10">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="text-main-l-2 size-0-8">Arme priviligiée :</p>
                                                <?=$this->getWeapons_of_choice(Content::FORMAT_MODIFY)?>
                                                <?=$this->getTrait(Content::FORMAT_MODIFY)?>
                                            </div>
                                            <div class="col-6">
                                                <?=$this->getUsable(Content::FORMAT_MODIFY)?>
                                            </div>
                                        </div>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                        <p class='size-0-7 mb-1'>Classe <?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$this->getTimestamp_updated(Content::DATE_FR);?></p>
                                        <p class="card-text"><?=$this->getDescription_fast(Content::FORMAT_MODIFY);?></p>
                                        <p class="card-text"><?=$this->getDescription(Content::FORMAT_MODIFY);?></p>
                                        <p class="card-text"><?=$this->getLife(Content::FORMAT_MODIFY);?></p>
                                        <p class="card-text"><?=$this->getSpecificity(Content::FORMAT_MODIFY);?></p>
                                        <p class="card-text"><?=$this->getSpell(Content::FORMAT_MODIFY);?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                break;

                case  Content::FORMAT_CARD:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-2">
                                    <a style="position:relative;top:5px;left:5px;" href="<?=$this->getPath_img()?>" download="<?=$this->getName().'.'.substr(strrchr($this->getPath_img(),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
                                    <?=$this->getPath_img(Content::FORMAT_FANCY, "img-back-200H-allL")?>
                                </div>
                                <div class="col-md-10">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-10">
                                                <?=$this->getTrait(Content::FORMAT_BADGE)?>
                                                <p class="text-main-l-2 size-0-8">Arme priviligiée :</p>
                                                <?=$this->getWeapons_of_choice(Content::FORMAT_BADGE)?>
                                            </div>
                                            <div class="col-2"><?=$this->getUsable(Content::FORMAT_MODIFY)?></div>                      
                                        </div>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                        <h5 class="card-title"><?=$this->getName()?></h5>
                                        <p class="card-text"><small class="text-muted"><?=$this->getDescription_fast()?></small></p>
                                        <p class="card-text"><?=$this->getDescription()?></p>
                                        <p class="text-main-d-3 text-bold mt-2">Spécificités de la classe</p>
                                        <p class="card-text"><?=$this->getSpecificity()?></p>
                                        <p class="text-main-d-3 text-bold mt-2">Gestion des points de vie</p>
                                        <p class="card-text"><?=$this->getLife()?></p>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                        <p class="card-text"><?=$this->getSpell(Content::FORMAT_LIST)?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                break;
            }

        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setId($data){
            if($data > 0){
                $this->_id = $data;
                return "success";
            } else {
                return "Id est incorrect";
            }
        }
        public function setUniqid($data){
            $this->_uniqid = $data;
            return "success";
        }    
        public function setTimestamp_add($data = ''){
            if(empty($data)){
                $this->_timestamp_add = time();
            } else {
                $date = new DateTime();
                if($this->isTimestamp($data)){
                    $date->setTimestamp(intval($data));
                }else {
                    try {
                        $date = new DateTime($data);
                    } catch (\Exception $e) {
                        return 'La date de création est incorrecte (format ?)';
                    }
                }
                $this->_timestamp_add = $date->format('U');
                return "success";
            }
        }
        public function setTimestamp_updated($data = ''){
            if(empty($data)){
                $this->_timestamp_updated = time();
            } else {
                $date = new DateTime();
                if($this->isTimestamp($data)){
                    $date->setTimestamp(intval($data));
                }else {
                    try {
                        $date = new DateTime($data);
                    } catch (\Exception $e) {
                        return 'La date de création est incorrecte (format ?)';
                    }
                }
                $this->_timestamp_updated = $date->format('U');
                return "success";
            }
        }
        public function setName($data){
            $this->_name = $data;
            return "success";
        }
        public function setDescription($data){
            $this->_description = $data;
            return "success";
        }
        public function setTrait($data){
            $this->_trait = $data;
            return "success";
        }
}
