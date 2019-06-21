<?php
$server = new swoole_websocket_server("0.0.0.0", 9504);

$server->on('open', function($server, $req) {
    echo "connection open: {$req->fd}\n";
    $server->push($req->fd,json_encode(['id' => $req->fd],JSON_UNESCAPED_UNICODE));
});

$server->on('message', function($server, $frame) {
    echo "received message: {$frame->data}\n";
    //              用户         消息
    // $server->push($frame->fd,$frame->data);
    //    var_dump($frame->data);
    $data = json_decode($frame->data,true);
    $nickname = $data['name'];
    $ip = $data['ip'];
    $desc = $data['content'];
    $time = time();
    $id = $data['id'];
//    echo $id;echo "\n";
    $er = substr($desc,0,1);
//    echo $er;echo "\n";
    if($er == "@"){
        $type = 2;
    }else{
        $type = 1;
    }
    $db = mysqli_connect('192.168.188.138','root','123123','1809A') or die('打开数据库失败');
    $sql = "insert into chatroom(content,time,name,ip,type) values('$desc','$time','$nickname','$ip','$type')";
    mysqli_set_charset($db,'utf8');
    $res = mysqli_query($db,$sql);
    if($type == 2){
        $server->push($id,json_encode(['content' => $desc,'nickname' => $nickname,'time' => date('Y-m-d H:i:s',$time),'id' => $frame->fd],JSON_UNESCAPED_UNICODE));
        $server->push($frame->fd,json_encode(['content' => $desc,'nickname' => $nickname,'time' => date('Y-m-d H:i:s',$time),'id' => $frame->fd],JSON_UNESCAPED_UNICODE));
    }else{
        foreach($server->connections as $v){
            //检查连接是否为有效的`WebSocket`客户端连接
            if($server->isEstablished($v)){
                $server->push($v,json_encode(['content' => $desc,'nickname' => $nickname,'time' => date('Y-m-d H:i:s',$time),'id' => $frame->fd],JSON_UNESCAPED_UNICODE));
            }
        }
    }
});

$server->on('close', function($server, $fd) {
    echo "connection close: {$fd}\n";
});

$server->start();


// 建表命令
//create table chatroom (
//    id int(10) auto_increment primary key,
//    name varchar(40),
//    content varchar(256),
//    ip varchar(24),
//    type tinyint(1),
//    time int(10)
//)charset=utf8 partition by range (id) (
//    partition p0 values less than (100),
//    partition p1 values less than (200),
//    partition p2 values less than (300),
//    partition p3 values less than (400)
//);