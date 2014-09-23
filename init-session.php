<?php	
	
	// set headers
		header( 'Access-Control-Allow-Origin: *' );

	// no signature no token
		if ( !isset($_POST['signature']) ){ exit('cool.story.bro'); }

	// required vars
		$user_signature = $_POST['signature'];		
		$server_signature = getenv('PRICINGAPP_SIGNATURE');

	// memcached server connection config
		ini_set('session.save_handler=memcached');
		ini_set('session.save_path', 'PERSISTENT=myapp_session ' . $_ENV['MEMCACHEDCLOUD_SERVERS']);
		ini_set('memcached.sess_binary', 1);
		ini_set('memcached.sess_sasl_username', $_ENV['MEMCACHEDCLOUD_USERNAME']);
		ini_set('memcached.sess_sasl_password', $_ENV['MEMCACHEDCLOUD_PASSWORD']);
		
	// prep session
		session_start();

		$token_metadata = array();
		$token_metadata['id'] = session_id();
		$token_metadata['time_issued'] = time();

	// set session type
		$session_metadata = array();
		$session_metadata['type'] = 'guest';

	// store session in memcached
		$mc = new Memcached();
		$mc->set( $token_metadata['id'], json_encode($session_metadata) );	

	// create session token
		$_jwt = new JWT;
		$session_token = $_jwt->encode( $token_metadata, $server_signature . $user_signature );

	echo $session_token;
?>