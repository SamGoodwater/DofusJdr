<?php
class Classe extends Content
{
    public function __construct(array $donnees){
        $this->hydrate($donnees);

        if(file_exists("medias/classes/".strtolower($this->getName()).".png")){
            $this->setPath_image("medias/classes/".strtolower($this->getName()).".png");
        }
        if(file_exists("medias/classes/".strtolower($this->getName())."_logo.svg")){
            $this->setPath_image_logo("medias/classes/".strtolower($this->getName())."_logo.svg");
        }
    } 

    const FECA = "62057f83d450d";
    const OSAMODAS = "62057fc39235f";
    const ENUTROF = "6205855578057";
    const SRAM = "6205857244fbd";
    const XELOR = "6205858631aa6";
    const ECAFLIP = "62058599d17f3";
    const ENIRIPSA = "620585bf304c8";
    const IOP = "620585d2cde72";
    const CRA = "620585e242898";
    const SADIDA = "6205864dd3d78";
    const SACRIER = "6205866608915";
    const PANDAWA = "6205867381210";

    const WEAPONS = [
        "hache" => 0,
        "arc" => 1,
        "dague" => 2,
        "marteau" => 3,
        "pelle" => 4,
        "baton" => 5,
        "épée" => 6,
        "baguette" => 7,
        "mains" => 8
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_description_fast='';
        private $_description='';
        private $_life='';
        private $_specificity='';
        private $_weapons_of_choice='';
        private $_trait="";
        private $_path_img_logo="medias/classe/default_logo.svg";
        private $_path_img="medias/classe/default.png";
        private $_usable=false;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥

        public function getName(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <input 
                                onchange="Classe.update('<?=$this->getUniqid();?>', this, 'name');" 
                                placeholder="Nom de la classe" 
                                maxlength="300" 
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_name?>">
                            <label class="size-0-8">Nom de la classe</label>
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_name;
            }
        }
        public function getDescription_fast(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <textarea 
                                placeholder=""
                                onchange="Classe.update('<?=$this->getUniqid();?>', this, 'description_fast');" 
                                class="form-control form-control-main-focus" 
                                maxlength="500"><?=$this->_description_fast?></textarea>
                            <label class="size-0-8">Description succincte</label>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_description_fast;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1">
                            <p>Description</p>
                            <div  id="description<?=$this->getUniqid()?>"><?=html_entity_decode($this->_description)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Classe.update('<?=$this->getUniqid()?>', CKEDITOR5['description<?=$this->getUniqid()?>'].getData(), 'description', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#description<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Classe.update('<?=$this->getUniqid()?>', editor.getData(), 'description', IS_VALUE);
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
        public function getLife(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1">
                            <p>Gestion de la vitalité</p>
                            <div  id="life<?=$this->getUniqid()?>"><?=html_entity_decode($this->_life)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Classe.update('<?=$this->getUniqid()?>', CKEDITOR5['life<?=$this->getUniqid()?>'].getData(), 'life', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#life<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Classe.update('<?=$this->getUniqid()?>', editor.getData(), 'life', IS_VALUE);
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
                                    CKEDITOR5['life<?=$this->getUniqid()?>'] = newEditor;
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
                    return html_entity_decode($this->_life);
            }
        }
        public function getSpecificity(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1">
                            <p>Spécificités de la classe</p>
                            <div  id="specificity<?=$this->getUniqid()?>"><?=html_entity_decode($this->_specificity)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Classe.update('<?=$this->getUniqid()?>', CKEDITOR5['specificity<?=$this->getUniqid()?>'].getData(), 'specificity', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#specificity<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Classe.update('<?=$this->getUniqid()?>', editor.getData(), 'specificity', IS_VALUE);
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
                                    CKEDITOR5['specificity<?=$this->getUniqid()?>'] = newEditor;
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
                    return html_entity_decode($this->_specificity);
            }
        }
        public function getWeapons_of_choice(int $format = Content::FORMAT_BRUT){
            $path = "medias/weapons/".array_search($this->_weapons_of_choice, Classe::WEAPONS).".svg";

            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="dropdown">
                            <a class="d-flex align-items-center" type="button" id="dropdownDisplay<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getWeapons_of_choice(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="dropdownDisplay<?=$this->getId()?>"> <?php
                                foreach (Classe::WEAPONS as $name => $weapons) { ?>
                                    <a class="dropdown-item" onclick="Classe.update('<?=$this->getUniqid()?>', <?=$weapons?>, 'weapons_of_choice', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-blue-d-2'><?=$name?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php return ob_get_clean();
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_weapons_of_choice, Classe::WEAPONS)){
                        return "<div class='d-flex pe-1'><div data-bs-toggle='tooltip' data-bs-placement='bottom' title='Arme de prédilection'><img class='icon me-1' src='".$path."'>".array_search($this->_weapons_of_choice, Classe::WEAPONS)."</div>";
                    } else  {
                        return "";
                    }

                case Content::FORMAT_ICON:
                    if(in_array($this->_weapons_of_choice, Classe::WEAPONS)){
                        return "<div data-bs-toggle='tooltip' data-bs-placement='bottom' title='Arme de prédilection'><img class='icon' src='".$path."'></div>";
                    } else  {
                        return "";
                    }

                case Content::FORMAT_PATH:
                    if(in_array($this->_weapons_of_choice, Classe::WEAPONS)){
                        return $path;
                    } else  {
                        return "";
                    }

                default:
                    return $this->_weapons_of_choice;
            }
        }
        public function getTrait(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <input 
                                onchange="Classe.update('<?=$this->getUniqid();?>', this, 'trait');" 
                                placeholder="Traits de la classe" 
                                maxlength="3000" 
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_trait?>">
                            <label class="size-0-8">Traits de la classe</label>
                        </div>
                        <span class="size-0-8 text-grey">Séparer les différents traits par des virgules.</span>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-around"> <?php
                            foreach (explode(",", $this->_trait) as $trait) { ?>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Trait <?=$trait?>" class="badge back-<?=View::getColorFromLetter($trait)?>-d-1"><?=$trait?></span>
                            <?php } ?>                            
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_trait;
            }

        }
        public function getPath_img(int $format = Content::FORMAT_BRUT, $css = ""){
            if(file_exists("medias/classes/".strtolower($this->getName()).".png")){
                $this->setPath_image("medias/classes/".strtolower($this->getName()).".png");
            }
            
            if(!empty($this->_path_img) || file_exists($this->_path_img)){
                $path = $this->_path_img;
            } else {
                $path = "medias/mobs/default.png";
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
        public function getPath_img_logo(int $format = Content::FORMAT_BRUT, $css = ""){
            if(file_exists("medias/classes/".strtolower($this->getName())."_logo.svg")){
                $this->setPath_image("medias/classes/".strtolower($this->getName())."_logo.svg");
            }
            
            if(!empty($this->_path_img_logo) || file_exists($this->_path_img_logo)){
                $path = $this->_path_img_logo;
            } else {
                $path = "medias/mobs/default_logo.svg";
            }

            switch ($format) {
                case  Content::FORMAT_BRUT:
                    return $path;
                break;
                case  Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="text-center">
                            <div id='showFile_path_image' class='m-2 d-flex justify-content-center'><?=$this->getPath_img_logo(Content::FORMAT_FANCY, 'img-back-180');?></div>
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
                            <input onchange="Classe.update('<?=$this->getUniqid();?>', this, 'usable', <?=Controller::IS_CHECKBOX?>);"  type="checkbox" class="form-check-input back-main-d-1 border-main-d-1" <?=$checked?> id="customSwitchUsable<?=$this->getId()?>">
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
        public function getSpell(int $format = Content::FORMAT_BRUT, $display_remove = false, $size = 300){
            $manager = new ClasseManager();
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
                                        data-action = <?=ControllerSearch::SEARCH_DONE_ADD_SPELL_TO_CLASSE?>
                                        data-limit = 10
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
                                        <div style="position:relative;width:<?=$size?>px;">
                                            <?php if($display_remove){ ?>
                                                <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                                                    <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher ce sort de cette classe" class="p-4 <?=View::getCss(View::TYPE_BTN_UNDERLINE, "red")?>" onclick="if (confirm('Etes vous sûr d\'étacher le sort de cette classe ?')){Classe.update('<?=$this->getUniqid()?>',{action:'remove', uniqid:'<?=$spell->getUniqid()?>'},'spell', IS_VALUE);}"><i class="fas fa-times"></i></a>
                                                </div>
                                            <?php } ?>
                                            <?= $spell->getVisual(Content::DISPLAY_RESUME);?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php }
                    return ob_get_clean();
                    
                case Content::FORMAT_ARRAY:
                    return $spells;
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
                                <div class="col-auto"><?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-200")?></div>
                                <div class="col">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <p class="text-main-l-2 size-0-8">Arme priviligiée :</p>
                                                <?=$this->getWeapons_of_choice(Content::FORMAT_MODIFY)?>
                                                <?=$this->getTrait(Content::FORMAT_MODIFY)?>
                                            </div>
                                            <div class="col-auto">
                                                <?=$this->getUsable(Content::FORMAT_MODIFY)?>
                                            </div>
                                        </div>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                        <p class='size-0-7 mb-2'>Classe <?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$this->getTimestamp_updated(Content::DATE_FR);?></p>
                                        <p class="card-text my-2"><?=$this->getDescription_fast(Content::FORMAT_MODIFY);?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-0 mx-4">
                                <p class="card-text my-2"><?=$this->getDescription(Content::FORMAT_MODIFY);?></p>
                                <p class="card-text my-2"><?=$this->getLife(Content::FORMAT_MODIFY);?></p>
                                <p class="card-text my-2"><?=$this->getSpecificity(Content::FORMAT_MODIFY);?></p>
                                <p class="card-text my-2"><?=$this->getSpell(Content::FORMAT_MODIFY);?></p>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                break;

                case  Content::DISPLAY_CARD:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-auto">
                                    <a style="position:relative;top:5px;left:5px;" href="<?=$this->getPath_img()?>" download="<?=$this->getName().'.'.substr(strrchr($this->getPath_img(),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
                                    <?=$this->getPath_img(Content::FORMAT_FANCY, "img-back-200")?>
                                </div>
                                <div class="col">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <?=$this->getTrait(Content::FORMAT_BADGE)?>
                                                <p class="text-main-l-2 size-0-8">Arme priviligiée :</p>
                                                <?=$this->getWeapons_of_choice(Content::FORMAT_BADGE)?>
                                            </div>
                                            <div class="col-auto ms-auto">
                                                <?=$this->getUsable(Content::FORMAT_BADGE)?>
                                                <?php if($user->getRight('classe', User::RIGHT_WRITE)){ ?>
                                                    <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Classe.open('<?=$this->getUniqid()?>', Controller.DISPLAY_MODIFY);"><i class='far fa-edit'></i></a>
                                                <?php } ?>
                                            </div>                      
                                        </div>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                        <h5 class="card-title"><?=$this->getName()?></h5>
                                        <p class="card-text"><small class="text-muted"><?=$this->getDescription_fast()?></small></p>
                                        <p class="card-text"><?=$this->getDescription()?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-0 mx-4">
                                <p class="text-main-d-2 size-1-2 text-bold mt-4 mb-2">Spécificités de la classe</p>
                                <p class="card-text"><?=$this->getSpecificity()?></p>
                                <p class="text-main-d-2 size-1-2 text-bold mt-4 mb-2">Gestion des points de vie</p>
                                <p class="card-text"><?=$this->getLife()?></p>
                                <div class="nav-item-divider back-main-d-1"></div>
                                <p class="card-text"><?=$this->getSpell(Content::DISPLAY_RESUME)?></p>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                break;
                
                case Content::DISPLAY_RESUME:
                    ob_start(); ?>
                        <div style="width: <?=$size?>px;">
                            <div style="position:relative;">
                                <div ondblclick="Classe.open('<?=$this->getUniqid()?>');" class="card-hover-linked card border-secondary-d-2 border p-2 m-1" style="width: <?=$size?>px;" >
                                    <div class="d-flex flew-row flex-nowrap justify-content-start">
                                        <div>
                                            <?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-50")?>
                                        </div>
                                        <div class="mx-2 d-flex flex-column justify-content-between">
                                            <div class="d-flex flex-nowrap justify-content-between">
                                                <p class="bold ms-1"><?=$this->getName()?></p>
                                                <div class="me-1"><?=$this->getWeapons_of_choice(Content::FORMAT_ICON)?></div>
                                            </div>
                                            <div class="size-0-7 text-grey-d-2"><?=$this->getDescription_fast()?></div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-between ms-auto">
                                            <a onclick='User.changeBookmark(this);' data-classe='classe' data-uniqid='<?=$this->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                                            <p class="align-self-end"><a class="btn-text-secondary" title="Afficher les sorts" onclick="Classe.getSpellList('<?=$this->getUniqid()?>');"><i class="fas fa-magic"></i></a></p>
                                            <a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=classe&a=getPdf&uniqid=<?=$this->getUniqid()?>'><i class='fas fa-file-pdf'></i></a>
                                        </div>
                                    </div>
                                    <div class="justify-content-center d-flex short-badge-150 flex-wrap"><?=$this->getTrait(Content::FORMAT_BADGE)?></div>
                                    <div class="card-hover-showed">
                                        <p class="card-text"><small class="text-muted"><?=$this->getDescription_fast()?></small></p>
                                        <p class="card-text"><?=$this->getDescription()?></p>
                                        <p class="text-main-d-2 size-1-2 text-bold mt-4 mb-2">Spécificités de la classe</p>
                                        <p class="card-text"><?=$this->getSpecificity()?></p>
                                        <p class="text-main-d-2 size-1-2 text-bold mt-4 mb-2">Gestion des points de vie</p>
                                        <p class="card-text"><?=$this->getLife()?></p>
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
        
    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥S
        public function setName($data){
            $this->_name = $data;
            return true;
        }
        public function setDescription_fast($data){
            $this->_description_fast = $data;
            return true;
        }
        public function setDescription($data){
            $this->_description = $data;
            return true;
        }
        public function setSpecificity($data){
            $this->_specificity = $data;
            return true;
        }
        public function setLife($data){
            $this->_life = $data;
            return true;
        }
        public function setWeapons_of_choice($data){
            $this->_weapons_of_choice = $data;
            return true;
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
        public function setPath_image_logo($data){
            if(is_file($data)){
                $file = New File($data);
                if(FileManager::isImage($file)){
                    $this->_path_img_logo = $data;
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
            Js : Classe.update(UniqidM,{action:'add|remove|update', uniqid:'uniqIdS'},'spell', IS_VALUE);
        */
        public function setSpell(array $data){ 
            $managerC = new ClasseManager;
            $managerS = new SpellManager;
            if(!isset($data['uniqid'])){return "L'uniqid du sort n'est pas défini";}
            if($managerS->existsUniqid($data['uniqid'])){
                $spell = $managerS->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            if($managerC->addLinkSpell($this, $spell)){
                                return true;
                            }else{
                                return "Erreur lors de l'ajout du sort";
                            }
               
                        case "remove":
                            if($managerC->removeLinkSpell($this, $spell)){
                                return true;
                            }else{
                                return "Erreur lors de la suppression du sort";
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
