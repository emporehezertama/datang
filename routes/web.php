<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return [
    	'Company' => 'PT Empore Heze Tama',
    	'Address' => 'Metropolitan tower, level13-A
					Jl. R.A. Kartini - T.B. Simatupang Kav. 14
					Cilandak, Jakarta Selatan
					Jakarta - 12430'

    ];
});

$router->get('test', function(){
	$img = \Image::make('1.png');
	// resize image
	$img->fit(300, 200);
	// save image
	$img->save('1.jpg');
});

$router->post('login', 'AuthController@verify');
$router->post('send-attendance', 'AttendanceController@send');
// Device Finger
$router->post('finger-store', 'AttendanceController@fingerStore');

$router->get('device-list', function(){

	$key = isset($_GET['key']) ? $_GET['key'] : '';

	if($key == 'iv5Ccmn4m2GVScDxOrkHSnHG1r6pDUBaOwA8jhlFLxM')
	{
		$params['status']		= 'success';
		$params['code']			= 200;
		$params['data'] 		= app('db')->select('SELECT * FROM iclock');
	}
	else
	{
		$params['status'] 	= 'Forbidden';
		$params['code']		= '403';
	}

	return $params;
});


$router->get('attendance-list', function(){

	$key = isset($_GET['key']) ? $_GET['key'] : '';

	if($key == 'iv5Ccmn4m2GVScDxOrkHSnHG1r6pDUBaOwA8jhlFLxM')
	{
		$params['status']		= 'success';
		$params['code']			= 200;
		$params['data'] 		= app('db')->select("SELECT c.*, u.badgenumber, u.Name, u.Password, u.Privilege FROM checkinout c INNER JOIN userinfo u on u.userid=c.userid WHERE c.SN='". $_GET['SN'] ."' ORDER BY c.id ASC");
	}
	else
	{
		$params['status'] 	= 'Forbidden';
		$params['code']		= '403';
	}

	return $params;
});

$router->get('iclock-login', function(){
	$url 			= 'http://localhost/get_login.php';
	
	$html = file_get_contents($url);

	return $html;
});


$router->get('send-iclock', function(){

	$url 			= 'http://localhost/get_login.php';
  	$cookie 	= 'curl-session';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); // Cookie aware
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie); // Cookie aware

	$html = curl_exec($ch);

	if (curl_errno($ch)) die(curl_error($ch));

	# Create a DOM parser object
	$dom = new DOMDocument();

	@$dom->loadHTML($html);

	$csrfmiddlewaretoken = '';
	# Iterate over all the <a> tags
	foreach($dom->getElementsByTagName('input') as $link) 
	{
	    if ($link->getAttribute('name') == 'csrfmiddlewaretoken')
	    {
	        $csrfmiddlewaretoken = $link->getAttribute('value');
	    }
	}

  	$username = env('ATTENDANCE_USERNAME');
  	$password = env('ATTENDANCE_PASSWORD');

	$data = "username=$username&password=$password&csrfmiddlewaretoken=$csrfmiddlewaretoken&this_is_the_login_form=1&post_data=";

	//Set a user agent. This basically tells the server that we are using Chrome ;)
	define('USER_AGENT', 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36');

	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	
	$html = curl_exec($ch);

	if (curl_errno($ch)) 
	{
		print curl_error($ch);
	}

	curl_close($ch);
	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://localhost/iclock/iclock.php');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); // Cookie aware
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie); // Cookie aware
	$content = curl_exec($ch);
	curl_close($ch);

	return $content;

	$ch = curl_init();
    	//We should be logged in by now. Let's attempt to access a password protected page
	curl_setopt($ch, CURLOPT_URL, 'http://localhost/iclock/iclock.php');
	//Use the same cookie file.
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie); 
	//Use the same user agent, just in case it is used by the server for session validation.
	curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
	//We don't want any HTTPS / SSL errors.
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	 
	//Execute the GET request and print out the result.
	$data =  curl_exec($ch);
	dd($data);
  	return;


  	#$ch = curl_init();
  	// set URL and other appropriate options
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
  	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
  	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_REFERER, $url);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
  	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  	// grab URL and pass it to the browser
  	$html = curl_exec($ch);
  	curl_close ($ch);
  	
  	
  	if($html = curl_exec($ch) == false)
		echo 'Curl error: ' . curl_error($ch);
	else
	{
	  	//We should be logged in by now. Let's attempt to access a password protected page
		curl_setopt($ch, CURLOPT_URL, 'http://192.168.0.104:8000/iclock/data/iclock/');
		//Use the same cookie file.
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookiefile); 
		//Use the same user agent, just in case it is used by the server for session validation.
		curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
		//We don't want any HTTPS / SSL errors.
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 
		//Execute the GET request and print out the result.
		$data =  curl_exec($ch);

	  	return $data;
	}
});


$router->get('iclock/cdata', function(){


	error_reporting(E_ALL | E_STRICT);

	$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	socket_bind($socket, '127.0.0.1', 1223);

	$from = '';
	$port = 0;
	socket_recvfrom($socket, $buf, 12, 0, $from, $port);

	$msg = "Received $buf from remote address $from and remote port $port" . PHP_EOL;

	file_put_contents('./log_'.date("j.n.Y").'.log', $msg, FILE_APPEND);
	
	exit;
	return;

	$request = \Illuminate\Http\Request::instance(); // Access the instance
	$data  = $request->getContent(); // Get its content


	//Something to write to txt log
	$log  = "======================================================================================\n";
	$log  .= "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
	        "-------------------------".PHP_EOL;
	$log  .= 'DATA : '. json_encode($data);
	$log  .= PHP_EOL;

	$header = '';
    foreach (getallheaders() as $name => $value) { 
	    $header .=$name .' : '. $value ."\n"; 
	}
	$log .=$header;

	$data = file_get_contents('php://input');

	$log .= 'Data file Input '. json_encode($data);
	$log .= 'GET '. json_encode($_GET);
	$log .= "\n ======================================================================================\n";


	//Save string to log, use FILE_APPEND to append.
	file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);
});