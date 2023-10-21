<div class="close-menu">
    <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Fermer le menu" onclick="toogleMenu();"><i class="fa-solid fa-times"></i></a>
</div>
<div>
    <a onclick="Page.show('home')" class="brand-logo"><img src="medias/logos/logo.png" alt="Logo du site" width="180"></a>
</div>
<?php $manager = new PageManager; ?>

<aside class="app-nav-list"> <!-- MENU -->
    
    <?php 
    $currentUser = ControllerConnect::getCurrentUser();
    foreach (Page::CATEGORY as $name_category => $number_category) { ?>

        <p class="app-nav-list-title"><?=$name_category?></p>
        <?php foreach ($manager->getAllFromCategory($number_category) as $page) { 

            if($page->getIs_dropdown()){ 
                if($page->getPublic() || ($currentUser->isConnect() && $currentUser->getRight('page', User::RIGHT_READ) )){ ?>

                <div class='app-nav-dropdown'>
                    <a class="app-nav-item dropdown-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#dropdown-menu<?=$page->getId()?>" role="button" aria-expanded="false"><?=$page->getName()?></a>
                    <div id='dropdown-menu<?=$page->getId()?>' class="hide collapse">
                        <?php foreach ($manager->getAllFromCategory($page->getUniqid()) as $page_child) { 
                            if($page_child->getPublic() || ($currentUser->isConnect() && $currentUser->getRight('page', User::RIGHT_READ))){ ?>
                                <a class="dropdown-item" href="<?=$page_child->getUrl_name()?>" onclick="Page.show('<?=$page_child->getUrl_name()?>');return false;"><?=$page_child->getName()?></a>
                        <?php }
                        } ?></a>
                    </div>
                </div>
                    
                <?php }
            } else { 
                
                if($page->getPublic() || ($currentUser->isConnect() && $currentUser->getRight('page', User::RIGHT_READ))){ ?>
                
                    <a href="<?=$page->getUrl_name()?>" onclick="Page.show('<?=$page->getUrl_name()?>'); return false;" data-uniqid="<?=$page->getUniqid()?>" class="menu-item-selector app-nav-item grid">
                        <span><?=$page->getName()?></span>
                    </a>
                    <?php foreach ($manager->getAllFromCategory($page->getUniqid()) as $page_child) { 
                        if($page_child->getPublic() || ($currentUser->isConnect() && $currentUser->getRight('page', User::RIGHT_READ))){ ?>
                            <a href="<?=$page_child->getUrl_name()?>" onclick="Page.show('<?=$page_child->getUrl_name()?>');return false;" data-uniqid="<?=$page_child->getUniqid()?>" class="menu-item-selector app-nav-item grid">
                                <span class="item-child size-0-9" ><?=$page_child->getName()?></span>
                            </a>
                        <?php }
                    } 
                }
            }
        } ?>
        
        <div class="item-divider-main"></div>

    <?php } ?>
    
    <div class="p-2 size-0-7 text-main-d-3 text-center">
        <a onclick="$('.cookie-bar').show('drop', 100);">Personaliser les cookies</a>
    </div>
    <div class="p-2 size-0-7 text-main-d-3 text-center">
        <a onclick="Page.show('cgu');">CGU</a>
    </div>
    <div class="p-2 size-0-8 text-secondary-d-4 text-center">
        <?=$GLOBALS['project']['name']?> version <?=$GLOBALS['project']['stability']?> <?=$GLOBALS['project']['version']?> <?=date("Y");?>
    </div>
</aside>