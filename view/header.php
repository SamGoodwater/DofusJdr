<header class='app-toolbar'> <!-- ENTETE -->
    <div class="app-toolbar-title">
        <h1 id="title"></h1>
    </div>
    <div class="app-toolbar-nav">
        <button class="menu-toggle"><?php
            View::shortcutDispatch(
                template_type: View::TEMPLATE_SNIPPET,
                template_name : "icon",
                data : [
                    "style" => Style::ICON_SOLID,
                    "icon" => "bars",
                    "color" => "secondary",
                    "is_btn" => true,
                    "btn_type" => Style::STYLE_TEXT,
                    "size" => "size-1-3",
                    "tooltip" => "Ouvrir le Menu",
                    "onclick" => "toggleMenu()",
                    "content" => "Menu",
                    "content_placement" => Style::POSITION_BOTTOM
                ], 
                write: true);
        ?></button>
        <div class="globalsearch-box-container">
            <div>
                <input type="text"
                    data-url = "index.php?c=search&a=search"
                    data-search_in = <?=ControllerModule::SEARCH_IN_ALL?>
                    data-minlenght = 3
                    data-action = <?=ControllerModule::SEARCH_DONE_REDIRECT?>
                    data-limit = 5
                    data-only_usable = true
                    class="form-control text-main-d-3 form-control-sm"
                    id="globalsearch"
                    placeholder="Recherche ...">
                <i class="fa-solid fa-magnifying-glass globalsearch-box-icon"></i>
                <span id="search-sign"></span>
            </div>
        </div>
        <button class="globalsearch-toggle"><?php
            View::shortcutDispatch(
                template_type: View::TEMPLATE_SNIPPET,
                template_name : "icon",
                data : [
                    "style" => Style::ICON_SOLID,
                    "icon" => "search",
                    "color" => "secondary",
                    "is_btn" => true,
                    "btn_type" => Style::STYLE_TEXT,
                    "size" => "size-1-3",
                    "tooltip" => "Rechercher sur le site",
                    "content" => "Rechercher",
                    "content_placement" => Style::POSITION_BOTTOM
                ], 
                write: true);
        ?></button>
        <button class="bookmark-toggle"><?php
            View::shortcutDispatch(
                template_type: View::TEMPLATE_SNIPPET,
                template_name : "icon",
                data : [
                    "style" => Style::ICON_SOLID,
                    "icon" => "book",
                    "color" => "secondary",
                    "is_btn" => true,
                    "btn_type" => Style::STYLE_TEXT,
                    "size" => "size-1-3",
                    "tooltip" => "Ouvrir le ".ucfirst($GLOBALS['project']['bookmark_name'])." (ctrl + b)",
                    "onclick" => "User.getBookmark(true);",
                    "content" => ucfirst($GLOBALS['project']['bookmark_name']),
                    "content_placement" => Style::POSITION_BOTTOM
                ], 
                write: true);
        ?></button>
        <button class="toolsbox-toggle"><?php   
            View::shortcutDispatch(
                template_type: View::TEMPLATE_SNIPPET,
                template_name : "icon",
                data : [
                    "style" => Style::ICON_SOLID,
                    "icon" => "dice",
                    "color" => "secondary",
                    "is_btn" => true,
                    "btn_type" => Style::STYLE_TEXT,
                    "size" => "size-1-3",
                    "tooltip" => "Boite à outils",
                    "onclick" => "$('#diceroller').modal('show');",
                    "content" => "Outils",
                    "content_placement" => Style::POSITION_BOTTOM
                ], 
                write: true);
        ?></button>
        <div id="userVisual" class="account-toggle"></div>
    </div>
</header>