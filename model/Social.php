<?php
class Social extends Content
{
    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/social/default.svg",
            "dir" => "medias/social/",
            "preferential_format" => "png",
            "naming" => "[uniqid]"
        ]
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_text='';
        private $_link='';
        private $_description='';
        protected $_visible = true;
        protected $_usable = true; // surcharge de la variable de Content

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Social",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom du réseau social",
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getText(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Social",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "text",
                            "label" => "Texte associé au lien",
                            "placeholder" => "#moncompte",
                            "comment" => "Texte associé au lien affiché à coté de l'icone du réseau social ou du si, par exemple : #moncompte",
                            "value" => $this->_text,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);

                default:
                    return $this->_text;
            }
        }
        public function getLink(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Social",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "link",
                            "label" => "Lien vers le réseau social ou le site",
                            "placeholder" => "https://www.reseau.com/moncompte",
                            "value" => $this->_link,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);

                default:
                    return $this->_link;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Social",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description",
                            "label" => "Description",
                            "placeholder" => "Description du réseau social",
                            "value" => $this->_description
                        ], 
                        write: false);

                default:
                    return $this->_description;
            }
        }
        public function getVisible(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/checkbox",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "id" => "visible_" . $this->getUniqid(),
                            "input_name" => "visible",
                            "label" => $this->getVisible(Content::FORMAT_BADGE),
                            "checked" => $this->returnBool($this->_visible),
                            "style" => Style::CHECK_SWITCH
                        ], 
                        write: false);
                    
                case Content::FORMAT_BADGE:
                    if($this->_visible){ 
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "Visible",
                                "color" => "green-d-3",
                                "style" => Style::STYLE_BACK,
                                "tooltip" => "Visible au visiteurs"
                            ], 
                            write: false);
    
                    } else {
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "Invisible",
                                "color" => "red-d-3",
                                "style" => Style::STYLE_BACK,
                                "tooltip" => "Invisible aux visiteurs"
                            ], 
                            write: false);
                    }
    
                case Content::FORMAT_ICON:
                    if($this->_usable){
    
                        return $view->dispatch(
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "eye",
                                "color" => "green-d-3",
                                "tooltip" => "Visible au visiteurs"
                            ], 
                            write: false); 
    
                    } else { 
    
                        return $view->dispatch(
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "eye-slash",
                                "color" => "red-d-3",
                                "tooltip" => "Invisible aux visiteurs"
                            ], 
                            write: false);
                    }
                    
                default:
                    return $this->_visible;
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName(string | int $data){
            $this->_name = $data;
            return true;
        }
        public function setText(string | int $data){
            $this->_text = $data;
            return true;
        }
        public function setLink(string | int $data){
            $this->_link = $data;
            return true;
        }
        public function setDescription(string $data){
            $this->_description = $data;
            return true;
        }
        public function setVisible(bool | null $data){
            $this->_visible = $this->returnBool($data);
            return true;
        }
}