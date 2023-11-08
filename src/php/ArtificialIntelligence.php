<?php 
class ArtificialIntelligence {
    use SecurityFct;

    const ROOT_PATH = "src/php/chatgpt_templates/";
 
    private $_openai_api = "";
    private $_openai_system_content="";
    private $_openai_endpoint = "https://api.openai.com/v1/chat/completions";
    private $_openai_model = "gpt-3.5-turbo";
    private $_openai_max_tokens = 150;
    private $_openai_temperature = 0.7;

    private $_data = [];
    private $_template_name_file = "";

    private $_prompt = ""; //msg pour chatgpt
    private $_output = ""; //msg de chatgpt

    public function __construct(){
        $this->setOpenai_api($GLOBALS['project']['openai_api']);
        $this->setOpenai_system_content($GLOBALS['project']['openai_system_content']);
    }
    public function getOpenai_api(){
        return $this->_openai_api;
    }
    public function setOpenai_api(string $api){
        $this->_openai_api = $api;
        return true;
    }
    public function getOpenai_system_content(){
        return $this->_openai_system_content;
    }
    public function setOpenai_system_content(string $content){
        $this->_openai_system_content = $content;
        return true;
    }
    public function getOpenai_endpoint(){
        return $this->_openai_endpoint;
    }
    public function setOpenai_endpoint(string $endpoint){
        $this->_openai_endpoint = $endpoint;
        return true;
    }
    public function getOpenai_model(){
        return $this->_openai_model;
    }
    public function setOpenai_model(string $model){
        $this->_openai_model = $model;
        return true;
    }
    public function getOpenai_max_tokens(){
        return $this->_openai_max_tokens;
    }
    public function setOpenai_max_tokens(int $max_tokens){
        if($max_tokens > 0 && $max_tokens <= 500){
            return "Le nombre de tokens doit être compris entre 1 et 500.";
        }
        $this->_openai_max_tokens = $max_tokens;
        return true;
    }
    public function getOpenai_temperature(){
        return $this->_openai_temperature;
    }
    public function setOpenai_temperature(float $temperature){
        if($temperature < 0 || $temperature > 1){
            return "La température doit être comprise entre 0 et 1.";
        }
        $this->_openai_temperature = $temperature;
        return true;
    }

    public function getData(string $key = null){
        if(!empty($key)){
            return isset($this->_data[$key]) ? $this->_data[$key] : null;
        } else {
            return $this->_data;
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
    public function getTemplate_name_file(){
        return $this->_template_name_file;
    }
    public function setTemplate_name_file(string $name_file){
        $this->_template_name_file = $name_file;
    }

    public function loadTemplate(string $name_file){
        $name_file = FileManager::formatPath($name_file, false, false);
        if(!preg_match("/\.php$/", $name_file)){
            $name_file .= ".php";
        }
        $this->setTemplate_name_file($name_file);
        $template_path = FileManager::formatPath(self::ROOT_PATH . $name_file, false, false);
        if(empty($template_path)){return "Le template n'existe pas.";}
        if(!file_exists($template_path)){return "Le template n'existe pas.";}
        if(!empty($this->getData())){
            extract($this->getData());
        }

        include_once $template_path;
        if(!isset($template["prompt"])){
            return "Le template ne contient pas de prompt.";
        }else{
            $this->setPrompt($template["prompt"]);
            if(!empty($template["model"])){
                $this->setOpenai_model($template["model"]);
            }
            if(!empty($template["max_tokens"])){
                $this->setOpenai_max_tokens($template["max_tokens"]);
            }
            if(!empty($template["temperature"])){
                $this->setOpenai_temperature($template["temperature"]);
            }
            if(!empty($template["system_content"])){
                $this->setOpenai_system_content($template["system_content"]);
            }
        }
        return true;
    }
    public function getPrompt(){
        if(empty($this->_prompt)){
            $this->loadTemplate($this->getTemplate_name_file());
        }
        return $this->_prompt;
    }
    private function setPrompt(string $prompt){
        $this->_prompt = $prompt;
        return true;
    }

    public function getOutput(){
        return $this->_output;
    }
    private function setOutput(string $output){
        $this->_output = $output;
        return true;
    }
    
    public function call(){
        $openai_data = [
            "model" => $this->getOpenai_model(),
            "messages" => [
                [
                    "role" => "system",
                    "content" => $this->getOpenai_system_content()
                ],
                [
                    "role" => "user",
                    "content" => $this->getPrompt()
                ]
            ],
            "max_tokens" => $this->getOpenai_max_tokens(),
            "temperature" => $this->getOpenai_temperature()
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getOpenai_endpoint());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($openai_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->getOpenai_api()
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return 'Erreur cURL : ' . $error;
        }        
        curl_close($ch);

        if ($response) {
            $response_data = json_decode($response, true);
            if(isset($response_data['error'])){
                return $response_data['message'];
            }
            if(!isset($response_data['choices'][0]['message']['content'])){
                return "Une erreur s'est produite lors de l'appel à l'API.";
            }
            $response_prod = $response_data['choices'][0]['message']['content'];
            $this->setOutput($response_prod);
            return true;
        } else {
            return "Une erreur s'est produite lors de l'appel à l'API.";
        }
    }

    public function dispatch(string $name_file = null, array $data = []) : array {
        if(empty($name_file)){
            $name_file = $this->getTemplate_name_file(); 
        }
        if(!empty($data)){
            $this->setData($data);
        }
        $state = $this->loadTemplate($name_file);
        if($state !== true){
            return [
                "state" => false,
                "value" => $state
            ];
        }
        $state = $this->call();
        if($state !== true){
            return [
                "state" => false,
                "value" => $state
            ];
        }

        return [
            "state" => true,
            "value" => $this->getOutput()];
    }

    static function staticDispatch(string $name_file = null, array $data = []) : array{
        $ai = new ArtificialIntelligence();
        $state = $ai->dispatch($name_file, $data);
        if($state['state'] !== true){
            return [
                "state" => false,
                "value" => $state['value']
            ];
        }
        return [
            "state" => true,
            "value" => $state['value']
        ];
    }

    public function checkQuota(){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getOpenai_endpoint());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->getOpenai_api()
        ));

        $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'Erreur cURL : ' . curl_error($ch);
            } else {
                $response_data = json_decode($response, true);

                if (isset($response_data['data']) && isset($response_data['data'][0]['usage'])) {
                    $usage = $response_data['data'][0]['usage'];
                    return $usage['remaining'];
                } else {
                    return "Impossible de récupérer les informations d'utilisation.";
                }
            }
            curl_close($ch);
    }
}