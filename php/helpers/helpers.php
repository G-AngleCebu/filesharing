<?php 

require_once 'config/config.php';

# Return base url with trailing slash
function get_base_url(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }

    // BASE_URL is defined in config/config.php
    $baseUrl = BASE_URL;
    
    // return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $protocol . "://" . $baseUrl;
}

?>