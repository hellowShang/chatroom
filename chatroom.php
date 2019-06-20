<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ChatRoom</title>
    <script src="http://client.lab993.com/js/jquery.js"></script>
</head>
<body>
    <h2>聊天室</h2>
    <div></div>
    <input type="text" id="content" style="width: 900px">
    <button id="btn">发送</button>
    <script>

        var web = new WebSocket("ws://chat.com:9504");
        web.onopen=function () {
            $("#btn").click(function(){
                var type =1;
                var content = $("#content").val();
                var name = "<?php echo $_COOKIE['name'];?>";
                var ip = "<?php echo $_SERVER['REMOTE_ADDR'];?>";
                var data = {content:content,name:name,ip:ip,type:type};
                web.send(JSON.stringify(data));
            });
        };

        web.onmessage=function(d){
            var data = JSON.parse(d.data);
                var li = "<li><font>"+data.time+"</font>&nbsp;&nbsp;&nbsp;&nbsp;<font color='blue'>"+data.nickname+"</font>&nbsp;&nbsp;&nbsp;&nbsp;<font color='red'>"+data.content+"</font></li>";
                $('#content').before(li);
                $('#content').val('');
        }
    </script>
</body>
</html>