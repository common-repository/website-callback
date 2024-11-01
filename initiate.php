<?php

// load WP and get options
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
$options = get_option('callback_options'); 
if(!$options){
	header('HTTP/1.1 203 Maybe- try again');
	print 'Not Configured!';
	die();
}

// sanitise
$to = @$_REQUEST['number'];
$to = preg_replace('/[^0-9\s]/', '', $to);

// feedback
$error='';
if(!$to) $error='No number given'; 
elseif(strlen($to)<8) $error='Number not long enough';
if($error){
	header('HTTP/1.1 203 Maybe- try again');
	print $error;
	die();
}

// write out headers to close the connection
ob_start();
print "Calling $to!";

// close connection so we can do the work
$size = ob_get_length();
header("Content-Length: $size");
header("Connection: close\r\n");
ob_end_flush(); flush();
ob_end_clean();

// initiate
$call=new InitiateCall($options['callback_username'], $options['callback_password']);
$call->connect($options['callback_number'], $to);

// write to admin letting them know there's a new call
mail($options['callback_email'],
	'Website Call Initiated', "
		Domain: {$_SERVER['HTTP_HOST']}
		Referer: {$_SERVER['HTTP_REFERER']}
		IP Address: {$_SERVER['REMOTE_ADDR']}
		Phone Number: {$to}
	",
	$headers = "From: Your Website <server@{$_SERVER['HTTP_HOST']}>" . "\r\n" .
		'Reply-To: ' . $options['callback_email'] . "\r\n" .
		'X-Mailer: Acumen Call Handler'
);

class InitiateCall{

        var $host='api.netfuse.org';
        var $port=5500;
        var $socket=null;
        var $received='';
        var $calls=array();

        function InitiateCall($user, $pass){

                $this->socket=socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                if(!socket_connect($this->socket, $this->host, $this->port)){
                        die("Could not connect\n");
                }

                // log in
                $logon="AUTH $user $pass\r\n";
                        sleep(0.2);
                        socket_write($this->socket, $logon, strlen($logon));
                        sleep(0.2);
                        socket_send($this->socket, $logon, strlen($logon), 1);
        }

        function connect($from, $to){
                $command="ORIGINATE {$from} {$to} Website {$to}\r\n";
                socket_write($this->socket, $command, strlen($command));
        }
}
?>
