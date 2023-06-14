<header class='app-toolbar'> <!-- ENTETE -->
    <div class="app-toolbar-title">
        <a onclick="toogleMenu();" class="menu-toggle"><i class="fas fa-bars"></i></a>
        <h1 id="title"></h1>
    </div>
    <div class="app-toolbar-nav">
        <div class="mx-2">
            <input type="text" 
                style="max-width: 300px;"
                data-url = "index.php?c=search&a=search"
                data-search_in = <?=ControllerSearch::SEARCH_IN_ALL?>
                data-minlenght = 3
                data-action = <?=ControllerSearch::SEARCH_DONE_REDIRECT?>
                data-limit = 5
                data-only_usable = true
                class="form-control text-main-d-3 form-control-sm" 
                id="globalsearch" 
                placeholder="Recherche ...">
            <span id="search-sign"></span>
            <script>
                window.onload = function () {
                    autocomplete_load("#globalsearch");
                };
            </script>
        </div>
        <a class="mx-2"><?php
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
        ?></a>
        <a class="mx-2"><?php
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
                    "tooltip" => "Lanceur de dé",
                    "onclick" => "$('#diceroller').modal('show');"
                ], 
                write: true);
        ?></a>
        <div id="userVisual" class="ms-3"></div>
    </div>
</header>
<div class="app-toolbar-mobile">
    <a class="mx-2"><?php
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
                "onclick" => "toogleMenu()",
                "content" => "Menu",
                "content_placement" => Style::POSITION_BOTTOM
            ], 
            write: true);
    ?></a>
    <a class="mx-2"><?php
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
                "onclick" => "User.getBookmark(true)",
                "content" => ucfirst($GLOBALS['project']['bookmark_name']),
                "content_placement" => Style::POSITION_BOTTOM
            ], 
            write: true);
    ?></a>
    <a class="mx-2"><?php
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
                "onclick" => "Page.showSearchbar();",
                "content" => "Rechercher",
                "content_placement" => Style::POSITION_BOTTOM
            ], 
            write: true);
    ?></a>
    <a id="account_btn_toolbar_mobile" class="mx-2"></a>
</div>