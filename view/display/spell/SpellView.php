<?php

class SpellView extends View {

    public function __construct() {
        // Ajoutez le code de construction ici
    }

    public function renderTemplate($template, $data) {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }

}

?>