<?php

use SebastianBergmann\Environment\Console;

use function PHPUnit\Framework\fileExists;

class Mail
{
    use ColorFct, CheckingFct;

        const TEMPLATE_GENERIC = "view/mails/generic.php";      

        private $in_fatal_error = false;
        private $fatal_error_msg = "Erreur inconnu";
    
    //Contenus
        private $_subject = "";
        private $_attachment = array();
        private $_content_html = "";
        private $_content_txt = "";

    // Settings 
        private $_from = "";
        private $_from_name = "";
        private $_reply = "";
        private $_reply_name = "";
        private $_to = array();
        private $_cc = array();
        private $_cci = array();

    // CONTENU
        public function getSubject(){return $this->_subject;}
        public function getAttachment(){return $this->_attachment;}
        public function getContent_html(){return $this->_content_html;}
        public function getContent_txt(){return $this->_content_txt;}

        public function setSubject($data){$this->_subject = $data;}
        public function setAttachment($data){
            if(is_file($data)){
                $this->_attachment[] = $data;
            } 
        }
        public function setTemplate(int $template_path = self::TEMPLATE_GENERIC, Array $data = array('text' => "")){
            if(file_exists($template_path)){

                // Start template
                    ob_start(); ?>
                        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        <html xmlns="http://www.w3.org/1999/xhtml">
                            <head>
                                <title><?=$GLOBALS['project']['name']?></title>
                                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                                <style type="text/css">
                                    * {
                                        -ms-text-size-adjust:100%;
                                        -webkit-text-size-adjust:none;
                                        -webkit-text-resize:100%;
                                        text-resize:100%;
                                    }
                                    a{
                                        outline:none;
                                        color:#40aceb;
                                        text-decoration:underline;
                                    }
                                    a:hover{text-decoration:none !important;}
                                    .nav a:hover{text-decoration:underline !important;}
                                    .title a:hover{text-decoration:underline !important;}
                                    .title-2 a:hover{text-decoration:underline !important;}
                                    .btn:hover{opacity:0.8;}
                                    .btn a:hover{text-decoration:none !important;}
                                    .btn{
                                        -webkit-transition:all 0.3s ease;
                                        -moz-transition:all 0.3s ease;
                                        -ms-transition:all 0.3s ease;
                                        transition:all 0.3s ease;
                                    }
                                    table td {border-collapse: collapse !important;}
                                    .ExternalClass, .ExternalClass a, .ExternalClass span, .ExternalClass b, .ExternalClass br, .ExternalClass p, .ExternalClass div{line-height:inherit;}
                                    @media only screen and (max-width:500px) {
                                        table[class="flexible"]{width:100% !important;}
                                        table[class="center"]{
                                            float:none !important;
                                            margin:0 auto !important;
                                        }
                                        *[class="hide"]{
                                            display:none !important;
                                            width:0 !important;
                                            height:0 !important;
                                            padding:0 !important;
                                            font-size:0 !important;
                                            line-height:0 !important;
                                        }
                                        td[class="img-flex"] img{
                                            width:100% !important;
                                            height:auto !important;
                                        }
                                        td[class="aligncenter"]{text-align:center !important;}
                                        th[class="flex"]{
                                            display:block !important;
                                            width:100% !important;
                                        }
                                        td[class="wrapper"]{padding:0 !important;}
                                        td[class="holder"]{padding:30px 15px 20px !important;}
                                        td[class="nav"]{
                                            padding:20px 0 0 !important;
                                            text-align:center !important;
                                        }
                                        td[class="h-auto"]{height:auto !important;}
                                        td[class="description"]{padding:30px 20px !important;}
                                        td[class="i-120"] img{
                                            width:120px !important;
                                            height:auto !important;
                                        }
                                        td[class="footer"]{padding:5px 20px 20px !important;}
                                        td[class="footer"] td[class="aligncenter"]{
                                            line-height:25px !important;
                                            padding:20px 0 0 !important;
                                        }
                                        tr[class="table-holder"]{
                                            display:table !important;
                                            width:100% !important;
                                        }
                                        th[class="thead"]{display:table-header-group !important; width:100% !important;}
                                        th[class="tfoot"]{display:table-footer-group !important; width:100% !important;}
                                    }
                                </style>
                            </head>
                            <body style="margin:0; padding:0;" bgcolor="<?=Style::COLOR_TO_HEX[Module::COLOR_CUSTOM['main'].'-l-4']?>">
                            <table style="min-width:320px;" width="100%" cellspacing="0" cellpadding="0" bgcolor="#eaeced">
                                <!-- fix for gmail -->
                                <tr>
                                    <td class="hide">
                                        <table width="600" cellpadding="0" cellspacing="0" style="width:600px !important;">
                                            <tr>
                                                <td style="min-width:600px; font-size:0; line-height:0;">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="wrapper" style="padding:0 10px;">
                    <?php $template_current = ob_get_clean();     
                    
                
                    if(is_array($data) && !empty($data)){
                        extract($data);
                    }
                    ob_start();
                        include $template_path();
                    $template_current .= ob_get_clean();
        
                // END template
                    ob_start(); ?>
                            <table data-module="module-7" data-thumb="thumbnails/07.png" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td data-bgcolor="bg-module" bgcolor="#eaeced">
                                        <table class="flexible" width="600" align="center" style="margin:0 auto;" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td class="footer" style="padding:0 0 10px;">
                                                    <table width="100%" cellpadding="0" cellspacing="0">
                                                        <tr class="table-holder">
                                                            <th class="tfoot" width="400" align="left" style="vertical-align:top; padding:0;">
                                                                <table width="100%" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td data-color="text" data-link-color="link text color" data-link-style="text-decoration:underline; color:#797c82;" class="aligncenter" style="font:12px/16px Arial, Helvetica, sans-serif; color:#797c82; padding:0 0 10px;">
                                                                            <?=$GLOBALS['project']['name']?> <?=date("Y")?> | Propulsé par Iota21
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            </td>
                            </tr>
                            <!-- fix for gmail -->
                            <tr>
                                <td style="line-height:0;"><div style="display:none; white-space:nowrap; font:15px/1px courier;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</div></td>
                            </tr>
                                </table>
                            </body>
                        </html>
                    <?php $template_current .= ob_get_clean();
        
                $this->_content_html = $template_current;
                
            } else {
                new \Exception('Le template n\'existe pas');
            }
        }

    // Setting
        public function getFrom(){return $this->_from;}
        public function getFrom_name(){return $this->_from_name;}
        public function getReply(){return $this->_reply;}
        public function getReply_name(){return $this->_reply_name;}
        public function getTo(){return $this->_to;}
        public function getCc(){return $this->_cc;}
        public function getCci(){return $this->_cci;}

        public function setFrom(string $email, string $name = ""){
            if($this->isEmail($email)){
                $this->_from = $email;
                $this->_from_name = $name;
                return true;
            } else {
                return false;
            }
        }
        public function setFrom_name(string $name){
            if(is_string($name) && !empty($name)){
                $this->_from_name = $name;
                return true;
            } else {
                return false;
            }
        }
        public function setReply(string $email, string $name = ""){
            if($this->isEmail($email)){
                $this->_reply = $email;
                $this->_reply_name = $name;
                return true;
            } else {
                return false;
            }
        }
        public function setTo(string $email){
            if($this->isEmail($email)){
                $this->_to[] = $email;
                return true;
            } else {
                return false;
            } 
        }
        public function setCc(string $email){
            if($this->isEmail($email)){
                $this->_cc[] = $email;
                return true;
            } else {
                return false;
            } 
        }
        public function setCci(string $email){
            if($this->isEmail($email)){
                $this->_cci[] = $email;
                return true;
            } else {
                return false;
            } 
        }

        public function send(){
            if(!$this->in_fatal_error){
                //Instantiation and passing `true` enables exceptions
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
                try {
                    //Server settings
                    $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
                    $mail->isSMTP();
                    $mail->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
                    $mail->Encoding = PHPMailer\PHPMailer\PHPMailer::ENCODING_BASE64;
                    $mail->Host       = $GLOBALS['project']['mail']['smtp_host'];
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $GLOBALS['project']['mail']['smtp_username'];
                    $mail->Password   = $GLOBALS['project']['mail']['smtp_password'];
                    $mail->SMTPSecure = $GLOBALS['project']['mail']['smtp_secure'];
                    $mail->Port       = $GLOBALS['project']['mail']['smtp_port'];

                    if(!$this->isEmail($this->getFrom())){
                        $mail->setFrom($GLOBALS['project']['mail']['contact'], $GLOBALS['project']['name']);
                    } else {
                        if(empty($this->getFrom_name())){
                            $mail->setFrom($this->getFrom(), $GLOBALS['project']['name']);
                        } else {
                            $mail->setFrom($this->getFrom(), $this->getFrom_name());
                        }
                    }
                    if(!$this->isEmail($this->getReply())){
                        $this->setReply($GLOBALS['project']['mail']['contact'], $GLOBALS['project']['name']);
                    } else {
                        if(empty($this->getReply_name())){
                            $this->setReply($this->getReply(), $GLOBALS['project']['name']);
                        }
                        $mail->addReplyTo($this->getReply(), $this->getReply_name());
                    }

                    foreach ($this->getTo() as $email) {
                        $mail->addAddress($email);
                    }
                    if(!empty($this->getCc())){
                        foreach ($this->getCc() as $email) {
                            $mail->addCC($email);
                        }
                    }
                    if(!empty($this->getCci())){
                        foreach ($this->getCci() as $email) {
                            $mail->addBCC($email);
                        }
                    }
                    if(!empty($this->getAttachment())){
                        foreach ($this->getAttachment() as $file) {
                            $mail->addAttachment($file);
                        }
                    }
        
                    //Content
                    $mail->isHTML(true); 
                    $mail->Subject = $this->getSubject();
                    $mail->Body    = $this->getContent_html();
                    $mail->AltBody = $this->getContent_txt();
                    $mail->send();
                    return true;
                } catch (Exception $e) {
                    return "Le mail n'a pas pu être envoyé. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                return $this->fatal_error_msg;
            }
        }
}