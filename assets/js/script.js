chat_user = 'null';
count = 0;

function login(field) {
  var data = field + '=' + document.getElementById(field).value;
  var user = document.getElementById(field).value;
  if (user == '') return;
  chat_user = user;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     if (this.responseText == "OK") {
       load_chat();
     } else {
       alert(this.responseText);
     }
    }
  };
  xhttp.open("POST", "./user", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send(data);
}

function reset_users() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      alert(this.responseText);
    }
  };
  xhttp.open("GET", "./reset_users", true);
  xhttp.send();
}

function reset_messages() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      alert(this.responseText);
    }
  };
  xhttp.open("GET", "./reset_messages", true);
  xhttp.send();
}

function load_chat() {
  var field = 'container';
  var divdest = document.getElementById(field);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      divdest.innerHTML = this.responseText;
      document.getElementById('chat_user').innerHTML = chat_user;
      get_users();
      get_messages();
      setInterval(get_messages, 3000);
      setInterval(get_users, 5000);
    }
  };
  xhttp.open("GET", "chat.html", true);
  xhttp.send();
}

function get_users() {
  var field = 'online_users';
  var divdest = document.getElementById(field);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      users_json = JSON.parse(this.responseText);
      var users = '';
      for(var k in users_json) {
        users += users_json[k] + ' | ';
      }
      divdest.innerHTML = "Usuário conectados: " + users;
      console.log('Carregando usuários conectados...');
    }
  };
  xhttp.open("GET", "./users", true);
  xhttp.send();
}

function get_messages(field) {
  var field = 'messages';
  var divdest = document.getElementById(field);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      messages_json = JSON.parse(this.responseText);
      countx = messages_json.length;
      if (countx != count) { count = countx; setTimeout(scrolldown, 100); }
      var messages = '';
      for(var k in messages_json) {
        item = messages_json[k];
        if (item.user == chat_user) {
          messages += '<div class="content right"><span class="user"><span class="date">['+item.datetime+']</span> Você diz:</span><span class="message">'+item.textmsg+'</span></div>';
        } else {
          messages += '<div class="content left"><span class="user"><span class="date">['+item.datetime+']</span> '+item.user+' diz:</span><span class="message">'+item.textmsg+'</span></div>';
        }
      }
      divdest.innerHTML = messages;
      console.log('Carregando mensagens...');
    }
  };
  xhttp.open("GET", "./messages?nickname="+chat_user, true);
  xhttp.send();
}

function send_message(field) {
  var data = field + '=' + document.getElementById(field).value;
  data += '&nickname' + '=' + chat_user;
  var message = document.getElementById(field).value;
  document.getElementById(field).value = '';
  if (message == '') return;
  var xhttp = new XMLHttpRequest();
  console.log('Enviando mensagem...' + data);
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     if (this.responseText == "OK") {
       console.log('Mensagem enviada.');
       get_messages();
       setTimeout(scrolldown, 100);
     } else {
       alert(this.responseText);
     }
    }
  };
  xhttp.open("POST", "./send", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send(data);
}

function scrolldown() {
  window.scrollTo(0,document.body.scrollHeight+30);
}
