const express = require('express');
const app = express();
const http = require('http').Server(app);
const io = require('socket.io')(http,{
    cors: {origin:"*"}
});

const axios=require('axios');
const mysql = require('mysql');
const { access } = require('fs');

const connection = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'isimm'
});

const channelName = "anonymous_group";
let messages = [];

io.on('connection', (socket) => {
  socket.join(channelName);

  console.log('Backend Connected');
  console.log(socket.id);
  console.log('A user has connected with ID ${socket.id}');


  socket.on('sendMsg', (message) => {
    console.log('New message: ' + message.msg + ' ' + message.sender);
    // Insertion du message dans la table de la base de données
    axios({
      method: 'post',
      url: 'ISIMM_eCampus/public/api/repondre_enseignant',
      headers: {
        'Authorization': 'Bearer '+ message.access_token
      },
      data:{
        "text":message.msg,
        "chat_id":message.chat_id,
      }
    });  

 
   
    io.to("anonymous_group").emit('sendMsgServer', { ...message, type: "otherMsg" });
  });

  // Ajoutez votre code ici pour exécuter les actions nécessaires lors de la connexion du backend

});

http.listen(3000, () => {
  console.log('Socket.io server listening on port 10000');
})


