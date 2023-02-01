<?php

class File extends Content
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
                    } elseif (FileManager::isWord($this)) {
                        ob_start(); ?>
                            <span class='badge text-white back-blue-d-2 <?=$css?>'> <?=$this->_extention?></span> 
                        <?php return ob_get_clean();
                    } elseif (FileManager::isExcel($this)) {
                        ob_start(); ?>
                            <span class='badge text-white back-green-d-2 <?=$css?>'> <?=$this->_extention?></span> 
                        <?php return ob_get_clean();
                    } elseif (FileManager::isPowerpoint($this)) {
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
                return new File($this->getDirname() . "thumb/" . $this->getName());
            }
        }
        public function existThumbnail(){
            if(file_exists($this->getDirname() . "thumb/" . $this->getName())){
                return true;
            } else {
                return false;
            }
        }
    
        public function getVisual(int $format = Content::FORMAT_BRUT, string $css = "", bool $is_dowload = false, File $thumbnail = null){
            if(empty($this->getPath())){
                return "";
            }

            $dowload = "";
            if($is_dowload){
                $dowload = "href='".$this->getPath()."' target='_blank'";
            }
            $thumbnail_path = $this->getPath();
            if(!empty($thumbnail)){
                if(FileManager::isImage($thumbnail)){
                    $thumbnail_path = $thumbnail->getPath();
                } else {
                    $thumbnail_path = $this->getPath();
                }
            }
            
            switch ($format) {
                case Content::FORMAT_ICON:
                    if (FileManager::isImage($this)){
                        ob_start(); ?>
                            <a  class='text-brown-d-2 <?=$css?>' data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$this->getName()?>"><i class="fas fa-file-image"></i></a>   
                        <?php return ob_get_clean();
                    } elseif (FileManager::isPdf($this)) {
                        ob_start(); ?>
                            <a <?=$dowload?> class='text-red-d-2 <?=$css?>' data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$this->getName()?>"><i class='fas fa-file-pdf'></i></a>   
                        <?php return ob_get_clean();
                    } elseif (FileManager::isWord($this)) {
                        ob_start(); ?>
                            <a <?=$dowload?> class='text-blue-d-2 <?=$css?>' data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$this->getName()?>"><i class="fas fa-file-word"></i></a>   
                        <?php return ob_get_clean();
                    } elseif (FileManager::isExcel($this)) {
                        ob_start(); ?>
                            <a <?=$dowload?> class='text-green-d-2 <?=$css?>' data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$this->getName()?>"><i class="fas fa-file-excel"></i></a>   
                        <?php return ob_get_clean();
                    } elseif (FileManager::isPowerpoint($this)) {
                        ob_start(); ?>
                            <a <?=$dowload?> class='text-orange-d-2 <?=$css?>' data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$this->getName()?>"><i class="fas fa-file-powerpoint"></i></a>   
                        <?php return ob_get_clean();
                    } elseif (FileManager::isArchive($this)) {
                        ob_start(); ?>
                            <a <?=$dowload?> class='text-amber-d-2 <?=$css?>' data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$this->getName()?>"><i class="fas fa-file-archive"></i></a>   
                        <?php return ob_get_clean();
                    } elseif (FileManager::isAudio($this)) {
                        ob_start(); ?>
                            <a <?=$dowload?> class='text-purple-d-2 <?=$css?>' data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$this->getName()?>"><i class="fas fa-file-audio"></i></a>   
                        <?php return ob_get_clean();
                    } elseif (FileManager::isVideo($this)) {
                        ob_start(); ?>
                            <a <?=$dowload?> class='text-main-d-2 <?=$css?>' data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$this->getName()?>"><i class="fas fa-file-video"></i></a>   
                        <?php return ob_get_clean();
                    } else {
                        ob_start(); ?>
                            <a <?=$dowload?> class='text-grey-d-2 <?=$css?>' data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$this->getName()?>"><i class="fas fa-file"></i></a>   
                        <?php return ob_get_clean();
                    }
                break;

                case Content::FORMAT_VIEW:
                    $icon =""; $color = "";
                    if (FileManager::isImage($this)){
                        if(empty($css)){ $css = 'img-back-120';}
                        ob_start(); ?>
                            <a id="<?=$this->getName(Content::FORMAT_BRUT, false)?>" class="d-flex justify-content-center" data-fancybox='gallery' href='<?=$this->getPath()?>' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html='true' title="<?=$this->getName()?><br><small>Cliquez pour ouvrir</small>">
                                <div class='<?=$css?>'  style="background-image:url('<?=$thumbnail_path?>')"></div>
                            </a>
                        <?php return ob_get_clean();

                    } elseif (FileManager::isPdf($this)) {
                        ob_start(); ?>
                            <embed src="<?=$this->getPath()?>" width="100%" height="1150" type='application/pdf'/>
                        <?php return ob_get_clean();
                    } elseif (FileManager::isWord($this)) {
                        if($this->getExtention() == "doc" || $this->getExtention() == "docx"){
                            ob_start(); //('.ppt' '.pptx' '.doc', '.docx', '.xls', '.xlsx') ?>
                                <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?=$_SERVER['SERVER_NAME'].'/'.$this->getPath()?>' width="100%" height="1150" frameborder='0'></iframe>
                            <?php return ob_get_clean();
                        }
                        $icon = "fas fa-file-word";
                        $color = "blue";
                    } elseif (FileManager::isExcel($this)) {
                        if($this->getExtention() == "xls" || $this->getExtention() == "xlsx"){
                            ob_start(); //('.ppt' '.pptx' '.doc', '.docx', '.xls', '.xlsx') ?>
                                <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?=$_SERVER['SERVER_NAME'].'/'.$this->getPath()?>' width="100%" height="1150" frameborder='0'></iframe>
                            <?php return ob_get_clean();
                        }
                        $icon = "fas fa-file-excel";
                        $color = "green";
                    } elseif (FileManager::isPowerpoint($this)) {
                        if($this->getExtention() == "ppt" || $this->getExtention() == "pptx"){
                            ob_start(); //('.ppt' '.pptx' '.doc', '.docx', '.xls', '.xlsx') ?>
                                <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?=$_SERVER['SERVER_NAME'].'/'.$this->getPath()?>' width="100%" height="1150" frameborder='0'></iframe>
                            <?php return ob_get_clean();
                        }
                        $icon = "fas fa-file-powerpoint";
                        $color = "orange";
                    } elseif (FileManager::isArchive($this)) {
                        $icon = "fas fa-file-archive";
                        $color = "amber";
                    } elseif (FileManager::isAudio($this)) {
                        ob_start(); ?>
                            <audio controls src="<?=$this->getPath()?>">
                                Votre navigateur ne supporte pas la lecture des fichiers audios
                            </audio>
                        <?php return ob_get_clean();
                    } elseif (FileManager::isVideo($this)) {
                        ob_start(); ?>
                            <video width="320" height="240" controls>
                                <source src="<?=$this->getPath()?>" type="video/<?=$this->getExtention()?>">
                                Votre navigateur ne supporte pas le visionnage des vidéos
                            </video>
                        <?php return ob_get_clean();
                    } else {
                        $icon = "fas fa-file";
                        $color = "grey";
                    }

                    ob_start(); ?>
                        <a id="<?=$this->getName(Content::FORMAT_BRUT, false)?>" class="text-center" <?=$dowload?> data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html='true' title="<?=$this->getName()?><br><small>Cliquez pour télécharger</small>">
                            <p><i class='<?=$icon?> text-<?=$color?>-d-2 font-size-2 <?=$css?>'></i></p>
                            <p style="width: 5rem;" class='badge text-white back-<?=$color?>-d-2 overflow-hidden text-truncate font-weight-bold mt-1'><?=$this->getName()?></p>
                        </a> 
                    <?php return ob_get_clean();
                
                default:
                    $icon =""; $color = "";
                    if (FileManager::isImage($this)){
                        if(empty($css)){ $css = 'img-back-50';}
                        ob_start(); ?>
                            <a class="d-flex justify-content-center" data-fancybox='gallery' href='<?=$this->getPath()?>' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html='true' title="<?=$this->getName()?><br><small>Cliquez pour ouvrir</small>">
                                <div class='<?=$css?>'  style="background-image:url('<?=$thumbnail_path?>')"></div>
                            </a>
                        <?php return ob_get_clean();
                    } elseif (FileManager::isPdf($this)) {
                        $icon = "fas fa-file-pdf";
                        $color = "red";
                    } elseif (FileManager::isWord($this)) {
                        $icon = "fas fa-file-word";
                        $color = "blue";
                    } elseif (FileManager::isExcel($this)) {
                        $icon = "fas fa-file-excel";
                        $color = "green";
                    } elseif (FileManager::isPowerpoint($this)) {
                        $icon = "fas fa-file-powerpoint";
                        $color = "orange";
                    } elseif (FileManager::isArchive($this)) {
                        $icon = "fas fa-file-archive";
                        $color = "amber";
                    } elseif (FileManager::isAudio($this)) {
                        $icon = "fas fa-file-audio";
                        $color = "purple";
                    } elseif (FileManager::isVideo($this)) {
                        $icon = "fas fa-file-video";
                        $color = "indigo";
                    } else {
                        $icon = "fas fa-file";
                        $color = "grey";
                    }

                    ob_start(); ?>
                        <a id="<?=$this->getName(Content::FORMAT_BRUT, false)?>" class="text-center" <?=$dowload?> data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html='true' title="<?=$this->getName()?><br><small>Cliquez pour télécharger</small>">
                            <p><i class='<?=$icon?> text-<?=$color?>-d-2 font-size-2 <?=$css?>'></i></p>
                            <p style="width: 5rem;" class='badge text-white back-<?=$color?>-d-2 overflow-hidden text-truncate font-weight-bold mt-1'><?=$this->getName()?></p>
                        </a> 
                    <?php return ob_get_clean();
            }   
            
        }

}