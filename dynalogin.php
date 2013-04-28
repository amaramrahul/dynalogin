<?php
class dynalogin extends rcube_plugin
{
  // registered tasks for this plugin.
  public $task = 'login|logout';

  // Dynalogin server and port
  private $dynalogin_server;  
  private $dynalogin_port;  

  function init()
  {
    $rcmail = rcmail::get_instance();
    
    // check whether the "global_config" plugin is available,
    // otherwise load the config manually.
    $plugins = $rcmail->config->get('plugins');
    $plugins = array_flip($plugins);
    if (!isset($plugins['global_config'])) {
      $this->load_config();
    }
    
    // load plugin configuration.
    $this->dynalogin_server = $rcmail->config->get('dynalogin_server', 'localhost');
    $this->dynalogin_port = $rcmail->config->get('dynalogin_port', '9050');
    
    // login form modification hook.
    $this->add_hook('template_object_loginform', array($this,'dynalogin_loginform'));

    // register hooks.
    $this->add_hook('authenticate', array($this, 'authenticate'));
  }
  
  function dynalogin_loginform($content)
  {
    // load localizations.
    $this->add_texts('localization', true);
    
    // import javascript client code.
    $this->include_script('dynalogin.js');
    
    return $content;
  }
  
  function authenticate($args)
  {  
    $this->authenticate_args = $args;

    $user = $args['user'];
    $code = get_input_value('_code', RCUBE_INPUT_POST);

    if (!self::dynalogin_auth($user, $code, $this->dynalogin_server, $this->dynalogin_port))
    {
      write_log('errors', 'dynalogin: OTP verfication failed');
      $args['abort'] = true;
    }

    return $args;
  }
  
  function dynalogin_read($sock)
  {
    if(!socket_last_error($sock)) {
      while($line = socket_read($sock, 512, PHP_NORMAL_READ)) {
  #      echo "<p>".$line."</p>";
	if(!preg_match("/^(\d\d\d)([- ])(.*)\$/", $line, $elements)) {
	  write_log("errors", "dynalogin: bad data line");
	  return FALSE;
	}
	$code = $elements[1];
	$last_line = ($elements[2] == " ");
	$msg = $elements[3];
	if($last_line) {
	  return $code;
	}
      }
    }
    return FALSE;
  }

  function dynalogin_auth($user, $code, $server, $port)
  {
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_connect($sock, $server, $port);

    // read greeting
    $greeting_code = self::dynalogin_read($sock);

    if($greeting_code == 220) {
      // send auth request
      $request = "UDATA HOTP $user $code\n";
      socket_write($sock, $request);

      // check response
      $response_code = self::dynalogin_read($sock);
      if($response_code == 250)
	$logged_in = 1;
      else
	$logged_in = 0;
    } else {
      // bad greeting
      write_log("errors", "dynalogin: bad greeting");
    }

    // quit
  //  socket_close($sock);
    return $logged_in;
  }

}
