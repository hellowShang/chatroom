<?php
$nickname = $_POST['nickname']??'';
if($nickname){
    setcookie('name',$nickname,time()+604800,'/','chat.com',false,true);
    echo json_encode(['num' => 1]);
}else{
    die(json_encode(['num' => 2]));
}