<?php
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
 ?>

<div data-component="tabs" class="text-center user__modal_connexion user-connexion-container">
    <div class="user-connexion-container__menu">
        <button data-target=".user-connexion-container__tab-connexion" class="user-connexion-container__menu__switcher user-connexion-container__menu__switcher-connexion">Se connecter</button>
        <button data-target=".user-connexion-container__tab-inscription" class="user-connexion-container__menu__switcher user-connexion-container__menu__switcher-inscription">S'inscrire</button>
        <button data-target=".user-connexion-container__tab-password-forgotten" class="user-connexion-container__menu__switcher user-connexion-container__menu__switcher-password-forgotten">Mot de passe oublié</button>
    </div>

    <div class="user-connexion-container__tab-connexion user-connexion-container__tab">
        <div class="user-connexion-container__tab__container">
            <label for="email">Email</label>
            <input required type="text" class="user-connexion-container__tab__container__input" id="email" placeholder="Email">
        </div>
        <div class="user-connexion-container__tab__container">
            <label for="password">Mot de passe ou Passe-phrase</label>
            <input required type="password" class="user-connexion-container__tab__container__input" id="password" placeholder="Mot de passe ou Passe-phrase">
        </div>
        <?php $disabled=""; if(!$user->getCookie(User::COOKIE_CONNEXION)){$disabled="disabled title='Cette fonctionnalité resquière l\'utilisation de cookies de connexion.'";}?>
        <div class="form-check text-left user-connexion-container__tab__container-check">
            <input class="form-check-input form-control-main-focus back-main-l-2 border-main" type="checkbox" value="" <?=$disabled?> id="remember">
            <label class="form-check-label" for="remember">Garder la connexion</label>
        </div>
        <p class="size-0-8 text-grey-d-1"><i class="fa-solid fa-question-circle mt-0"></i> Cette fonctionnalité resquière l'utilisation d'un cookie de connexion.</p>
        <p id="display_error" class="bold text-red-d-3 size-0-8"></p>
        <button id="btn_connexion" type="button" onclick="Connect.connect();" class="user-connexion-container__tab__btn-valid">Se connecter</button>
    </div>
    
    <div class="user-connexion-container__tab-inscription user-connexion-container__tab">
        <div class="user-connexion-container__tab__container">
            <label for="email">Email</label>
            <input required type="text" class="user-connexion-container__tab__container__input" id="email" placeholder="Email">
        </div>
        <div class="user-connexion-container__tab__container">
            <label for="email">Pseudo</label>
            <input required type="text" class="user-connexion-container__tab__container__input" id="pseudo" placeholder="Pseudo">
        </div>
        <div class="user-connexion-container__tab__container">
            <label for="password">Mot de passe ou Passe-phrase</label>
            <input required onchange="verifPassword();" type="password" class="user-connexion-container__tab__container__input" id="password" placeholder="Mot de passe ou Passe-phrase">
        </div>
        <div class="user-connexion-container__tab__container">
            <label for="password_repeat">Réécrire le mot de passe ou la passe-phrase</label>
            <input required onchange="verifPassword();" type="password" class="user-connexion-container__tab__container__input" id="password_repeat" placeholder="Réécrire le mot de passe ou la passe-phrase">
        </div>
        <p id="display_error" class="bold text-red-d-3 size-0-8"></p>
        <button type="button" onclick="User.add();" class="user-connexion-container__tab__btn-valid">S'inscrire</button>
    </div>

    <div class="user-connexion-container__tab-password-forgotten user-connexion-container__tab">
        <p>Saisissez l'adresse mail que vous avez utilisé pour créer votre compte.</p>
        <div class="user-connexion-container__tab__container">
            <label for="email">Email</label>
            <input required type="text" class="user-connexion-container__tab__container__input" id="email" placeholder="Email">
        </div>
        <p>Un nouveau mot de passe vous sera transmis sur cette adresse mail. Pensez à le modifier dès votre première connexion.</p>
        <button type="button" onclick="Connect.passwordForgotten();" class="user-connexion-container__tab__btn-valid">Envoyer un nouveau mot de passe</button>
    </div>
</div>