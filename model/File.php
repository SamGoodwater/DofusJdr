<?php

class File
{
    private $_path = '';
    private $_name = '';
    private $_extention = '';
    private $_size = 0;
    private $_dirname = "";

    public function __construct(string $path){
        if(is_file($path)){
            $path_parts = pathinfo($path);

            $this->_path = $path;
            $this->_dirname = $path_parts['dirname'];
            $this->_name = basename($path);
            $this->_size = filesize($path);
            if(isset($path_parts['extension'])){
                $this->_extention = strtolower($path_parts['extension']);
            } else {
                $this->_extention = "";
            }
        }
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥

        public function getPath(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                default:
                    return $this->_path;
                break;
            }
        }
        public function getDirname(int $format = Content::FORMAT_BRUT){
            $dirname = $this->_dirname;
            $dirname = FileManager::formatPath($dirname, false, true);
            switch ($format) {
                default:
                    return  $dirname;
                break;
            }
        }
        public function getName(int $format = Content::FORMAT_BRUT, bool $with_extention = true){
            $path_parts = pathinfo($this->getPath());
            $name = $path_parts['filename'];

            switch ($format) {
                case Content::FORMAT_TEXT:
                    return "<span data-bs-toggle='tooltip' data-bs-placement='bottom' title='".$this->_name."'>".$name."</span>";
                break;
                default:
                    if($with_extention){
                        return $this->_name;
                    } else {
                        return $name;
                    }
                break;
            }
        }
        public function getSize(int $format = Content::FORMAT_BRUT){
            $size = $this->_size;
            if($size >= 1000000000){
                $size = round($size / 1000000000) . "Go";
            } elseif ($size >= 1000000){
                $size = round($size / 1000000) . "Mo";
            } elseif ($size >= 1000){
                $size = round($size / 1000) . "Ko";
            } else{ 
                $size = $size . "o";
            }
        
            switch ($format) {
                case Content::FORMAT_TEXT:
                    return $size;
                break;
                case Content::FORMAT_BADGE:
                    return "<span class='badge bg-dark' data-bs-toggle='tooltip' data-bs-placement='top' title='Taille du fichier'>".$size."</span>";
                break;
                default:
                    return $this->_size;
                break;
            }
        }
        public function getExtention(int $format = Content::FORMAT_BRUT, string $css = ""){
            switch (Content::FORMAT_BADGE) {
                case $format:
                    if (FileManager::isImage($this)){
                        ob_start(); ?>
                            <span class='badge text-white back-red-d-2 <?=$css?>'> <?=$this->_extention?></span>    
                        <?php return ob_get_clean();
                    } elseif (FileManager::isPdf($this)) {
                        ob_start(); ?>
                            <span class='badge text-white back-red-d-2 <?=$css?>'><?=$this->_extention?></span>     
                        <?php return ob_get_clean();
                    } elseif (FileManager::isDocument($this)) {
                        ob_start(); ?>
                            <span class='badge text-white back-blue-d-2 <?=$css?>'> <?=$this->_extention?></span> 
                        <?php return ob_get_clean();
                    } elseif (FileManager::isTableur($this)) {
                        ob_start(); ?>
                            <span class='badge text-white back-green-d-2 <?=$css?>'> <?=$this->_extention?></span> 
                        <?php return ob_get_clean();
                    } elseif (FileManager::isSlider($this)) {
                        ob_start(); ?>
                            <span class='badge text-white back-orange-d-2 <?=$css?>'> <?=$this->_extention?></span> 
                        <?php return ob_get_clean();
                    } elseif (FileManager::isArchive($this)) {
                        ob_start(); ?>
                            <span class='badge text-white back-amber-d-2 <?=$css?>'> <?=$this->_extention?></span> 
                        <?php return ob_get_clean();
                    } elseif (FileManager::isAudio($this)) {
                        ob_start(); ?>
                            <span class='badge text-white back-purple-d-2 <?=$css?>'> <?=$this->_extention?></span> 
                        <?php return ob_get_clean();
                    } elseif (FileManager::isVideo($this)) {
                        ob_start(); ?>
                            <span class='badge text-white back-indigo-d-2 <?=$css?>'> <?=$this->_extention?></span>   
                        <?php return ob_get_clean();
                    } else {
                        ob_start(); ?>
                            <span class='badge text-white back-grey-d-2 <?=$css?>'> <?=$this->_extention?></span>  
                        <?php return ob_get_clean();
                    }
                break;
                default:
                    return $this->_extention;
                break;
            }
        }
        public function getThumbnail(){
            if($this->existThumbnail()){
                return new File($this->getDirname() . $this->getName(with_extention:false) ."_thumb". $this->getExtention());
            }
        }
        public function existThumbnail(){
            if(file_exists($this->getDirname() . $this->getName(with_extention:false) ."_thumb". $this->getExtention())){
                return true;
            } else {
                return false;
            }
        }
    
        public function getVisual(Style $style = new Style()) {
            $view = new View();
            if(empty($this->getPath())){
                return "";
            }

            $dowload = "";
            if($style->getIs_download()){
                $dowload = "href='".$this->getPath()."' target='_blank'";
            }

            $thumbnail = new File($this->getPath());
            if($style->getUse_thumbnail()){
                if(!empty($style->getOther_thumbnail())){
                    $thumbnail = $style->getOther_thumbnail();
                } elseif($this->existThumbnail()){
                    $thumbnail = $this->getThumbnail();
                }
            }

            if (FileManager::isImage($this)){
                $data = Style::ICONS_FILE[FileManager::FORMAT_IMG];
            } elseif (FileManager::isPdf($this)) {
                $data = Style::ICONS_FILE[FileManager::FORMAT_PDF];
            } elseif (FileManager::isDocument($this)) {
                $data = Style::ICONS_FILE[FileManager::FORMAT_DOCUMENT];
            } elseif (FileManager::isTableur($this)) {
                $data = Style::ICONS_FILE[FileManager::FORMAT_TABLEUR];
            } elseif (FileManager::isSlider($this)) {
                $data = Style::ICONS_FILE[FileManager::FORMAT_SLIDER];
            } elseif (FileManager::isArchive($this)) {
                $data = Style::ICONS_FILE[FileManager::FORMAT_ARCHIVE];
            } elseif (FileManager::isAudio($this)) {
                $data = Style::ICONS_FILE[FileManager::FORMAT_AUDIO];
            } elseif (FileManager::isVideo($this)) {
                $data = Style::ICONS_FILE[FileManager::FORMAT_VIDEO];
            } else {
                $data = Style::ICONS_FILE[FileManager::FORMAT_OTHER];
            }
            
            switch ($style->getDisplay()) {
                case Content::FORMAT_ICON:
                    if(FileManager::isImage($this)){
                        $dirname = str_replace("medias/", "", $this->getDirname());
                        return $view->dispatch(
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_MEDIA,
                                "icon" => $this->getName(),
                                "dirfile" => $dirname,
                                "tooltip" => $this->getName(),
                                "class" => $style->getClass(), 
                                "size" => $style->getSize()
                            ], 
                            write: false);
                    }

                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_SOLID,
                            "icon" => $data["icon"],
                            "color" => $data["color"],
                            "tooltip" => key($data),
                            "content" => "",
                            "class" => $style->getClass()
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    $style_badge = $style;
                    $style_badge->setDisplay(Content::FORMAT_ICON);

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->getVisual($style_badge)} {$this->getName()}",
                            "color" => $data["color"],
                            "tooltip" => key($data),
                            "style" => Style::STYLE_OUTLINE,
                            "class" => $style->getClass()
                        ], 
                        write: false);
                

                case Content::FORMAT_VIEW:
                    if(FileManager::isImage($this)){
                        if(empty($style->getClass())){ $css = 'img-back-120';} else {$css = $style->getClass();}
                        ob_start(); ?>
                            <a id="<?=$this->getName(Content::FORMAT_BRUT, false)?>" class="d-flex justify-content-center" data-fancybox='gallery' href='<?=$this->getPath()?>' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html='true' title="<?=$this->getName()?><br><small>Cliquez pour ouvrir</small>">
                                <div class='<?=$css?>'  style="background-image:url('<?=$thumbnail->getPath()?>')"></div>
                            </a>
                        <?php return ob_get_clean();
                    } elseif (FileManager::isPdf($this)) {
                        $width = "100%"; $height = 1150;
                        if(!empty($style->getWidth())){ $width = $style->getWidth();}
                        if(!empty($style->getHeight()) && $style->getHeight() != "100%"){ $height = $style->getHeight();}

                        ob_start(); ?>
                            <embed src="<?=$this->getPath()?>" width="<?=$width?>" height="<?=$height?>" type='application/pdf'/>
                        <?php return ob_get_clean();
                    } elseif (FileManager::isDocument($this)) {
                        $width = "100%"; $height = 1150;
                        if(!empty($style->getWidth())){ $width = $style->getWidth();}
                        if(!empty($style->getHeight())){ $height = $style->getHeight();}

                        if($this->getExtention() == "doc" || $this->getExtention() == "docx"){
                            ob_start(); ?>
                                <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?=$_SERVER['SERVER_NAME'].'/'.$this->getPath()?>' width="<?=$width?>" height="<?=$height?>" frameborder='0'></iframe>
                            <?php return ob_get_clean();
                        }
                    } elseif (FileManager::isTableur($this)) {
                        $width = "100%"; $height = 1150;
                        if(!empty($style->getWidth())){ $width = $style->getWidth();}
                        if(!empty($style->getHeight())){ $height = $style->getHeight();}

                        if($this->getExtention() == "xls" || $this->getExtention() == "xlsx"){
                            ob_start(); ?>
                                <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?=$_SERVER['SERVER_NAME'].'/'.$this->getPath()?>' width="<?=$width?>" height="<?=$height?>" frameborder='0'></iframe>
                            <?php return ob_get_clean();
                        }
                    } elseif (FileManager::isSlider($this)) {
                        $width = "100%"; $height = 1150;
                        if(!empty($style->getWidth())){ $width = $style->getWidth();}
                        if(!empty($style->getHeight())){ $height = $style->getHeight();}

                        if($this->getExtention() == "ppt" || $this->getExtention() == "pptx"){
                            ob_start();?>
                                <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?=$_SERVER['SERVER_NAME'].'/'.$this->getPath()?>' width="<?=$width?>" height="<?=$height?>" frameborder='0'></iframe>
                            <?php return ob_get_clean();
                        }
                    } elseif (FileManager::isAudio($this)) {
                        ob_start(); ?>
                            <audio controls src="<?=$this->getPath()?>">
                                Votre navigateur ne supporte pas la lecture des fichiers audios
                            </audio>
                        <?php return ob_get_clean();
                    } elseif (FileManager::isVideo($this)) {
                        $width = 320; $height = 240;
                        if(!empty($style->getWidth())){ $width = $style->getWidth();}
                        if(!empty($style->getHeight())){ $height = $style->getHeight();}
                        ob_start(); ?>
                            <video width="<?=$width?>" height="<?=$height?>" controls>
                                <source src="<?=$this->getPath()?>" type="video/<?=$this->getExtention()?>">
                                Votre navigateur ne supporte pas le visionnage des vidéos
                            </video>
                        <?php return ob_get_clean();
                    }

                    ob_start(); ?>
                        <a id="<?=$this->getName(Content::FORMAT_BRUT, false)?>" class="text-center" <?=$dowload?> data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html='true' title="<?=$this->getName()?><br><small>Cliquez pour télécharger</small>">
                            <p><i class='fas fa-<?=$data["icon"]?> text-<?=$data["color"]?>-d-2 font-size-2 <?=$style->getClass()?>'></i></p>
                            <p style="width: 5rem;" class='badge text-white back-<?=$data["color"]?>-d-2 overflow-hidden text-truncate font-weight-bold mt-1'><?=$this->getName()?></p>
                        </a> 
                    <?php return ob_get_clean();
                
                default:
                    $style->setDisplay(Content::FORMAT_ICON);

                    if(FileManager::isImage($this)){
                        $style->setClass("img-back-50");
                        return $this->getVisual($style);
                    } elseif (FileManager::isAudio($this)) {
                        // Utilisation d'amplitude JS pour générer un lecteur audio en html et JS très simple. Un seul bouton play/pause
                        ob_start();?>
                            <div class="glob-player">
                                <div class="glob-btn">
                                    <span class="btn amplitude-play-pause btn-back-<?=Style::ICONS_FILE[FileManager::FORMAT_AUDIO]["color"]?>" amplitude-main-play-pause="true"><i class=" fas fa fa-play" aria-hidden="true"></i> <i class="fas fa fa-pause" aria-hidden="true"></i></span>
                                </div>
                                <div>
                                    <div class="meta-name" amplitude-song-info="name" amplitude-main-song-info="true"></div>
                                    <progress class="amplitude-song-played-progress" amplitude-main-song-played-progress="true" id="song-played-progress"></progress>
                                </div>
                            </div>
                            <script>
                                Amplitude.init({
                                    "songs": [
                                        {
                                            "name": "<?=$this->getName(with_extention:false)?>",
                                            "url": "<?=$this->getPath()?>",
                                        }
                                    ],
                                    callbacks: {
                                        //pour démarrer la lecture à cuaque fois que l'on passe au morceau suivant ou préc
                                        song_change: function () {
                                            Amplitude.play();
                                        }
                                    }
                                });
                            </script>
                        <?php return ob_get_clean();
                        
                    } elseif (FileManager::isVideo($this)) {
                        ob_start();?>
                            <div>
                                <video width="50" height="50">
                                    <source src=”<?=$this->getPath();?>” type=video/<?=$this->getExtention();?>>
                                </video>
                            </div>
                        <?php return ob_get_clean();
                    } else {
                        return $this->getVisual($style);
                    }
            }   
            
        }

}
