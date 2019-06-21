<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ChatRoom</title>
    <script src="http://client.lab993.com/js/jquery.js"></script>
    <style>
        .nck{
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>聊天室</h2>
    <div></div>
    <input type="text" id="content" style="width: 85%">
    <button id="btn">发送</button>
    <script>
        // 实例化websocket
        var web = new WebSocket("ws://chat.com:9504");
        // 获取nickname
        var name = "<?php echo $_COOKIE['name'];?>";
        // 获取ip
        var ip = "<?php echo $_SERVER['REMOTE_ADDR'];?>";
        // 本地缓存
        var storage = window.localStorage;
        // 定义@用户的id
        var nickid = '';
        // 打开连接
        web.onopen=function () {
            // 获取nickname
            $(document).on('click','.nck',function(){
                var nickname = $(this).text();
                nickid = $(this).attr('id');
                $('#content').val('@'+nickname+':');
            });

            // 群聊
            $("#btn").click(function(){
                var content = $("#content").val();
                var data = '';
                if(nickid == ''){
                    data = {content:content,name:name,ip:ip};
                }else{
                    data = {content:content,name:name,ip:ip,id:nickid};
                }

                // 发送数据
                web.send(JSON.stringify(data));
            });
        };


        // 接收响应
        web.onmessage=function(d){
            // 转化响应回来的数据
            var data = JSON.parse(d.data);
            console.log(data);
            // 获取本地缓存中是否由于缓存
            var values = storage.getItem(name);
            // 没有的话存本地缓存
            if(values == null){
                storage.setItem(name,data.id);
            }
            // 返回数据中没有nickname终止执行下面代码
            if(data.nickname == undefined){
                return false;
            }
            // 聊天内容追加展示
            var li = "<li><font>"+data.time+"</font>&nbsp;&nbsp;&nbsp;&nbsp;<font color='blue' class='nck' id="+data.id+">"+data.nickname+"</font>&nbsp;&nbsp;&nbsp;&nbsp;<font color='red'>"+data.content+"</font></li>";
            $('#content').before(li);
            $('#content').val('');
        };

    </script>
</body>
</html>