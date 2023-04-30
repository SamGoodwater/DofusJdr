<?php
trait SecurityFct
{
    // TOKENS
        protected $_security_path_token_file = "medias/safeDir/token.txt";
        protected $_security_token_separator = "$";
        
        function isTokenValid($token) {
            $this->cleanExpiredTokens();
            $now = time();
            $file_contents = file_get_contents($this->_security_path_token_file);
            $tokens = array_filter(explode($this->_security_token_separator, $file_contents));
            foreach ($tokens as $line) {
                $line = explode("|", $line);
                if(is_array($line)){
                    if(count($line) == 2){
                        if($line[0] == $token){
                            if ($now > $line[1]) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        }
        function generateAndSaveToken() {
            $this->cleanExpiredTokens();
            $token = bin2hex(random_bytes(64));
            $expire = time() + (3600 * 24); // expire dans 24 heures
            $data = $token . '|' . $expire . $this->_security_token_separator;
            file_put_contents($this->_security_path_token_file, $data, FILE_APPEND);
            return $token;
        }
        function cleanExpiredTokens() {
            $now = time();
            $file_contents = file_get_contents($this->_security_path_token_file);
            $tokens = array_filter(explode($this->_security_token_separator, $file_contents));
            $valid_tokens = array();
            foreach ($tokens as $line) {
                if(!empty($line)){
                    list($t, $expire) = explode("|", $line);
                    if ($now <= $expire) {
                        $valid_tokens[] = $line . $this->_security_token_separator;
                    }
                }
            }
            file_put_contents($this->_security_path_token_file, implode("", $valid_tokens));
        }

        function securite($data, $hard_secure = false){ // Permet de protéger toutes les données reçu depuis la base de donnée
            if(is_array($data)){
                $new_data = array();
                foreach ($data as $key => $value) {
                    $new_data[$key] =   $this->securite($value);
                }
                return $new_data;
            } else {
                if(is_string($data)){
                    if($hard_secure){
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = strip_tags($data);
                        $data = $this->removeScript($data);
                        $data = htmlspecialchars_decode($data, ENT_QUOTES);
                        return $data;
                    } else {
                        return htmlspecialchars_decode($data, ENT_QUOTES);
                    }
                } else {
                    return $data;
                }
                
            }
        }
    
        function removeScript($text){
            // suppression des balises <script>
            $text = preg_replace('#<script(.*)/script>#Ui', '', $text);
            // suppression des balises <iframe>
            $text = preg_replace('#<iframe(.*)/iframe>#Ui', '', $text);
            // suppression des balises <form>
            $text = preg_replace('#<form(.*)/form>#Ui', '', $text);
            // suppression des attributs js
            $text = preg_replace('#on[a-zA-Z]*(=|:)#Ui', '', $text);
    
            return $text;
        }

}
