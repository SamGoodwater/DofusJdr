<?php
class Shop extends Content
{
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ CONSTANTES ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public const PRICE = [
            "Très peu cher" => 1,
            "Peu cher" => 2,
            "Normal" => 0,
            "Cher" => 3,
            "Très cher" => 4
        ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='Hôtel de vente';
        private $_description='';
        private $_location='';
        private $_price=Shop::PRICE["Normal"];
        private $_id_seller = "";

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Nom de l'hôtel de vente</p>
                            <input 
                                onchange="Shop.update('<?=$this->getUniqid();?>', this, 'name');" 
                                placeholder="Nom de l'hôtel de vente" 
                                maxlength="100"
                                type="text" 
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                value="<?=$this->_name?>">
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
                            <p>Description de l'hôtel de vente</p>
                            <div  id="description<?=$this->getUniqid()?>"><?=html_entity_decode($this->_description)?></div>
                            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' onclick="Shop.update('<?=$this->getUniqid()?>', CKEDITOR5['description<?=$this->getUniqid()?>'].getData(), 'description', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                        </div>
                        <script>
                            ClassicEditor
                                .create( document.querySelector('#description<?=$this->getUniqid()?>'), { 
                                    autosave: {
                                        waitingTime: 10000, // in ms
                                        save(editor) {
                                            Shop.update('<?=$this->getUniqid()?>', editor.getData(), 'description', IS_VALUE);
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
        public function getLocation(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Localisation de l'hôtel de vente</p>
                            <input 
                                onchange="Shop.update('<?=$this->getUniqid();?>', this, 'location');" 
                                placeholder="Localisation de l'hôtel de vente" 
                                maxlength="500"
                                type="text" 
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                value="<?=$this->_location?>">
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_ICON:
                    ob_start(); ?>
                        <span><i class="fas fa-map-marker-alt"></i> <?=$this->_location?></span>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_location;
            }
        }
        public function getPrice(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <p>Moyenne des prix de l'hôtel de vente</p>
                        <div class="dropdown">
                            <a class="" type="button" id="price<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getPrice(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="price<?=$this->getId()?>"> <?php
                                foreach (Shop::PRICE as $name => $price) { ?>
                                    <a class="dropdown-item" onclick="Shop.update('<?=$this->getUniqid()?>', <?=$price?>, 'price', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-main-d-2'><?=$name?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php return ob_get_clean();
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_price, Shop::PRICE)){
                        return "<span class='badge back-main-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Moyenne des prix de l'hôtel de vente\">".array_search($this->_price, Shop::PRICE)."</span>";
                    } else  {
                        return "";
                    }

                default:
                    return $this->_price;
            }
        }
        public function getId_seller(int $format = Content::FORMAT_BRUT){
            $manager = new NpcManager();
            if($manager->existsId($this->_id_seller)){
                $npc = $manager->getFromId($this->_id_seller);
            }

            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <p>Marchand·e de l'hôtel de vente (PNJ)</p>
                        <div class="dropdown">
                            <a class="" type="button" id="id_seller<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getId_seller(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="id_seller<?=$this->getId()?>"> <?php
                                foreach ($manager->getAll() as $npc) { ?>
                                    <a class="dropdown-item" onclick="Shop.update('<?=$this->getUniqid()?>', '<?=$npc->getId()?>', 'id_seller', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge border-1 border-solid border-green-d-2'><?=$npc->getName()?> - <i class='text-grey-d-2'><?=$npc->getClasse(Content::FORMAT_OBJECT)->getName()?></i></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php return ob_get_clean();


                case Content::DISPLAY_RESUME:
                    if(isset($npc)){
                        return $npc->getVisual(Content::DISPLAY_RESUME);
                    } else {
                        return "";
                    }

                case Content::FORMAT_OBJECT:
                    if(isset($npc)){
                        return $npc;
                    } else {
                        return "";
                    }

                case Content::FORMAT_BADGE:
                    if(isset($npc)){
                        return "<span class='badge text-black back-white border-1 border-solid border-green-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Marchand·e gérant l'hôtel de vente\">".$npc->getName()." - <i class='text-grey-d-2'>".$npc->getClasse(Content::FORMAT_OBJECT)->getName()."</i></span>";
                    } else {
                        return "";
                    }
                
                case Content::FORMAT_IMAGE:
                    if(isset($npc)){
                        return $npc->getClasse(Content::FORMAT_OBJECT)->getPath_img_logo(Content::FORMAT_IMAGE, "img-back-30");
                    } else {
                        return "";
                    }
                    

                default:
                    return $this->_id_seller;
            }
        }
        
        public function getConsumable(int $format = Content::FORMAT_BRUT){
            $manager = new ShopManager;
            $links = $manager->getLinkConsumable($this);

            switch ($format) { 
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div><?=$this->getConsumable()?></div>
                        <h6 class="mt-1">Ajouter des consommables</h6>
                        <div class="d-flex flex-row justify-content-evenly align-items-baseline mb-3 position-relative">
                            <div class="form-floating w-100">
                                <input  type="text" 
                                        data-url = "index.php?c=search&a=search"
                                        data-search_in = <?=ControllerSearch::SEARCH_IN_CONSUMABLE?>
                                        data-minlenght = 3
                                        data-parameter = "<?=$this->getUniqid()?>"
                                        data-action = <?=ControllerSearch::SEARCH_DONE_ADD_CONSUMABLE_TO_SHOP?>
                                        data-limit = 10
                                        data-only_usable = false
                                        class="form-control form-control-main-focus" 
                                        id="addConsumable<?=$this->getUniqid()?>" 
                                        placeholder="Rechercher un consommable">
                                <label for="addConsumable<?=$this->getUniqid()?>">Rechercher un consommable</label>
                            </div>
                            <span id="search-sign"></span>
                        </div>
                        <script>autocomplete_load("#addConsumable<?=$this->getUniqid()?>");</script>
                    <?php return ob_get_clean();

                case Content::FORMAT_ARRAY:
                    return $links;
                
                default:
                    ob_start(); ?> 
                        <div class="d-flex flex-row justify-content-around flex-wrap"> <?php
                            if(!empty($links)){
                                foreach ($links as $link) { ?>
                                    <div class="m-2" style="width: 15rem;position:relative;">
                                        <div class="card border border-grey back-white card-hover-linked" style="width: 15rem;">
                                            <div>
                                                <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                                                    <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Dissocier cet équipement de cet hôtel de vente" class="<?=View::getCss(View::TYPE_BTN_UNDERLINE, "red")?>" style="position:absolute;top:10px;right:10px;z-index:10;" onclick="if (confirm('Etes vous sûr dissocier l\'équipement de cet hôtel de vente ?')){Shop.update('<?=$this->getUniqid()?>',{action:'remove', uniqid:'<?=$link['obj']->getUniqid()?>'},'consumable', IS_VALUE);}"><i class="fas fa-times"></i></a>
                                                </div>
                                                <a class="text-left" style="position:absolute;top:5px;left:5px;" href="<?=$link["obj"]->getPath_img()?>" download="<?=$link["obj"]->getName().'.'.substr(strrchr($link["obj"]->getPath_img(),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
                                                <?=$link["obj"]->getPath_img(Content::FORMAT_FANCY, "img-back-100H-allL")?>
                                                <div class="card-body position-relative" id="viewconsumable<?=$link['obj']->getUniqid()?>">
                                                    <span class="ms-1 position-absolute" style="top:-14px;left:5px;"><?=$link["obj"]->getLevel(Content::FORMAT_BADGE)?></span> 
                                                    <?php if(!empty($link['quantity'])){ ?>
                                                        <span class="position-absolute translate-middle badge rounded-pill back-main-d-2 size-1" style="top:0px;right:5px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Quantité">
                                                            <?=$link['quantity']?><span class="visually-hidden">unread messages</span>
                                                        </span>
                                                    <?php } ?>
                                                    <h5 class="card-title"><?=$link["obj"]->getName()?></h5>
                                                    <div class="row justify-content-between">
                                                        <p class="d-flex flex-row justify-content-between flex-wrap">
                                                            <span class="me-1"><?=$link["obj"]->getType(Content::FORMAT_BADGE)?></span>
                                                            <span class="ms-1"><?=$link["obj"]->getRarity(Content::FORMAT_BADGE)?></span>          
                                                        </p>
                                                        <p class="d-flex flex-row justify-content-around mt-1 badge back-white border border-2 border-kamas-d-4">
                                                            <?php if(!empty($link['price'])){ ?>
                                                                <span class='text-kamas-d-4 size-1-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Prix de l'équipement"><?=$link["price"]?> <img class='icon' src='medias/icons/kamas.png'></span>     
                                                            <?php } ?>
                                                            <?php if(!empty($link['obj']->getPrice())){ ?>
                                                                <span class="ms-1 size-0-8 text-grey-d-2 text-right">Prix recommandé :<br><?=$link["obj"]->getPrice()?> <img class='icon-sm' src='medias/icons/kamas.png'></span>
                                                            <?php } ?>
                                                        </p>
                                                    </div>
                                                    <p class="card-text"><?=$link["obj"]->getEffect()?></p>
                                                </div>
                                            </div>
                                            <div class="card-hover-showed">
                                                <div class="nav-consumable-divider back-main-d-1"></div>
                                                <p class="card-text size-0-8 text-grey-d-2"><?=$link["obj"]->getDescription()?></p>
                                                <?php if(!empty($link['comment'])){ ?>
                                                    <div class="nav-consumable-divider back-main-d-1"></div>
                                                    <p class="card-text text-red-d-2"><?=$link["comment"]?></p>
                                                <?php } ?>
                                                <div class="nav-consumable-divider back-main-d-1"></div>
                                                <a class="text-center btn-underline-main" onclick="switchCardShopconsumable('<?=$link['obj']->getUniqid()?>');">Passer en mode édition</a>
                                            </div>
                                            <div class="card-body" id="modifyconsumable<?=$link['obj']->getUniqid()?>">
                                                <h5 class="card-title"><?=$link["obj"]->getName()?></h5>
                                                <div class="form-floating mb-3">
                                                    <input value="<?=$link['quantity']?>" id="quantityInput<?=$link['obj']->getUniqid()?>" onchange="Shop.update('<?=$this->getUniqid()?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', quantity:$(this).val()},'consumable', IS_VALUE);" type="text" class="form-control form-control-main-focus" placeholder="Quantité">
                                                    <label for="quantityInput<?=$link['obj']->getUniqid()?>">Quantité</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input value="<?=$link['price']?>" id="priceInput<?=$link['obj']->getUniqid()?>" onchange="Shop.update('<?=$this->getUniqid()?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', price:$(this).val()},'consumable', IS_VALUE);" type="text" class="form-control form-control-main-focus" placeholder="Prix">
                                                    <label for="priceInput<?=$link['obj']->getUniqid()?>">Prix</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input value="<?=$link['comment']?>" id="commentInput<?=$link['obj']->getUniqid()?>" onchange="Shop.update('<?=$this->getUniqid()?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', comment:$(this).val()},'consumable', IS_VALUE);" type="text" class="form-control form-control-main-focus" placeholder="Commentaire">
                                                    <label for="commentInput<?=$link['obj']->getUniqid()?>">Commentaire</label>
                                                </div>
                                                <div>
                                                    <a class="btn btn-sm btn-back-secondary" onclick="consumable.open('<?=$link['obj']->getUniqid()?>');">Modifier l'équipement</a>
                                                </div>
                                                <div class="nav-consumable-divider back-main-d-1"></div>
                                                <a class="text-center btn-underline-main" onclick="switchCardShopconsumable('<?=$link['obj']->getUniqid()?>');">Revenir à la vue descriptive</a>
                                            </div>
                                            <script>$("#modifyconsumable<?=$link['obj']->getUniqid()?>").hide();</script>
                                        </div>
                                    </div>
                                <?php } ?>
                                    <script>
                                        function switchCardShopconsumable(uniqid){
                                            if($('#viewconsumable' + uniqid).is(":hidden")){
                                                $('#viewconsumable' + uniqid).show("slow");
                                                $('#modifyconsumable' + uniqid).hide("slow");
                                            } else {
                                                $('#viewconsumable' + uniqid).hide("slow");
                                                $('#modifyconsumable' + uniqid).show("slow");
                                            }
                                        }
                                    </script>
                            <?php }
                        ?> </div> <?php 
                    return ob_get_clean();
            }   
        }
        public function getItem(int $format = Content::FORMAT_BRUT){
            $manager = new ShopManager;
            $links = $manager->getLinkItem($this);

            switch ($format) { 
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div><?=$this->getItem()?></div>
                        <h6 class="mt-1">Ajouter des équipements</h6>
                        <div class="d-flex flex-row justify-content-evenly align-items-baseline mb-3 position-relative">
                            <div class="form-floating w-100">
                                <input  type="text" 
                                        data-url = "index.php?c=search&a=search"
                                        data-search_in = <?=ControllerSearch::SEARCH_IN_ITEM?>
                                        data-minlenght = 3
                                        data-parameter = "<?=$this->getUniqid()?>"
                                        data-action = <?=ControllerSearch::SEARCH_DONE_ADD_ITEM_TO_SHOP?>
                                        data-limit = 10
                                        data-only_usable = false
                                        class="form-control form-control-main-focus" 
                                        id="addItem<?=$this->getUniqid()?>" 
                                        placeholder="Rechercher un équipement">
                                <label for="addItem<?=$this->getUniqid()?>">Rechercher un équipement</label>
                            </div>
                            <span id="search-sign"></span>
                        </div>
                        <script>autocomplete_load("#addItem<?=$this->getUniqid()?>");</script>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_ARRAY:
                    return $links;
                
                default:
                    ob_start(); ?> 
                        <div class="d-flex flex-row justify-content-around flex-wrap"> <?php
                            if(!empty($links)){
                                foreach ($links as $link) { ?>
                                    <div class="m-2" style="width: 15rem;position:relative;">
                                        <div class="card border border-grey back-white card-hover-linked" style="width: 15rem;">
                                            <div>
                                                <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                                                    <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Dissocier cet équipement de cet hôtel de vente" class="<?=View::getCss(View::TYPE_BTN_UNDERLINE, "red")?>" style="position:absolute;top:10px;right:10px;z-index:10;" onclick="if (confirm('Etes vous sûr dissocier l\'équipement de cet hôtel de vente ?')){Shop.update('<?=$this->getUniqid()?>',{action:'remove', uniqid:'<?=$link['obj']->getUniqid()?>'},'item', IS_VALUE);}"><i class="fas fa-times"></i></a>
                                                </div>
                                                <a class="text-left" style="position:absolute;top:5px;left:5px;" href="<?=$link["obj"]->getPath_img()?>" download="<?=$link["obj"]->getName().'.'.substr(strrchr($link["obj"]->getPath_img(),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
                                                <?=$link["obj"]->getPath_img(Content::FORMAT_FANCY, "img-back-100H-allL")?>
                                                <div class="card-body position-relative" id="viewItem<?=$link['obj']->getUniqid()?>">
                                                    <span class="ms-1 position-absolute" style="top:-14px;left:5px;"><?=$link["obj"]->getLevel(Content::FORMAT_BADGE)?></span> 
                                                    <?php if(!empty($link['quantity'])){ ?>
                                                        <span class="position-absolute translate-middle badge rounded-pill back-main-d-2 size-1" style="top:0px;right:5px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Quantité">
                                                            <?=$link['quantity']?><span class="visually-hidden">unread messages</span>
                                                        </span>
                                                    <?php } ?>
                                                    <h5 class="card-title"><?=$link["obj"]->getName()?></h5>
                                                    <div class="row justify-content-between">
                                                        <p class="d-flex flex-row justify-content-between flex-wrap">
                                                            <span class="me-1"><?=$link["obj"]->getType(Content::FORMAT_BADGE)?></span>
                                                            <span class="ms-1"><?=$link["obj"]->getRarity(Content::FORMAT_BADGE)?></span>          
                                                        </p>
                                                        <p class="d-flex flex-row justify-content-around mt-1 badge back-white border border-2 border-kamas-d-4">
                                                            <?php if(!empty($link['price'])){ ?>
                                                                <span class='text-kamas-d-4 size-1-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Prix de l'équipement"><?=$link["price"]?> <img class='icon' src='medias/icons/kamas.png'></span>     
                                                            <?php } ?>
                                                            <?php if(!empty($link['obj']->getPrice())){ ?>
                                                                <span class="ms-1 size-0-8 text-grey-d-2 text-right">Prix recommandé :<br><?=$link["obj"]->getPrice()?> <img class='icon-sm' src='medias/icons/kamas.png'></span>
                                                            <?php } ?>
                                                        </p>
                                                    </div>
                                                    <p class="card-text"><?=$link["obj"]->getEffect()?></p>
                                                </div>
                                            </div>
                                            <div class="card-hover-showed">
                                                <div class="nav-item-divider back-main-d-1"></div>
                                                <p class="card-text size-0-8 text-grey-d-2"><?=$link["obj"]->getDescription()?></p>
                                                <?php if(!empty($link['comment'])){ ?>
                                                    <div class="nav-item-divider back-main-d-1"></div>
                                                    <p class="card-text text-red-d-2"><?=$link["comment"]?></p>
                                                <?php } ?>
                                                <div class="nav-item-divider back-main-d-1"></div>
                                                <a class="text-center btn-underline-main" onclick="switchCardShopItem('<?=$link['obj']->getUniqid()?>');">Passer en mode édition</a>
                                            </div>
                                            <div class="card-body" id="modifyItem<?=$link['obj']->getUniqid()?>">
                                                <h5 class="card-title"><?=$link["obj"]->getName()?></h5>
                                                <div class="form-floating mb-3">
                                                    <input value="<?=$link['quantity']?>" id="quantityInput<?=$link['obj']->getUniqid()?>" onchange="Shop.update('<?=$this->getUniqid()?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', quantity:$(this).val()},'item', IS_VALUE);" type="text" class="form-control form-control-main-focus" placeholder="Quantité">
                                                    <label for="quantityInput<?=$link['obj']->getUniqid()?>">Quantité</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input value="<?=$link['price']?>" id="priceInput<?=$link['obj']->getUniqid()?>" onchange="Shop.update('<?=$this->getUniqid()?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', price:$(this).val()},'item', IS_VALUE);" type="text" class="form-control form-control-main-focus" placeholder="Prix">
                                                    <label for="priceInput<?=$link['obj']->getUniqid()?>">Prix</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input value="<?=$link['comment']?>" id="commentInput<?=$link['obj']->getUniqid()?>" onchange="Shop.update('<?=$this->getUniqid()?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', comment:$(this).val()},'item', IS_VALUE);" type="text" class="form-control form-control-main-focus" placeholder="Commentaire">
                                                    <label for="commentInput<?=$link['obj']->getUniqid()?>">Commentaire</label>
                                                </div>
                                                <div>
                                                    <a class="btn btn-sm btn-back-secondary" onclick="Item.open('<?=$link['obj']->getUniqid()?>');">Modifier l'équipement</a>
                                                </div>
                                                <div class="nav-item-divider back-main-d-1"></div>
                                                <a class="text-center btn-underline-main" onclick="switchCardShopItem('<?=$link['obj']->getUniqid()?>');">Revenir à la vue descriptive</a>
                                            </div>
                                            <script>$("#modifyItem<?=$link['obj']->getUniqid()?>").hide();</script>
                                        </div>
                                    </div>
                                <?php } ?>
                                    <script>
                                        function switchCardShopItem(uniqid){
                                            if($('#viewItem' + uniqid).is(":hidden")){
                                                $('#viewItem' + uniqid).show("slow");
                                                $('#modifyItem' + uniqid).hide("slow");
                                            } else {
                                                $('#viewItem' + uniqid).hide("slow");
                                                $('#modifyItem' + uniqid).show("slow");
                                            }
                                        }
                                    </script>
                            <?php }
                        ?> </div> <?php 
                    return ob_get_clean();
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

            $npc = $this->getId_seller(Content::FORMAT_OBJECT);

            switch ($display) {
                case Content::DISPLAY_MODIFY:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <p class='size-0-7 m-1'>Hôtel de vente N°<?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$this->getTimestamp_updated(Content::DATE_FR);?></p>
                            <div class="d-flex flex-row justify-content-around flex-wrap m-3">
                                <div class="col-auto">
                                    <h4 class="m-2 mx-4"><?=$this->getName(Content::DISPLAY_MODIFY)?></h4>
                                    <p><?=$this->getLocation(Content::DISPLAY_MODIFY)?></p>
                                    <p><?=$this->getPrice(Content::DISPLAY_MODIFY)?></p>
                                </div>
                                <div class="col-auto">
                                    <h6 class="text-center">Marchand·e</h6>
                                    <div class="row justify-content-center">
                                        <?php if(!empty($npc)){ ?>
                                            <?=$npc->getVisual(Content::DISPLAY_RESUME)?>
                                        <?php }  else {?>
                                            <p class="text-center">Aucun</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?=$this->getDescription(Content::DISPLAY_MODIFY)?>
                            <div class="card-body text-center">
                                <h3>Equipements</h3>
                                <?=$this->getItem(Content::DISPLAY_MODIFY)?>
                                <h3>Consommables</h3>
                                <?=$this->getConsumable(Content::DISPLAY_MODIFY)?>
                            </div>
                            <p class="text-right font-size-0-8 m-1"><a class='text-red-d-2 text-red-l-3-hover' onclick="Shop.remove('<?=$this->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
                        </div>
                    <?php return ob_get_clean();

                case Content::DISPLAY_CARD:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <p class='size-0-7 m-1'>Hôtel de vente <?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$this->getTimestamp_updated(Content::DATE_FR);?></p>
                            <div class="d-flex flex-row justify-content-around flex-wrap">
                                <div class="col-auto">
                                    <h3 class="m-2 mx-4"><?=$this->getName()?></h3>
                                    <div class="d-flex flex-row justify-content-between">
                                        <p class="me-3"><?=$this->getLocation(Content::FORMAT_ICON)?></p>
                                        <p>Prix moyen : <?=$this->getPrice(Content::FORMAT_BADGE)?></p>
                                    </div>
                                    <p><?=$this->getDescription()?></p>
                                </div>
                                <div class="col-auto">
                                    <h6 class="text-center">Marchand·e</h6>
                                    <div class="row justify-content-center">
                                        <?php if(!empty($npc)){ ?>
                                            <?=$npc->getVisual(Content::DISPLAY_RESUME)?>
                                        <?php }  else {?>
                                            <p class="text-center">Aucun</p>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <?php if($user->getRight('shop', User::RIGHT_WRITE)){ ?>
                                        <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Shop.open('<?=$this->getUniqid()?>', Controller.DISPLAY_MODIFY);"><i class='far fa-edit'></i> Modifier</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <h3>Equipements</h3>
                                <?=$this->getItem()?>
                                <h3>Consommables</h3>
                                <?=$this->getConsumable()?>
                            </div>
                            <p class="text-right font-size-0-8 m-1"><a class='text-red-d-2 text-red-l-3-hover' onclick="Shop.remove('<?=$this->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
                        </div>
                    <?php return ob_get_clean();

                case Content::DISPLAY_RESUME:
                    ob_start(); ?>
                        <div style="position:relative;" style="width: <?=$size?>px;">
                            <div ondblclick="Shop.open('<?=$this->getUniqid()?>');" class="card-hover-linked card p-2 m-1 border-secondary-d-2 border" style="width: <?=$size?>px;">
                                <div class="d-flex flew-row flex-nowrap">
                                    <div>
                                        <p class="bold"><?=$this->getName()?></p>
                                        <div class="d-flex justify-content-between">
                                            <?=$this->getLocation(Content::FORMAT_ICON)?>
                                            <?=$this->getPrice(Content::FORMAT_BADGE)?>
                                        </div>
                                        <p> Marchand·e : 
                                            <?php if(!empty($npc)){ echo $npc->getName(); }  else { echo "Aucun"; } ?>
                                        </p>
                                    </div>
                                    <div class="d-flex flex-column justify-content-between ms-auto">
                                        <a onclick='User.changeBookmark(this);' data-classe='shop' data-uniqid='<?=$this->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                                        <a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=shop&a=getPdf&uniqid=<?=$this->getUniqid()?>'><i class='fas fa-file-pdf'></i></a>
                                    </div>
                                </div>
                                <div class="card-hover-showed">
                                    <?=$this->getDescription()?> 
                                </div>
                            </div>
                        </div>
                    <?php return ob_get_clean();

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
        public function setLocation($data){
            $this->_location = $data;
            return true;
        }
        public function setPrice($data){
            if(in_array($data, Shop::PRICE)){
                $this->_price= $data;
                return true;
            } else {
                return "Le prix moyen n'est pas valide";
            }
        }
        public function setId_seller($data){
            $this->_id_seller = $data;
            return true;
        }

        /* Data = array(
                        uniqid => id du consomable,
                        quantity => quantité du consomable,
                        price => prix du consomable,
                        action => remove / add / update
                    )
            Js : Shop.update(UniqidS,{action:'add|remove|update', uniqid:'uniqIdC', quantity:'Quantity',price:'price',comment:'Comment'},'consumable', IS_VALUE);
        */
        public function setConsumable(array $data){ 
            $managerS = new ShopManager;
            $managerC = new ConsumableManager;
            if(!isset($data['uniqid'])){return "L'uniqid du consommable n'est pas défini";}
            if($managerC->existsUniqid($data['uniqid'])){
                $consumable = $managerC->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            if(isset($data['quantity'])){$quantity=$data['quantity'];} else {$quantity="";}
                            if(isset($data['price'])){$price=$data['price'];} else {$price="";}
                            if(isset($data['comment'])){$comment=$data['comment'];} else {$comment="";}
                            return $managerS->addLinkConsumable($this, $consumable, $quantity, $price, $comment);
               
                        case "remove":
                            return $managerS->removeLinkConsumable($this, $consumable);

                        case "update":
                            if($managerS->existsLinkConsumable($this, $consumable)){
                                $link = $managerS->getLinkConsumable($this, $consumable);
                                if(isset($data['quantity'])){$quantity=$data['quantity'];} else {$quantity=$link["quantity"];}
                                if(isset($data['price'])){$price=$data['price'];} else {$price=$link["price"];}
                                if(isset($data['comment'])){$comment=$data['comment'];} else {$comment=$link["comment"];}
                                return $managerS->updateLinkConsumable($this, $consumable, $quantity, $price, $comment);

                            } else {
                                if(isset($data['quantity'])){$quantity=$data['quantity'];} else {$quantity="";}
                                if(isset($data['price'])){$price=$data['price'];} else {$price="";}
                                if(isset($data['comment'])){$comment=$data['comment'];} else {$comment="";}
                                return $managerS->addLinkConsumable($this, $consumable, $quantity, $price, $comment);
                            }
                        
                        default:
                            return "L'action n'est pas valide";
                    }

                } else {
                    return "Une action est requise.";
                }

            }
        }

        /* Data = array(
                uniqid => id de l'équipement,
                quantity => quantité de l'équipement,
                price => prix du consomable,
                action => remove / add / update
            )
            Js : Shop.update(UniqidS,{action:'add|remove|update', uniqid:'uniqIdC', quantity:'Quantity',price:'price',comment:'Comment'},'item', IS_VALUE);
        */
        public function setItem(array $data){ 
            $managerS = new ShopManager;
            $managerI = new ItemManager;
            if(!isset($data['uniqid'])){return "L'uniqid du l'équipement n'est pas défini";}
            if($managerI->existsUniqid($data['uniqid'])){
                $item = $managerI->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            if(array_key_exists("quantity", $data)){$quantity=$data['quantity'];} else {$quantity="";}
                            if(array_key_exists("price", $data)){$price=$data['price'];} else {$price="";}
                            if(array_key_exists("comment", $data)){$comment=$data['comment'];} else {$comment="";}
                            return $managerS->addLinkItem($this, $item, $quantity, $price, $comment);
               
                        case "remove":
                            return $managerS->removeLinkItem($this, $item);

                        case "update":
                            if($managerS->existsLinkItem($this, $item)){
                                $link = $managerS->getLinkItemFromItem($this, $item);
                                if(array_key_exists("quantity", $data)){$quantity=$data['quantity'];} else {$quantity=$link["quantity"];}
                                if(array_key_exists("price", $data)){$price=$data['price'];} else {$price=$link["price"];}
                                if(array_key_exists("comment", $data)){$comment=$data['comment'];} else {$comment=$link["comment"];}
                                return $managerS->updateLinkItem($this, $item, $quantity, $price, $comment);

                            } else {
                                if(array_key_exists("quantity", $data)){$quantity=$data['quantity'];} else {$quantity="";}
                                if(array_key_exists("price", $data)){$price=$data['price'];} else {$price="";}
                                if(array_key_exists("comment", $data)){$comment=$data['comment'];} else {$comment="";}
                                return $managerS->addLinkItem($this, $item, $quantity, $price, $comment);
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