<?php	
	
	// set headers
		header( 'Access-Control-Allow-Origin: *' );

	// load JWT class
	//	require('vendor/bin/firebase/php-jwt/Authentication/JWT.php'); 

	// no signature no token
		if ( !isset($_POST['signature']) ){ echo ( __FILE__ );  exit('cool.story.bro'); }
		
	// required vars
		$user_signature = $_POST['signature'];		
		$server_signature = getenv('PRICINGAPP_SIGNATURE');

	// cookieless sessions 
		ini_set('session.use_cookies', 0);

	// memcached server connection config
		ini_set('session.save_handler=memcached');
		ini_set('session.save_path', 'PERSISTENT=myapp_session ' . getenv('MEMCACHEDCLOUD_SERVERS') );
		ini_set('memcached.sess_binary', 1);
		ini_set('memcached.sess_sasl_username', getenv('MEMCACHEDCLOUD_USERNAME') );
		ini_set('memcached.sess_sasl_password', getenv('MEMCACHEDCLOUD_PASSWORD') );
		
	// prep session
		session_start();

		$_SESSION['start_time'] = time();
		$_SESSION['type'] = 'guest';
		
	// create session token
		$this_session = (array) $_SESSION;
		$session_token = JWT::encode( $this_session, $server_signature . $user_signature );

	echo $session_token;
?>