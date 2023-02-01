<?php
class Alert
{
    public function __construct($title, $content, $link, $type, $delay = 0) // Si Delay = 0 alors l'alerte ne disparaît pas, $delay est en ms
    {
        $this->setTitle($title);
        $this->setContent($content);
        $this->setLink($link);
        $this->setType($type);
        $this->setDelay($delay);
    }

    private $_title = '';
    private $_content = '';
    private $_link='';
    private $_type='';
    private $_delay=0;

    const ALERT_TYPE = [
        'secondary',
        'primary',
        'success',
        'danger',
        'warning',
        'info',
        'light',
        'dark',
    ];
    const ALERT_WARNING = 'warning';
    const ALERT_PRIMARY = 'primary';
    const ALERT_SUCCESS = 'success';
    const ALERT_DANGER = 'danger';
    const ALERT_INFO = 'info';
    const ALERT_LIGHT = 'light';
    const ALERT_DARK = 'dark';

        // alert-secondary : gris
        // alert-primary : bleu foncé
        // alert-success : vert
        // alert-danger : rouge
        // alert-warning : jaune
        // alert-info : bleu/vert
        // alert-light : gris clair blanc
        // alert-dark : gris foncé / noir

    public function getTitle(){
        return $this->_title;
    }
    public function getContent(){
        return $this->_content;
    }
    public function getLink(){
        return $this->_link;
    }
    public function getType(){
        return $this->_type;
    }
    public function getDelay(){
        return $this->_delay;
    }

    public function setTitle($data){
        $this->_title = addslashes(htmlspecialchars($data));
    }
    public function setContent($data){
        $this->_content = addslashes(htmlspecialchars($data));
    }
    public function setLink($data){
        $this->_link = addslashes(htmlspecialchars($data));
    }
    public function setType($data){
        if(in_array($data, Alert::ALERT_TYPE))
        {
            $this->_type = $data;
        } else {
            $this->_type = 'alert-info';
        }
    }
    public function setDelay($data){
        if(is_numeric($data))
        {
            $this->_delay =$data;
        } else {
            $this->_delay=0;
        }
    }
}
