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
  database: 'hammadi'
});

// channel
const channelName = "anonymous_group";
let messages = []; // les messages seront stockés ici


// Écoute de l'événement 'connection' de Socket.io
io.on('connection', (socket) => {
      socket.join(channelName);
      console.log('Backend Connected');
      console.log(socket.id);
      console.log('Un utilisateur sest connecté avec lID ${socket.id}');

        // envoi des messages existants lorsqu'un nouvel utilisateur se connecte
        socket.emit("loadMessages", messages);

      // Écoute de l'événement 'message' de Socket.io
      socket.on('sendMsg', (message) => {
            console.log('Nouveau message : ' + message.content+' '+message.sender);
            // Insertion du message dans la table de la base de données
            axios({
              method: 'post',
              url: 'ISIMM_eCampus/public/api/contacter_admin',
              headers: {
                'Authorization': 'Bearer '+ message.access_token
              },
              data:{
                "text":message.content,
                "chat_id":message.chat_id
              }
        });  

        messages.push(message.content);
        io.to(channelName).emit('SendMsgServer', {...message.content, type: "otherMsg"}); // utilisez le même type que celui du message reçu
      });
});

// Démarrage du serveur Socket.io
io.listen(5000);