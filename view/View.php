<?php 

class View {
    use ColorFct, SecurityFct, DomFct, StringFct;

    const TEMPLATE_SECTION = "View/sections/";
    const TEMPLATE_SNIPPET = "View/snippet/";
    const TEMPLATE_DISPLAY = "View/display/";

    const STYLE_NONE = "none";
    const STYLE_BACK = "back";
    const STYLE_TEXT = "text";
    const STYLE_BORDER = "border";
    const STYLE_OUTLINE = "outline";
    const STYLE_UNDERLINE = "underline";

    const STYLE_INPUT_BASIC = 0;
    const STYLE_INPUT_FLOATING = 1;
    const STYLE_INPUT_ICON = 2;

    const STYLE_ICON_SOLID = "fas";
    const STYLE_ICON_REGULAR = "far";
    const STYLE_ICON_MEDIA = "media";

    const STYLE_CHECK_CHECKBOX = 0;
    const STYLE_CHECK_SWITCH = 1;
    const STYLE_CHECK_RADIO = 2;

    const SIZE_SM = "sm";
    const SIZE_MD = "md";
    const SIZE_BASIC = "";
    const SIZE_LG = "lg";
    const SIZE_XL = "xl";

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


    // Constantes des COULEUR
        const COLOR_TYPE_TEXT = 0;
        const COLOR_TYPE_BACK = 1;
        const COLOR_TYPE_BORDER = 2;
        const COLOR_TYPE_FORM_CONTROL = 3;

        const COLOR_ELEMENT_MAIN = 0;
        const COLOR_ELEMENT_BACKGROUND_MAIN = 1;
        const COLOR_ELEMENT_PRODUCT = 2;
        const COLOR_ELEMENT_MARKET = 4;
        const COLOR_ELEMENT_ARTICLE = 5;
        const COLOR_ELEMENT_MEDIA = 6;

        const COLOR_ALLOWED = [
            'red',
            'pink',
            'purple',
            'deep-purple',
            'indigo',
            'blue',
            'light-blue',
            'cyan',
            'teal',
            'green',
            'light-green',
            'lime',
            'yellow',
            'amber',
            'orange',
            'deep-orange',
            'brown',
            'grey',
            'blue-grey',
            'black',
            'transparent',
            'white',
            "main",
            "secondary",
            "tertiary"
        ];

        const COLOR_CUSTOM = array(
            "force" => "brown",
            "strong" => "brown",
            "terre" => "brown",
            "intel" => "red",
            "feu" => "red",
            "agi" => "green",
            "air" => "green",
            "chance" => "blue",
            "eau" => "blue",
            "vitality" => "amber",
            "sagesse" => "purple",
            "life" => "deep-orange",
            "level" => "teal",
            "dodge_pa" => "yellow",
            "dodge_pm" => "lime",
            "po" => "teal",
            "po-editable" => "blue-grey",
            "pa" => "deep-orange",
            "pm" => "cyan",
            "cast-per-turn" => "purple",
            "sight-line" => "indigo",
            "number-between-two-cast" => "pink",
            "ini" => "deep-purple",
            "invocation" => "amber",
            "touch" => "lime",
            "actif" => "amber",
            "twohands" => "grey",
            "kamas" => "yellow",
            "ca" => "grey",
            "fuite" => "light-green",
            "tacle" => "cyan",
            "neutre" => "grey",
            "shield" => "blue-grey",
            "terre-feu" => ["brown", "red"],
            "terre-air" => ["brown", "green"],
            "terre-eau" => ["brown", "blue"],
            "feu-air" => ["red", "green"],
            "feu-eau" => ["red", "blue"],
            "air-eau" => ["green", "blue"],
            "terre-feu-air" => ["brown","red", "green"],
            "terre-feu-eau" => ["brown","red", "blue"],
            "terre-air-eau" => ["brown","green", "blue"],
            "feu-air-eau" => ["red","green", "blue"],
            "terre-feu-air-eau" => ["brown","red","green", "blue"]
        );

        const COLOR_TO_HEX = array(
            'black' => "#000000", 
            'white' => "#000000", 
            'transparent' => "#00BFFF00", 
            'red-l-5' => "#ffebee",
            'red-l-4' => "#ffcdd2",
            'red-l-3' => "#ef9a9a",
            'red-l-2' => "#e57373",
            'red-l-1' => "#ef5350",
            'red' => "#f44336",
            'red-d-1' => "#e53935",
            'red-d-2' => "#d32f2f",
            'red-d-3' => "#c62828",
            'red-d-4' => "#b71c1c",
            'red-d-5' => "#881515",
            'red-a-1' => "#ff8a80",
            'red-a-2' => "#ff5252",
            'red-a-3' => "#ff1744",
            'red-a-4' => "#d50000",
            'pink-l-5' => "#fce4ec",
            'pink-l-4' => "#f8bbd0",
            'pink-l-3' => "#f48fb1",
            'pink-l-2' => "#f06292",
            'pink-l-1' => "#ec407a",
            'pink' => "#e91e63",
            'pink-d-1' => "#d81b60",
            'pink-d-2' => "#c2185b",
            'pink-d-3' => "#ad1457",
            'pink-d-4' => "#880e4f",
            'pink-d-5' => "#4e082e",
            'pink-a-1' => "#ff80ab",
            'pink-a-2' => "#ff4081",
            'pink-a-3' => "#f50057",
            'pink-a-4' => "#c51162",
            'purple-l-5' => "#f3e5f5",
            'purple-l-4' => "#e1bee7",
            'purple-l-3' => "#ce93d8",
            'purple-l-2' => "#ba68c8",
            'purple-l-1' => "#ab47bc",
            'purple' => "#9c27b0",
            'purple-d-1' => "#8e24aa",
            'purple-d-2' => "#7b1fa2",
            'purple-d-3' => "#6a1b9a",
            'purple-d-4' => "#4a148c",
            'purple-d-5' => "#270a4b",
            'purple-a-1' => "#ea80fc",
            'purple-a-2' => "#e040fb",
            'purple-a-3' => "#d500f9",
            'purple-a-4' => "#aa00ff",
            'deep-purple-l-5' => "#ede7f6",
            'deep-purple-l-4' => "#d1c4e9",
            'deep-purple-l-3' => "#b39ddb",
            'deep-purple-l-2' => "#9575cd",
            'deep-purple-l-1' => "#7e57c2",
            'deep-purple' => "#673ab7",
            'deep-purple-d-1' => "#5e35b1",
            'deep-purple-d-2' => "#512da8",
            'deep-purple-d-3' => "#4527a0",
            'deep-purple-d-4' => "#311b92",
            'deep-purple-d-5' => "#180d49",
            'deep-purple-a-1' => "#b388ff",
            'deep-purple-a-2' => "#7c4dff",
            'deep-purple-a-3' => "#651fff",
            'deep-purple-a-4' => "#6200ea",
            'indigo-l-5' => "#e8eaf6",
            'indigo-l-4' => "#c5cae9",
            'indigo-l-3' => "#9fa8da",
            'indigo-l-2' => "#7986cb",
            'indigo-l-1' => "#5c6bc0",
            'indigo' => "#3f51b5",
            'indigo-d-1' => "#3949ab",
            'indigo-d-2' => "#303f9f",
            'indigo-d-3' => "#283593",
            'indigo-d-4' => "#1a237e",
            'indigo-d-5' => "#0f1449",
            'indigo-a-1' => "#8c9eff",
            'indigo-a-2' => "#536dfe",
            'indigo-a-3' => "#3d5afe",
            'indigo-a-4' => "#304ffe",
            'blue-l-5' => "#e3f2fd",
            'blue-l-4' => "#bbdefb",
            'blue-l-3' => "#90caf9",
            'blue-l-2' => "#64b5f6",
            'blue-l-1' => "#42a5f5",
            'blue' => "#2196f3",
            'blue-d-1' => "#1e88e5",
            'blue-d-2' => "#1976d2",
            'blue-d-3' => "#1565c0",
            'blue-d-4' => "#0d47a1",
            'blue-d-5' => "#09326e",
            'blue-a-1' => "#82b1ff",
            'blue-a-2' => "#448aff",
            'blue-a-3' => "#2979ff",
            'blue-a-4' => "#2962ff",
            'light-blue-l-5' => "#e1f5fe",
            'light-blue-l-4' => "#b3e5fc",
            'light-blue-l-3' => "#81d4fa",
            'light-blue-l-2' => "#4fc3f7",
            'light-blue-l-1' => "#29b6f6",
            'light-blue' => "#03a9f4",
            'light-blue-d-1' => "#039be5",
            'light-blue-d-2' => "#0288d1",
            'light-blue-d-3' => "#0277bd",
            'light-blue-d-4' => "#01579b",
            'light-blue-d-5' => "#00365f",
            'light-blue-a-1' => "#80d8ff",
            'light-blue-a-2' => "#40c4ff",
            'light-blue-a-3' => "#00b0ff",
            'light-blue-a-4' => "#0091ea",
            'cyan-l-5' => "#e0f7fa",
            'cyan-l-4' => "#b2ebf2",
            'cyan-l-3' => "#80deea",
            'cyan-l-2' => "#4dd0e1",
            'cyan-l-1' => "#26c6da",
            'cyan' => "#00bcd4",
            'cyan-d-1' => "#00acc1",
            'cyan-d-2' => "#0097a7",
            'cyan-d-3' => "#00838f",
            'cyan-d-4' => "#006064",
            'cyan-d-5' => "#004042",
            'cyan-a-1' => "#84ffff",
            'cyan-a-2' => "#18ffff",
            'cyan-a-3' => "#00e5ff",
            'cyan-a-4' => "#00b8d4",
            'teal-l-5' => "#e0f2f1",
            'teal-l-4' => "#b2dfdb",
            'teal-l-3' => "#80cbc4",
            'teal-l-2' => "#4db6ac",
            'teal-l-1' => "#26a69a",
            'teal' => "#009688",
            'teal-d-1' => "#00897b",
            'teal-d-2' => "#00796b",
            'teal-d-3' => "#00695c",
            'teal-d-4' => "#004d40",
            'teal-d-5' => "#002b23",
            'teal-a-1' => "#a7ffeb",
            'teal-a-2' => "#64ffda",
            'teal-a-3' => "#1de9b6",
            'teal-a-4' => "#00bfa5",
            'green-l-5' => "#e8f5e9",
            'green-l-4' => "#c8e6c9",
            'green-l-3' => "#a5d6a7",
            'green-l-2' => "#81c784",
            'green-l-1' => "#66bb6a",
            'green' => "#4caf50",
            'green-d-1' => "#43a047",
            'green-d-2' => "#388e3c",
            'green-d-3' => "#2e7d32",
            'green-d-4' => "#1b5e20",
            'green-d-5' => "#0e3110",
            'green-a-1' => "#b9f6ca",
            'green-a-2' => "#69f0ae",
            'green-a-3' => "#00e676",
            'green-a-4' => "#00c853",
            'light-green-l-5' => "#f1f8e9",
            'light-green-l-4' => "#dcedc8",
            'light-green-l-3' => "#c5e1a5",
            'light-green-l-2' => "#aed581",
            'light-green-l-1' => "#9ccc65",
            'light-green' => "#8bc34a",
            'light-green-d-1' => "#7cb342",
            'light-green-d-2' => "#689f38",
            'light-green-d-3' => "#558b2f",
            'light-green-d-4' => "#33691e",
            'light-green-d-5' => "#1d3b10",
            'light-green-a-1' => "#ccff90",
            'light-green-a-2' => "#b2ff59",
            'light-green-a-3' => "#76ff03",
            'light-green-a-4' => "#64dd17",
            'lime-l-5' => "#f9fbe7",
            'lime-l-4' => "#f0f4c3",
            'lime-l-3' => "#e6ee9c",
            'lime-l-2' => "#dce775",
            'lime-l-1' => "#d4e157",
            'lime' => "#cddc39",
            'lime-d-1' => "#c0ca33",
            'lime-d-2' => "#afb42b",
            'lime-d-3' => "#9e9d24",
            'lime-d-4' => "#827717",
            'lime-d-5' => "#50490e",
            'lime-a-1' => "#f4ff81",
            'lime-a-2' => "#eeff41",
            'lime-a-3' => "#c6ff00",
            'lime-a-4' => "#aeea00",
            'yellow-l-5' => "#fffde7",
            'yellow-l-4' => "#fff9c4",
            'yellow-l-3' => "#fff59d",
            'yellow-l-2' => "#fff176",
            'yellow-l-1' => "#ffee58",
            'yellow' => "#ffeb3b",
            'yellow-d-1' => "#fdd835",
            'yellow-d-2' => "#fbc02d",
            'yellow-d-3' => "#f9a825",
            'yellow-d-4' => "#f57f17",
            'yellow-d-5' => "#83440e",
            'yellow-a-1' => "#ffff8d",
            'yellow-a-2' => "#ffff00",
            'yellow-a-3' => "#ffea00",
            'yellow-a-4' => "#ffd600",
            'amber-l-5' => "#fff8e1",
            'amber-l-4' => "#ffecb3",
            'amber-l-3' => "#ffe082",
            'amber-l-2' => "#ffd54f",
            'amber-l-1' => "#ffca28",
            'amber' => "#ffc107",
            'amber-d-1' => "#ffb300",
            'amber-d-2' => "#ffa000",
            'amber-d-3' => "#ff8f00",
            'amber-d-4' => "#ff6f00",
            'amber-d-5' => "#8d3e01",
            'amber-a-1' => "#ffe57f",
            'amber-a-2' => "#ffd740",
            'amber-a-3' => "#ffc400",
            'amber-a-4' => "#ffab00",
            'orange-l-5' => "#fff3e0",
            'orange-l-4' => "#ffe0b2",
            'orange-l-3' => "#ffcc80",
            'orange-l-2' => "#ffb74d",
            'orange-l-1' => "#ffa726",
            'orange' => "#ff9800",
            'orange-d-1' => "#fb8c00",
            'orange-d-2' => "#f57c00",
            'orange-d-3' => "#ef6c00",
            'orange-d-4' => "#e65100",
            'orange-d-5' => "#993601",
            'orange-a-1' => "#ffd180",
            'orange-a-2' => "#ffab40",
            'orange-a-3' => "#ff9100",
            'orange-a-4' => "#ff6d00",
            'deep-orange-l-5' => "#fbe9e7",
            'deep-orange-l-4' => "#ffccbc",
            'deep-orange-l-3' => "#ffab91",
            'deep-orange-l-2' => "#ff8a65",
            'deep-orange-l-1' => "#ff7043",
            'deep-orange' => "#ff5722",
            'deep-orange-d-1' => "#f4511e",
            'deep-orange-d-2' => "#e64a19",
            'deep-orange-d-3' => "#d84315",
            'deep-orange-d-4' => "#bf360c",
            'deep-orange-d-5' => "#922706",
            'deep-orange-a-1' => "#ff9e80",
            'deep-orange-a-2' => "#ff6e40",
            'deep-orange-a-3' => "#ff3d00",
            'deep-orange-a-4' => "#dd2c00",
            'brown-l-5' => "#efebe9",
            'brown-l-4' => "#d7ccc8",
            'brown-l-3' => "#bcaaa4",
            'brown-l-2' => "#a1887f",
            'brown-l-1' => "#8d6e63",
            'brown' => "#795548",
            'brown-d-1' => "#6d4c41",
            'brown-d-2' => "#5d4037",
            'brown-d-3' => "#4e342e",
            'brown-d-4' => "#3e2723",
            'brown-d-5' => "#251714",
            'grey-l-5' => "#fafafa",
            'grey-l-4' => "#f5f5f5",
            'grey-l-3' => "#eeeeee",
            'grey-l-2' => "#e0e0e0",
            'grey-l-1' => "#bdbdbd",
            'grey' => "#9e9e9e",
            'grey-d-1' => "#757575",
            'grey-d-2' => "#616161",
            'grey-d-3' => "#424242",
            'grey-d-4' => "#212121",
            'grey-d-5' => "#141414",
            'blue-grey-l-5' => "#eceff1",
            'blue-grey-l-4' => "#cfd8dc",
            'blue-grey-l-3' => "#b0bec5",
            'blue-grey-l-2' => "#90a4ae",
            'blue-grey-l-1' => "#78909c",
            'blue-grey' => "#607d8b",
            'blue-grey-d-1' => "#546e7a",
            'blue-grey-d-2' => "#455a64",
            'blue-grey-d-3' => "#37474f",
            'blue-grey-d-4' => "#263238",
            'blue-grey-d-5' => "#161d20"
        );
}