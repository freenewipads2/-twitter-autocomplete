<?php
session_start();

function generateToken()
	{
	if (isset($_SESSION['token']))
		{
		/*
		Error handling
		*/
		echo "Token already generated";
    return;
		}

	$public_key = '';
	$secret_key = '';
	$api_base = 'https://api.twitter.com/';
	/*
	Base64 encode key
	*/
	$base_key = base64_encode($public_key . ':' . $secret_key);
	/*
	Token settings
	*/
	$opts = array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Authorization: Basic ' . $base_key . "\r\n" . 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
			'content' => 'grant_type=client_credentials'
		)
	);
	$context = stream_context_create($opts);
	$json = file_get_contents($api_base . 'oauth2/token', false, $context);
	$result = json_decode($json, true);
	/*
	Error handling
	*/
	$success = true;
	if (!is_array($result) || !isset($result['token_type']) || !isset($result['access_token']))
		{
		echo ("No valid array: " . $json);
		$success = false;
		}

	if ($result['token_type'] !== "bearer")
		{
		echo ("Invalid token type.");
		$success = false;
		}

	/*
	User token
	*/
	if ($success)
		{
		$_SESSION['token'] = $result['access_token'];
		}

	return;
	}

function requestData($value)
	{
	if (!isset($_SESSION['token']))
		{
		generateToken();
		}

	$bearer_token = $_SESSION['token'];
	$api_base = 'https://api.twitter.com/';
	/*
	Search settings
	*/
	$opts = array(
		'http' => array(
			'method' => 'GET',
			'header' => 'Authorization: Bearer ' . $bearer_token
		)
	);
	$context = stream_context_create($opts);
	$response = file_get_contents($api_base . '1.1/search/tweets.json?q=' . urlencode ("#" . $value), false, $context);
	return ($response);
	}

function generateReturnData($data)
	{
	$data = json_decode($data, true);
	$data = $data['statuses'];
	$returnArray = array();
	foreach($data as $key => $obj)
		{
		/*
		Add the data you will use
		*/
		$returnArray[$key]['id'] = $obj['id'];
		$returnArray[$key]['text'] = $obj['text'];
		$returnArray[$key]['user'] = $obj['user'];
		}

	return (json_encode($returnArray));
	}

/*
Instead of escapestring
*/
function mres($value)
	{
	$search = array(
		"\\",
		"\x00",
		"\n",
		"\r",
		"'",
		'"',
		"\x1a"
	);
	$replace = array(
		"\\\\",
		"\\0",
		"\\n",
		"\\r",
		"\'",
		'\"',
		"\\Z"
	);
	return str_replace($search, $replace, $value);
	}

?>
