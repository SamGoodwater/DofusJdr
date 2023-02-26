<header>
    <a href="#" class="menu-toggle"><i class="fas fa-bars"></i></a>
    <a onclick="Page.show('home')" class="brand-logo"><img src="medias/logos/logo.png" width="180"></a>
</header>
<?php $manager = new PageManager; ?>
<nav class="dashboard-nav-list"> <!-- MENU -->
    
    <?php 
    $currentUser = ControllerConnect::getCurrentUser();
    foreach (Page::CATEGORY as $name_category => $number_category) { ?>

        <p class="text-center text-main-d-3 size-1-2"><?=$name_category?></p>
        <?php foreach ($manager->getAllFromCategory($number_category) as $page) { 

            if($page->getIs_dropdown()){ 
                if($page->getPublic() || $currentUser->getRight('page', User::RIGHT_READ)){ ?>

                <div class='dashboard-nav-dropdown'>
                    <a class="dashboard-nav-item dashboard-nav-dropdown-toggle d-flex flex-row align-items-center"><?=$page->getName()?></a>
                    <div class='dashboard-nav-dropdown-menu'>
                        <?php foreach ($manager->getAllFromCategory($page->getUniqid()) as $page_child) { 
                            if($page_child->getPublic() || $currentUser->getRight('page', User::RIGHT_READ)){ ?>
                                <a class="size-0-9 dashboard-nav-dropdown-item" href="<?=$page_child->getUrl_name()?>" onclick="Page.show('<?=$page_child->getUrl_name()?>');return false;"><?=$page_child->getName()?></a>
                        <?php }
                        } ?>
                    </div>
                </div>
                    
                <?php }
            } else { 
                
                if($page->getPublic() || $currentUser->getRight('page', User::RIGHT_READ)){ ?>
                
                    <a href="<?=$page->getUrl_name()?>" onclick="Page.show('<?=$page->getUrl_name()?>'); return false;" data-uniqid="<?=$page->getUniqid()?>" class="menu-item-selector dashboard-nav-item grid">
                        <span><?=$page->getName()?></span>
                    </a>
                    <?php foreach ($manager->getAllFromCategory($page->getUniqid()) as $page_child) { 
                        if($page_child->getPublic() || $currentUser->getRight('page', User::RIGHT_READ)){ ?>
                            <a href="<?=$page_child->getUrl_name()?>" onclick="Page.show('<?=$page_child->getUrl_name()?>');return false;" data-uniqid="<?=$page_child->getUniqid()?>" class="menu-item-selector dashboard-nav-item grid">
                                <span class="item-child size-0-9" ><?=$page_child->getName()?></span>
                            </a>
                        <?php }
                    } 
                }
            }
        } ?>
        
        <div class="nav-item-divider"></div>

    <?php } ?>
    
    <div class="p-2 size-0-7 text-main-l-3 text-center">
        <a onclick="$('.cookie-bar').show('drop', 100);">Personaliser les cookies</a>
    </div>
    <div class="p-2 size-0-7 text-main-l-3 text-center">
        <a onclick="Page.show('cgu');">CGU</a>
    </div>
    <div class="p-2 size-0-8 text-main-l-4 text-center">
        JDR Dofus version β 1.2 <?=date("Y");?>
    </div>
</nav>