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

// channel
const channelName = "anonymous_group";
let messages = []; // les messages seront stockés ici

access_token="10|JQG2mPMzvDywkqYHoNFeUEoeYNfdqYqKsl4xGVYs";

// Écoute de l'événement 'connection' de Socket.io
io.on('connection', (socket) => {
  socket.join(channelName);
  console.log('Backend Connected');
  console.log(socket.id);
  console.log(`Un utilisateur s'est connecté avec l'ID ${socket.id}`);

    // envoi des messages existants lorsqu'un nouvel utilisateur se connecte
    socket.emit("loadMessages", messages);

  // Écoute de l'événement 'message' de Socket.io
  socket.on('message', (message) => {
    console.log('Nouveau message : ' + message.content+' '+message.sender);
    // Insertion du message dans la table de la base de données
    axios({
      method: 'post',
      url: 'ISIMM_eCampus/public/api/messages',
      headers: {
        'Authorization': 'Bearer '+ access_token
      },
      data:{
        "text":message.content,
        "chat_id":1
      }
    });  

    messages.push(msg);
    io.to(channelName).emit('SendMsgServer', {...msg, type: "otherMsg"}); // utilisez le même type que celui du message reçu
  });
});

// Démarrage du serveur Socket.io
io.listen(3000); 