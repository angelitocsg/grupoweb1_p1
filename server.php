<?php
header('Access-Control-Allow-Origin: *');

echo start();

function start() {
  $vars = $_GET;
  $vars = array_merge($vars, $_POST);
  logg($vars);

  $chat = new Chat();

  if (isset($vars['r'])) {
    $route = $vars['r'];
    switch ($route) {
      case 'user':
        $nick = isset($vars['nickname']) ? $vars['nickname'] : '';
        if ($chat->user_exists($nick) || trim($nick) == '' ) return "Usuário inválido!";
        else {
          $chat->user_add($nick);
          $chat->message_add('chatbot', '<b>'.$nick. '</b> entrou na conversa.');
          return "OK";
        }
        break;
      case 'reset_messages':
        $chat->reset_messages();
        return "Mensagens excluídas!";
        break;
      case 'reset_users':
        $chat->reset_users();
        $chat->message_add('chatbot', 'Todos os usuários foram desconectados.');
        return "Usuários desconectados!";
        break;
      case 'messages':
        $nick = isset($vars['nickname']) ? $vars['nickname'] : '';
        if ($chat->user_exists($nick) && trim($nick) != '')
        {
          return json_encode($chat->Messages);
        }
        else {
          return "Usuário desconectado!";
        }
        break;
      case 'users':
        return json_encode($chat->Users);
        break;
      case 'send':
        $nick = isset($vars['nickname']) ? $vars['nickname'] : '';
        $message = isset($vars['textmsg']) ? $vars['textmsg'] : '';
        if (trim($message) != '' && $chat->user_exists($nick) && trim($nick) != '')
          return $chat->message_add($nick, $message);
        else {
            return "Erro enviando mensagem. Usuário desconectado.";
        }
        break;
    }
  }
}


class Message
{
  public $user;
  public $datetime;
  public $textmsg;
}

class Chat
{
  private $file_chat = "messages.json";
  private $file_user = "users.json";

  public $Messages = array();
  public $Users = array();

  function __construct() {
    $this->load_users();
    $this->load_messages();
  }

  public function reset_messages() {
    $this->Messages = array();
    $this->save_messages();
  }
  public function reset_users() {
    $this->Users = array();
    $this->save_users();
  }

  public function user_exists($nick) {
    $exists = false;
    foreach ($this->Users as $user) {
      if ($user == $nick) { $exists = true; break; }
    }
    return $exists;
  }
  public function user_add($nick) {
    $this->Users[] = $nick;
    $this->save_users();
  }
  public function message_add($nick, $textmsg) {
    $message = new Message();
    $message->datetime = date('d/m/Y H:i');
    $message->user = $nick;
    $message->textmsg = $textmsg;
    $this->Messages[] = $message;
    $this->save_messages();
    return 'OK';
  }

  private function load_messages() {
    $content = null;
    $handle = fopen($this->file_chat, "a+");
    if (filesize($this->file_chat) !== false && filesize($this->file_chat) > 0)
    {
      $content = fread($handle, filesize ($this->file_chat));
      fclose($handle);
    }
    if (empty($content)) {
      $message = new Message();
      $message->datetime = date('d/m/Y H:i');
      $message->user = 'chatbot';
      $message->textmsg = "Olá! Bem vindo ao CHAT!";
      $this->Messages[] = $message;
    }
    else {
      $this->Messages = json_decode($content);
    }
  }

  private function save_messages() {
    if (!empty($this->Messages)) {
      $content = json_encode($this->Messages);
      $handle = fopen($this->file_chat, "w");
      fwrite($handle, $content);
      fclose($handle);
    } else {
      $content = json_encode($this->Messages);
      $handle = fopen($this->file_chat, "w");
      fwrite($handle, '');
      fclose($handle);
    }
  }
  private function load_users() {
    $content = null;
    $handle = fopen($this->file_user, "a+");
    if (filesize($this->file_user) !== false && filesize($this->file_user) > 0)
    {
      $content = fread($handle, filesize ($this->file_user));
      fclose($handle);
    }
    if (empty($content)) {
      $user = 'chatbot';
      $this->Users[] = $user;
    }
    else {
      $this->Users = json_decode($content);
    }
  }

  private function save_users() {
    if (!empty($this->Users)) {
      $content = json_encode($this->Users);
      $handle = fopen($this->file_user, "w");
      fwrite($handle, $content);
      fclose($handle);
    } else {
      $content = json_encode($this->Users);
      $handle = fopen($this->file_user, "w");
      fwrite($handle, '');
      fclose($handle);
    }
  }
}

function logg($var) {
  return "<pre>" . print_r($var, true) . "</pre>";
}

?>
