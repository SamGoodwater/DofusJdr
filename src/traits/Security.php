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

}
