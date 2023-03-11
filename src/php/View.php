<?php 
// Ne pas placer dans le dossier view sinon Composer inclu l'ensemble des fichiers PHP

class View {
    use SecurityFct, DomFct, StringFct;

    const TEMPLATE_SECTION = "view/sections/";
    const TEMPLATE_SNIPPET = "view/snippet/";
    const TEMPLATE_DISPLAY = "view/display/";

    //Date
    const DATE_DB = "Y-m-d";
    const DATE_TIME_DB = "Y-m-d H:i:s";
    const DATE_FR = "d-m-Y";
    const TIME_FR = "H:i:s";
    const DATE_TIME_FR = "H:i:s d-m-Y";

    private array $_data = []; // une propriété qui stocke les données utilisées pour remplir les variables du template.
    private array $_attributes = [];
    private string $_template_name = ""; //une propriété qui stocke le nom ou le chemin vers le template à utiliser pour générer la sortie HTML.
    private string $_template_type = self::TEMPLATE_SNIPPET;
    private string $_template_path = "";
    private string $_output = ""; //  une propriété qui stocke la sortie HTML générée par la vue.

    public function __construct(string $template_type = self::TEMPLATE_SNIPPET){
        if(!empty($template_type) && in_array($template_type, [self::TEMPLATE_SECTION, self::TEMPLATE_SNIPPET, self::TEMPLATE_DISPLAY])){
            $this->setTemplate_type($template_type);
        } else {
            throw new InvalidArgumentException("Le type de template n'est pas valide.");
        }
    }

    public function includeMainTemplate(){
        include_once "view/main_template.php";
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getTemplate_type(){
            return $this->_template_type;
        }
        public function getTemplate_name(){
            return $this->_template_name;
        }
        private function getTemplate_path(){
            return $this->_template_path;
        }
        public function getData(string $key = null){
            if(!empty($key)){
                return isset($this->_data[$key]) ? $this->_data[$key] : null;
            } else {
                return $this->_data;
            }   
        }
        public function getView(bool $is_render = true) {
            if($is_render){$this->render();}
            return $this->_output;
        }
        public function getAttributes(){
            return $this->_attributes;
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setTemplate_type(string $template_type){
            if(!in_array($template_type, [self::TEMPLATE_SECTION, self::TEMPLATE_SNIPPET, self::TEMPLATE_DISPLAY])){
                throw new InvalidArgumentException("Invalid template type");
            }
            $this->_template_type = $template_type;
        }
        public function setTemplate_name(string $name_file) {
            $name_file = FileManager::formatPath($name_file, false, false);
            if(!preg_match("/\.php$/", $name_file)){
                $name_file .= ".php";
            }
            $this->_template_name = $name_file;
        }
        private function setTemplate_path(){
            $filepath = $this->getTemplate_type() . $this->getTemplate_name();
            if(file_exists($filepath)){
                $this->_template_path = $filepath;
            } else {
                throw new Exception("Le template n'existe pas.");
            }
        }
        public function setData(array $data) {
            foreach ($data as $key => $value) {
                $this->_data[$key] = $this->securite($value);
            }
        }
        public function assign(string $key, $value) {
            $this->setData([$key => $value]);
        }
        // Le selecteur est en format JS #, . ou rien
        public function setAttribute(string $selector, string $name, string $value) {
            $this->_attributes[$selector][$name] = $value;
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ OTHERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function render() {
            $this->setTemplate_path();
            extract($this->getData());

            ob_start();
                include $this->getTemplate_path();
            $output = ob_get_clean();

            if(!empty($this->getAttributes()) && is_array($this->getAttributes())){
                // Ajouter les attributs à chaque élément
                foreach ($this->getAttributes() as $selector => $attributes) {
                    $pattern = sprintf('/(<%s[^>]*)/i', preg_quote($selector, '/'));
                    $replace = '$1 ' . $this->getAttributeString($attributes);
                    $this->_output = preg_replace($pattern, $replace, $this->_output);
                }
            }

            $this->_output = $output;
        }

        public function write(){
            $this->setTemplate_path();
            if(!empty($this->_output) && is_string($this->_output)){
                echo $this->getView(false);
            } else {
                throw new Exception("La vue n'a pas été générée.");
            }
        }

        // Raccourcis pour écrire la vue
        public function dispatch($template_name = null, $data = null, array $attributes = null, bool $write = true) {
            if(!empty($template_name)){
                $this->setTemplate_name($template_name);
            }
            if(!empty($data)){
                $this->setData($data);
            }
            if(!empty($attributes)){
                foreach ($attributes as $selector => $attr) {
                    foreach ($attr as $name => $value) {
                        $this->setAttribute($selector, $name, $value);
                    }
                }
            }
            $this->render();

            if($write){
                $this->write();
            } else {
                return $this->getView(false);
            }
        }
        static function shortcutDispatch(string $template_type = self::TEMPLATE_SNIPPET, string $template_name = null, array $data = null, array $attributes = null, bool $write = true) {
            $view = new View($template_type);
            $view->dispatch($template_name, $data, $attributes, $write);
        }

        private function getAttributeString(array $attributes) {
            $attr_str = '';
            foreach ($attributes as $name => $value) {
                $attr_str .= sprintf('%s="%s" ', $name, $value);
            }
            return $attr_str;
        }
}