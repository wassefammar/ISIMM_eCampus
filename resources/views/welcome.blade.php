<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        
        <style>
            .chat-row {
                margin: 50px;
            }
             ul {
                 margin: 0;
                 padding: 0;
                 list-style: none;
             }
             ul li {
                 padding:8px;
                 background: #928787;
                 margin-bottom:20px;
             }
             ul li:nth-child(2n-2) {
                background: #c3c5c5;
             }
             .chat-input {
                 border: 1px soild lightgray;
                 border-top-right-radius: 10px;
                 border-top-left-radius: 10px;
                 padding: 8px 10px;
                 color:#fff;
             }
        </style>

    </head>
    <body>

        <div class="container">
            <div class="row chat-row">
                <div class="chat-content">
                    <ul>
                      
                    </ul>
                </div>

                <div class="chat-section">
                    <div class="chat-box">
                        <div class="chat-input bg-primary" id="chatInput" contenteditable="">

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script src="https://cdn.socket.io/3.1.3/socket.io.min.js" integrity="sha384-cPwlPLvBTa3sKAgddT6krw0cJat7egBga3DJepJyrLl4Q9/5WLra3rrnMcyTyOnh" crossorigin="anonymous"></script>

        <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
        <script>
            $(function(){
                $ip_address='127.0.0.1';
                $socket_port='3000';
                $socket=io($ip_address+':'+$socket_port);

                let chatInput = $('#chatInput');
                chatInput.keypress(function(e) {
                    let kkk = '{ "content":"John" , "sender":"5" }';
                    //kkk.chat_id=$(this).html();
                    let message=JSON.parse(kkk);
                    console.log(message);
                    if(e.which === 13 && !e.shiftKey) {
                        $socket.emit('message', message);
                        chatInput.html('');
                        return false;
                    }
                });
                $socket.on('message', (message) => {
                    $('.chat-content ul').append(`<li>${message}</li>`);
                });
            })
        </script>
    
    </body>
</html>
