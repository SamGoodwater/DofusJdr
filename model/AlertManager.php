<?php
class AlertManager
{
    function __construct(Alert $alert = null)
    {
        if(!isset($_SESSION['alerts']))
        {
            $_SESSION['alerts'] = array();
        }
        if(!empty($alert)){
            $this->add($alert);
        }
    }

    public function getAlerts(){
        return $_SESSION['alerts'];
    }

    public function add(Alert $alert)
    {
        $_SESSION['alerts'][] = $alert;
    }
    public function deleteAllAlerts()
    {
        $_SESSION['alerts'] = array();
    }
}
