<?php
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
 ?>

<div class="text-center user__modal_connexion">
    <div class="d-flex flex-row justify-content-around align-item-baseline pb-4 pt-2 px-4">
        <p><a class="btn-text-main user__modal_btn-switch" onclick="switchConnectInscript('#modalConnexionUser', this);">Se connecter</a></p>
        <p><a class="btn-text-main user__modal_btn-switch" onclick="switchConnectInscript('#modalAddUser', this);">S'inscrire</a></p>
        <p><a class="btn-text-main user__modal_btn-switch" onclick="switchConnectInscript('#modalPasswordForgotten', this);">Mot de passe oublié</a></p>
    </div>
    <div id="modalConnexionUser" class="user__modal_tab">
        <h3>Connexion</h3>
        <form>
            <div class="form-floating mb-3">
                <input required type="text" class="form-control form-control-main-focus form-control form-control-main-focus-main-focus" id="email" placeholder="Email">
                <label for="email">Email</label>
            </div>
            <div class="form-floating mb-3">
                <input required type="password" class="form-control form-control-main-focus" id="password" placeholder="Mot de passe ou Passe-phrase">
                <label for="password">Mot de passe ou Passe-phrase</label>
            </div>
            <?php $disabled=""; if(!$user->getCookie(User::COOKIE_CONNEXION)){$disabled="disabled title='Cette fonctionnalité resquière l\'utilisation d'un cookie de connexion.'";}?>
            <div class="form-check text-left">
                <input class="form-check-input form-control-main-focus back-main-l-2 border-main" type="checkbox" value="" <?=$disabled?> id="remember">
                <label class="form-check-label" for="remember">Garder la connexion</label>
            </div>
            <p class="size-0-8 text-grey-d-1 text-left"><i class="fa-solid fa-question-circle"></i> Cette fonctionnalité resquière l'utilisation d'un cookie de connexion.</p>
            <p id="display_error" class="bold text-red-d-3 size-0-8"></p>
            <button type="button" onclick="Connect.connect();" class="btn btn-animate btn-border-secondary">Se connecter</button>
        </form>
    </div>
    
    <div id="modalAddUser" class="user__modal_tab">
        <h3>Inscription</h3>
        <form>
            <div class="form-floating mb-3">
                <input required type="text" class="form-control form-control-main-focus" id="email" placeholder="Email">
                <label for="email">Email</label>
            </div>
            <div class="form-floating mb-3">
                <input required type="text" class="form-control form-control-main-focus" id="pseudo" placeholder="Pseudo">
                <label for="email">Pseudo</label>
            </div>
            <div class="form-floating mb-3">
                <input required onchange="verifPassword();" type="password" class="form-control form-control-main-focus" id="password" placeholder="Mot de passe ou Passe-phrase">
                <label for="password">Mot de passe ou Passe-phrase</label>
            </div>
            <div class="form-floating mb-3">
                <input required onchange="verifPassword();" type="password" class="form-control form-control-main-focus" id="password_repeat" placeholder="Réécrire le mot de passe ou la passe-phrase">
                <label for="password_repeat">Réécrire le mot de passe ou la passe-phrase</label>
            </div>
            <p id="display_error" class="bold text-red-d-3 size-0-8"></p>
            <button type="button" onclick="User.add();" class="btn btn-animate btn-border-secondary">S'inscrire</button>
        </form>
    </div>

    <div id="modalPasswordForgotten" class="user__modal_tab">
        <h3 class="mb-3">Mot de passe oublié</h3>
        <form>
            <p>Saisissez l'adresse mail que vous avez utilisé pour créer votre compte.</p>
            <div class="form-floating mb-3">
                <input required type="text" class="form-control form-control-main-focus form-control form-control-main-focus-main-focus" id="email" placeholder="Email">
                <label for="email">Email</label>
            </div>
            <p class="mb-3">Un nouveau mot de passe vous sera transmis sur cette adresse mail. Pensez à le modifier dès votre première connexion.</p>
            <button type="button" onclick="Connect.passwordForgotten();" class="btn btn-border-secondary">Envoyer un nouveau mot de passe</button>
        </form>
    </div>
</div>
<script>
    switchConnectInscript('#modalConnexionUser', ".user__modal_connexion .user__modal_btn-switch:first");
</script>