<?php	
	
	// set headers
		header( 'Access-Control-Allow-Origin: *' );

	// no signature no token
		if ( !isset($_POST['signature']) ){ exit('cool.story.bro'); }

	// required vars
		require('vendor/autoload.php'); // jwt token lib
		$user_signature = $_POST['signature'];		
		$server_signature = getenv('PRICINGAPP_SIGNATURE');

	// prep session
		ini_set('session.use_cookies', 0);
		session_start();

		$token_metadata = array();
		$token_metadata['id'] = session_id();
		$token_metadata['time_issued'] = time();

	// memcached server connection config
		$mc = new Memcached();
		$mc->setOption(Memcache::OPT_BINARY_PROTOCOL, true);
		$mc->addServers(array_map(function($server) { return explode(':', $server, 2); }, explode(',', $_ENV['MEMCACHEDCLOUD_SERVERS'])));
		$mc->setSaslAuthData($_ENV['MEMCACHEDCLOUD_USERNAME'], $_ENV['MEMCACHEDCLOUD_PASSWORD']);
		
	// set session type
		$session_metadata = array();
		$session_metadata['type'] = 'guest';

	// store session in memcached
		$mc->set( $token_metadata['id'], json_encode($session_metadata) );	

	// create session token
		$_jwt = new JWT;
		$session_token = $_jwt->encode( $token_metadata, $server_signature . $user_signature );

	echo $session_token;
?>