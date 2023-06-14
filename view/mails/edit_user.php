<?php
    $is_error = false;
    $error_msg = "";
    if(!isset($new_user)) {$is_error = true; $error_msg = "New_user n'est pas déclaré.";}else{if(!get_class($new_user) == "User") {$is_error = true; $error = "New_user n'est pas au bon format";}}
    if(!isset($old_user)) {$is_error = true; $error_msg = "New_user n'est pas déclaré.";}else{if(!get_class($old_user) == "User") {$is_error = true; $error = "New_user n'est pas au bon format";}}
?>

ob_start(); ?>
    <table data-thumb="<?=$GLOBALS['project']['logo']?>" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td data-bgcolor="bg-module" bgcolor="#eaeced">
                <table class="flexible" width="600" align="center" style="margin:0 auto;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="img-flex"><img src="<?=$GLOBALS['project']['background_img']?>" style="vertical-align:top;" width="600" alt="" /></td>
                    </tr>
                    <tr>
                        
                        <td data-bgcolor="bg-block" class="holder" style="padding:58px 60px 52px;" bgcolor="#f9f9f9">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td data-color="title" data-size="size title" data-min="25" data-max="45" data-link-color="link title color" data-link-style="text-decoration:none; color:#292c34;" class="title" align="center" style="font:35px/38px Arial, Helvetica, sans-serif; color:#292c34; padding:0 0 24px;">
                                        <?=$GLOBALS['project']['name']?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" class="img-flex"><img src="<?=$GLOBALS['project']['logo']?>" style="vertical-align:top;padding-bottom:20px;" height="90"/></td>
                                </tr>
                                <tr>
                                    <td data-color="text" data-size="size text" data-min="10" data-max="26" data-link-color="link text color" data-link-style="font-weight:bold; text-decoration:underline; color:#40aceb;" align="left" style="font:bold 16px/25px Arial, Helvetica, sans-serif; color:#888; padding:0 0 23px;">
                                        Les droits de l'utilisateur·trice <?=$new_user->getPseudo()?> (<?=$old_user->getPseudo()?>) ont été modifiés. <br>
                                        <table>
                                            <tr>
                                                <td>Ancien Compte</td>
                                                <td>Nouveau Compte</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <ul>
                                                        <?php foreach($old_user->getRights(Content::FORMAT_ARRAY) as $right_name => $value){ ?>
                                                            <li><?=$right_name?> : <?=array_search($value, User::RIGHT_TYPE)?></li>
                                                        <?php } ?>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <ul>
                                                    <?php foreach($new_user->getRights(Content::FORMAT_ARRAY) as $right_name => $value){ ?>
                                                            <li><?=$right_name?> : <?=array_search($value, User::RIGHT_TYPE)?></li>
                                                        <?php } ?>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Admin : <?php if($old_user->getIs_admin()){ echo "Yes";}else{"non";}?></td>
                                                <td>Admin : <?php if($new_user->getIs_admin()){ echo "Yes";}else{"non";}?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 20px;">
                                        <table width="134" align="center" style="margin:0 auto;" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td data-bgcolor="bg-button" data-size="size button" data-min="10" data-max="16" class="btn" align="center" style="font:12px/14px Arial, Helvetica, sans-serif; color:#f8f9fb; text-transform:uppercase; mso-padding-alt:12px 10px 10px; border-radius:2px;" bgcolor="#3f51b5">
                                                    <a target="_blank" style="text-decoration:none; color:#f8f9fb; display:block; padding:12px 10px 10px;" href="<?=$_SERVER['SERVER_NAME']?>/admin">Cliquez ici pour vous connecter.</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top:15px;"><p>Ou utiliser le lien suivant <a href="<?=$_SERVER['SERVER_NAME']?>"><?=$_SERVER['SERVER_NAME']?></a></p></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td height="28"></td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php $content = ob_get_clean();