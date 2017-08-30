<?php

function renderTemplate($template, $templateData){

	if (!isset($template)) {
        return "";
    }

	ob_start();

    require_once $template;

    return ob_get_clean();
}

?>