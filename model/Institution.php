<?php
class Institution extends Content
{

    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/capabilities/default.svg",
            "dir" => "medias/modules/capabilities/",
            "preferential_format" => "jpg",
            "naming" => "[uniqid]"
        ],
        "logo_mini" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/capabilities/default.svg",
            "dir" => "medias/modules/capabilities/",
            "preferential_format" => "jpg",
            "naming" => "[uniqid]"
        ],
        "background" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/capabilities/default.svg",
            "dir" => "medias/modules/capabilities/",
            "preferential_format" => "jpg",
            "naming" => "[uniqid]"
        ]
    ];

    const STABILITY_ALPHA = "α";
    const STABILITY_BETA = "β";
    const STABILITY_STABLE = "stb";
    const STABILITY = [
        "alpha" => self::STABILITY_ALPHA,
        "béta" => self::STABILITY_BETA,
        "stable" => self::STABILITY_STABLE
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_title='';
        private $_subtitle='';
        private $_slogan='';
        private $_major_news='';
        private $_description='';
        private $_keywords='';
        private $_version='';
        private $_stability='';
        // CONTACT
        private $_author='';
        private $_email='';
        private $_phone_number='';
        private $_address='';
        private $_location_google_map='';
        // SETTINGS
        private $_bookmark_name='';
        private $_color_main='';
        private $_color_secondary='';
        private $_color_tertiary='';
        private $_color_background='';

        protected $_usable = true; // surcharge de la variable de Content

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getTitle(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "title",
                            "label" => "Nom du site",
                            "placeholder" => "Nom du site",
                            "value" => $this->_title,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_title;
            }
        }
        public function getSubtitle(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "subtitle",
                            "label" => "Sous-titre",
                            "placeholder" => "Sous-titre",
                            "value" => $this->_subtitle,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_subtitle;
            }
        }
        public function getSlogan(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "slogan",
                            "label" => "Slogan",
                            "placeholder" => "Slogan",
                            "value" => $this->_slogan,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);

                default:
                    return $this->_slogan;
            }
        }
        public function getMajor_news(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "major_news",
                            "label" => "Actualité majeure",
                            "placeholder" => "Actualité majeure",
                            "value" => $this->_major_news,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);

                default:
                    return $this->_major_news;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description",
                            "label" => "Description",
                            "placeholder" => "Description",
                            "value" => $this->_description
                        ], 
                        write: false);

                default:
                    return $this->_description;
            }
        }
        public function getKeywords(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "keywords",
                            "label" => "Mots-clés",
                            "placeholder" => "Mots-clés",
                            "value" => $this->_keywords,
                            "comment" => "Séparés chaque mots-clés par des virgules"
                        ], 
                        write: false);

                default:
                    return $this->_keywords;
            }
        }
        public function getVersion(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "version",
                            "label" => "Version",
                            "placeholder" => "Version",
                            "value" => $this->_version,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_version;
            }
        }
        public function getStability(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Institution::STABILITY as $name => $value) {
                        $items[] = [
                            "onclick" => "Institution.update('".$this->getUniqid()."', ".$value.", 'stability', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".Style::getColorFromLetter($name, true)."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Stabilité du projet",
                            "label" => $this->getStability(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_stability, Mob::SIZE)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_stability, Mob::SIZE)),
                                "color" => Style::getColorFromLetter(array_search($this->_stability, Mob::SIZE), true)."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }
                case Content::FORMAT_ICON:
                    if(in_array($this->_stability, Mob::SIZE)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => $this->_stability,
                                "color" => Style::getColorFromLetter(array_search($this->_stability, Mob::SIZE), true)."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($this->_stability, Mob::SIZE)){
                        return array_search($this->_stability, Mob::SIZE);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_stability;
            }
        }
        // CONTACT
        public function getAuthor(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "author",
                            "label" => "Auteur·trice",
                            "placeholder" => "Auteur·trice",
                            "value" => $this->_author,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_author;
            }
        }
        public function getEmail(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "email",
                            "label" => "Email",
                            "placeholder" => "Email de contact",
                            "value" => $this->_email,
                            "parttern" => "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$", 
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_email;
            }
        }
        public function getPhone_number(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "phone_number",
                            "label" => "Numéro de téléphone",
                            "placeholder" => "Numéro de téléphone",
                            "value" => $this->_phone_number,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_phone_number;
            }
        }
        public function getAddress(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "address",
                            "label" => "Adresse",
                            "placeholder" => "Adresse",
                            "value" => $this->_address
                        ], 
                        write: false);

                default:
                    return $this->_address;
            }
        }
        public function getLocation_google_map(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "location_google_map",
                            "label" => "Lien Google Map",
                            "placeholder" => "Lien Google Map",
                            "value" => $this->_location_google_map
                        ], 
                        write: false);

                default:
                    return $this->_location_google_map;
            }
        }
        // SETTINGS
        public function getBookmark_name(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Institution",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "bookmark_name",
                            "label" => "Nom du marque-page",
                            "placeholder" => "Nom du marque-page",
                            "value" => $this->_bookmark_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_bookmark_name;
            }
        }
        public function getColor_main(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Style::COLOR_ALLOWED as $name => $hexa) {
                        $items[] = [
                            "onclick" => "Institution.update('".$this->getUniqid()."', ".$name.", 'color_main', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".$name."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Couleur principale",
                            "label" => $this->getColor_main(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_main])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst($this->_color_main),
                                "color" => $this->_color_main."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }
                case Content::FORMAT_ICON:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_main])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "<i class='fa-regular fa-circle'></i>",
                                "color" => $this->_color_main."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_main])){
                        return ucfirst($this->_color_main);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_color_main;
            }
        }
        public function getColor_secondary(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Style::COLOR_ALLOWED as $name => $hexa) {
                        $items[] = [
                            "onclick" => "Institution.update('".$this->getUniqid()."', ".$name.", 'color_secondary', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".$name."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Couleur secondaire",
                            "label" => $this->getColor_secondary(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_secondary])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst($this->_color_secondary),
                                "color" => $this->_color_secondary."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }
                case Content::FORMAT_ICON:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_secondary])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "<i class='fa-regular fa-circle'></i>",
                                "color" => $this->_color_secondary."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_secondary])){
                        return ucfirst($this->_color_secondary);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_color_secondary;
            }
        }
        public function getColor_tertiary(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Style::COLOR_ALLOWED as $name => $hexa) {
                        $items[] = [
                            "onclick" => "Institution.update('".$this->getUniqid()."', ".$name.", 'color_tertiary', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".$name."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Couleur tertiaire",
                            "label" => $this->getColor_tertiary(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_tertiary])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst($this->_color_tertiary),
                                "color" => $this->_color_tertiary."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }
                case Content::FORMAT_ICON:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_tertiary])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "<i class='fa-regular fa-circle'></i>",
                                "color" => $this->_color_tertiary."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_tertiary])){
                        return ucfirst($this->_color_tertiary);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_color_tertiary;
            }
        }
        public function getColor_background(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Style::COLOR_ALLOWED as $name => $hexa) {
                        $items[] = [
                            "onclick" => "Institution.update('".$this->getUniqid()."', ".$name.", 'color_background', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".$name."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Couleur de fond",
                            "label" => $this->getColor_background(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_background])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst($this->_color_background),
                                "color" => $this->_color_background."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }
                case Content::FORMAT_ICON:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_background])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "<i class='fa-regular fa-circle'></i>",
                                "color" => $this->_color_background."-d-2",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(isset(Style::COLOR_ALLOWED[$this->_color_background])){
                        return ucfirst($this->_color_background);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_color_background;
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setTitle(string $data){
            $this->_title = $data;
            return true;
        }
        public function setSubtitle(string $data){
            $this->_subtitle = $data;
            return true;
        }
        public function setSlogan(string $data){
            $this->_slogan = $data;
            return true;
        }
        public function setMajor_news(string $data){
            $this->_major_news = $data;
            return true;
        }
        public function setDescription(string $data){
            $this->_description = $data;
            return true;
        }
        public function setKeywords(string $data){
            $this->_keywords = $data;
            return true;
        }
        public function setVersion(string $data){
            $this->_version = $data;
            return true;
        }
        public function setStability(string $data){
            if(in_array($data, Institution::STABILITY)){
                $this->_stability = $data;
                return true;
            } else {
                throw new Exception("La stabilité n'est pas valide");
            }
        }
        // CONTACT
        public function setAuthor(string $data){
            $this->_author = $data;
            return true;
        }
        public function setEmail(string $data){
            if($this->isEmail($data)){
                $this->_email = $data;
                return true;
            } else {
                throw new Exception("L'email n'est pas valide");
            }
        }
        public function setPhone_number(string $data){
            $this->_phone_number = $data;
            return true;
        }
        public function setAddress(string $data){
            $this->_address = $data;
            return true;
        }
        public function setLocation_google_map(string $data){
            $this->_location_google_map = $data;
            return true;
        }
        // SETTINGS
        public function setBookmark_name(string $data){
            $this->_bookmark_name = $data;
            return true;
        }
        public function setColor_main(string $data){
            if(isset(Style::COLOR_ALLOWED[$data])){
                $this->_color_main = $data;
                return true;
            } else {
                throw new Exception("La couleur principale n'est pas valide");
            }
        }
        public function setColor_secondary(string $data){
            if(isset(Style::COLOR_ALLOWED[$data])){
                $this->_color_secondary = $data;
                return true;
            } else {
                throw new Exception("La couleur secondaire n'est pas valide");
            }
        }
        public function setColor_tertiary(string $data){
            if(isset(Style::COLOR_ALLOWED[$data])){
                $this->_color_tertiary = $data;
                return true;
            } else {
                throw new Exception("La couleur tertiaire n'est pas valide");
            }
        }
        public function setColor_background(string $data){
            if(isset(Style::COLOR_ALLOWED[$data])){
                $this->_color_background = $data;
                return true;
            } else {
                throw new Exception("La couleur de fond n'est pas valide");
            }
        }
        
}