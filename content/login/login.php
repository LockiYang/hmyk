<?php
function loginPlugin($initPluginParams) {

    if(!$initPluginParams['user']){
        if($initPluginParams['action'] != 'login'){
            header('location: /login.html');
            die;
        }
    }


}

addAction('init', 'loginPlugin');



?>