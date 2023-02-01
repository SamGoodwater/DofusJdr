<?php 

// Pour créer une classe View dans votre système Model-View-Controller (MVC) basé sur PHP, voici les étapes principales que vous devriez suivre:

// Créez une classe View qui prend en entrée un objet issu d'un modèle.
// Utilisez des mini-templates pour générer les parties répétitives de la sortie HTML. Ces mini-templates peuvent se trouver dans un répertoire spécifique pour une meilleure identification.
// Utilisez la fonction str_replace() de PHP pour remplacer les variables modifiables dans les mini-templates. Il est important de bien nommer les variables pour les identifier facilement et de prévoir des valeurs par défaut pour éviter les erreurs.
// Sécurisez les variables en utilisant les générateurs de template pour échapper les variables automatiquement, en échappant manuellement les variables, en sanitizing les données avant tout insert ou update dans la base de données.
// Utilisez les méthodes de votre classe View pour générer des parties spécifiques de la sortie HTML, par exemple pour les inputs répétitifs.
// Utilisez la classe View dans vos contrôleurs pour générer la sortie HTML et fournir celle-ci à l'utilisateur final.
// Il est important de s'assurer que toutes les variables sont correctement échappées et que les données sont valides avant d'être utilisées. Il est également important de surveiller les performances pour vérifier que la solution choisie est adaptée à vos besoins et de tester régulièrement votre système pour s'assurer qu'il est sécurisé.

class View {
    const TEMPLATE_SECTION = "View/sections/";
    const TEMPLATE_SNIPPET = "View/snippet/";

    //Date
    const DATE_DB = "Y-m-d";
    const DATE_TIME_DB = "Y-m-d H:i:s";
    const DATE_FR = "d-m-Y";
    const TIME_FR = "H:i:s";
    const DATE_TIME_FR = "H:i:s d-m-Y";

    private $_model = null; // une propriété qui stocke l'objet issu d'un modèle qui est passé à la vue.
    private $_data = []; // une propriété qui stocke les données utilisées pour remplir les variables du template.
    private $_attributes = [];
    private $_property = ""; // stock la propriété qui doit être utilisé pour générer le template. Par exemple pour un input, on lui donnera le "name", "description", ...
    private $_template = ""; //une propriété qui stocke le nom ou le chemin vers le template à utiliser pour générer la sortie HTML.
    private $_output = ""; //  une propriété qui stocke la sortie HTML générée par la vue.

    public function __construct(object $obj = null, $template_type = self::TEMPLATE_SNIPPET, $template_name_file = null){
        if(!empty($obj)){
            $this->setModel($obj);
        }
        if(!empty($template_name_file) && !empty($template_type)){
            $this->setTemplate($template_type, $template_name_file);
        }
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getModel(){
            return $this->_model;
        }
        public function getProperty(){
            return $this->_property;
        }
        public function getData($key){
            return isset($this->_data[$key]) ? $this->_data[$key] : null;
        }
        public function getView($is_render = true) {
            if($is_render){$this->render();}
            return $this->_output;
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setModel(object $obj){
            if(is_object($obj)){
                $this->_model = $obj;
            }
            throw new InvalidArgumentException("La variable entrée n'est pas un objet.");
        }
        public function setProperty(string $property){
            if(is_string($property)){
                $this->_property = $property;
            }
            throw new InvalidArgumentException("La variable entrée n'est pas une chaine de caractère.");
        }
        public function setTemplate($template_type = self::TEMPLATE_SNIPPET, $name_file) {
            if($template_type != self::TEMPLATE_SECTION && $template_type != self::TEMPLATE_SNIPPET){
                throw new InvalidArgumentException("Invalid template type");
            }
            $filepath = $template_type . $name_file . ".php";
            if(!file_exists($filepath)){
                throw new Exception("Template file does not exist at: " . $filepath);
            }
            if(empty($this->getModel())){
                throw new Exception("Invalid model.");
            }
            ob_start();
                include $filepath;
            $this->_template = ob_get_clean();
        }
        public function setData(array $data) {
            foreach ($data as $key => $value) {
                $this->_data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }
        // Le selecteur est en format JS #, . ou rien
        public function setAttribute($selector, $name, $value) {
            $this->_attributes[$selector][$name] = $value;
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ OTHERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function render() {
            $output = $this->_template;

            // Use str_replace to replace variables in template with data
            if(!empty($this->_template) && !empty($this->_data) && is_array($this->_data)){
                foreach ($this->_data as $key => $value) {
                    $output = str_replace('{' . $key . '}', $value, $output);
                }
            } 
            if(!empty($this->_attributes) && is_array($this->_attributes)){
                // Ajouter les attributs à chaque élément
                foreach ($this->_attributes as $selector => $attributes) {
                    $pattern = sprintf('/(<%s[^>]*)/i', preg_quote($selector, '/'));
                    $replace = '$1 ' . $this->getAttributeString($attributes);
                    $this->_output = preg_replace($pattern, $replace, $this->_output);
                }
            }

            $this->_output = $output;
        }

        private function getAttributeString(array $attributes) {
            $attr_str = '';
            foreach ($attributes as $name => $value) {
                $attr_str .= sprintf('%s="%s" ', $name, $value);
            }
            return $attr_str;
        }
}