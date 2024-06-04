<?php
    if(!isset($is_connect)) {$is_connect = false;}else{if(!is_bool($is_connect)) {$is_connect = false;}}
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }

    if($is_connect){ ?>
        <div class="btn-group header-button-account">
            <button class="header-button-account-access" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="heander-button-account-access-text btn-md btn btn-back-secondary dropdown-toggle"><?=$user->getPseudo()?></div>
                <div class="heander-button-account-access-icon">
                    <?php View::shortcutDispatch(
                        template_type: View::TEMPLATE_SNIPPET,
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_SOLID,
                            "icon" => "user",
                            "color" => "secondary",
                            "is_btn" => true,
                            "btn_type" => Style::STYLE_TEXT,
                            "size" => "size-1-3",
                            "tooltip" => "Accèder à mon compte",
                            "onclick" => "",
                            "content" => "Mon compte",
                            "content_placement" => Style::POSITION_BOTTOM
                        ], 
                        write: true); ?>     
                </div>
            </button>
            <ul class="dropdown-menu">
                <li class="size-1-2 text-secondary-d-3 text-center"><?=$user->getPseudo()?></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="toolbox-toolgle-dropdrown-item dropdown-item btn-animate back-secondary-d-2-hover text-white-hover" onclick="$('#diceroller').modal('show');">Outils</a></li>
                <li><a class="dropdown-item btn-animate back-secondary-d-2-hover text-white-hover" onclick="User.open('<?=$user->getUniqid()?>');">Paramètres</a></li>
                <?php if($user->getRight("page", User::RIGHT_WRITE)){ ?>
                    <li><a class="dropdown-item btn-animate back-secondary-d-2-hover text-white-hover" onclick="Page.show('gestion_des_pages');">Gérer les pages</a></li>
                <?php } ?>
                <?php if($user->getIs_admin()){ ?>
                    <li><a class="dropdown-item btn-animate back-secondary-d-2-hover text-white-hover" onclick="Page.show('user_manager');">Gérer les utilisateurs·trices</a></li>
                <?php } ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item btn-animate back-secondary-d-2-hover text-white-hover" onclick="toggleToolbar();">Masquer l'entête</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item btn-animate back-secondary-d-2-hover text-white-hover" onclick="Connect.disconnect();">Deconnexion</a></li>
            </ul>
        </div>

    <?php } else { ?>
        <div class="btn-group header-btn-account">
            <button type="button" onclick="Connect.getHeader(true);" class="btn btn-sm btn-animate btn-back-secondary header-btn-account-account-access">
                <?php View::shortcutDispatch(
                    template_type: View::TEMPLATE_SNIPPET,
                    template_name : "icon",
                    data : [
                        "style" => Style::ICON_SOLID,
                        "icon" => "user",
                        "color" => "white",
                        "is_btn" => false,
                        "btn_type" => Style::STYLE_NONE,
                        "size" => "size-1-3",
                        "tooltip" => "Se connecter ou créer un compte",
                        "content" => "Se connecter",
                        "content_placement" => Style::POSITION_BOTTOM
                    ], 
                    write: true); ?>
            </button>
        </div>
    <?php }