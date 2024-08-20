<?php
function fanghongPlugin() {


    if((strpos($_SERVER['HTTP_USER_AGENT'], 'QQ/') || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !==false)){
        echo <<<html
<html>
<head>
    <meta charset="UTF-8">
    <title>请使用浏览器打开</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta name="format-detection" content="telephone=no">
    <meta content="false" name="twcClient" id="twcClient">
    <meta name="aplus-touch" content="1">
    <style>
    *{
        padding: 0;
        margin: 0;
    }
    body{
        background: #F6F6F6;
    }
    .img1{
        width: 100%;
        max-width: 700px;
        display: block;
        margin: 0 auto;
    }
    .img2{
        width: calc(100% - 50px);
        margin: 0 auto;
        display: block;
        margin-top: 50px;
        max-width: 700px;
    }
    </style>
</head>
<body>
    
    <img src="/template/fanghong/q.png" class="img1" />
    <img src="/template/fanghong/img2.png" class="img2" />
    
    

</body>
</html>



html;

        die;

    }



}

addAction('init', 'fanghongPlugin');



?>