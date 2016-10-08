<?php 
require_once 'dbConfig.php';

$response = '';

function _doAirline($resourceElems, $qpElems, $debugState)
{
	$link = @mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE_NAME);
	if (!$link) {
		require 'response_500_db_open_error.php';
	} else {
		// if not HTTPS or running on a local host, return an error
		// if (!empty($_SERVER['HTTPS']) || ($_SERVER['HTTP_HOST'] == 'localhost'))
		if (true) 
		{
			if ($_SERVER['REQUEST_METHOD'] == 'GET') {
				// get the request data
				// if the data is not in the the post form, try the query string
				// method not supported, for now
				$errData['code'] = 405;
				$errData['message'] = 'Method not supported';
				$response['error'] = $errData;
				$response['code'] = $errData['code'];
				if ($debugState) {
					$response['debug']['httpMethod'] = $_SERVER['REQUEST_METHOD'];
					$response['debug']['resourceElems'] = $resourceElems;
					$response['debug']['qpElems'] = $qpElems;
					$postData = mb_convert_encoding (file_get_contents("php://input"), "UTF-8", "auto");
					$response['debug']['postData'] = $postData;
					$response['debug']['data'] = json_decode($postData);

					$response['debug']['module'] = __FILE__;
				}
				// when supported, delete the above and fill ine the call
				// $response = _airline_get($link, $resourceElems, $qpElems, $debugState);
			} else {
				// method not supported
				$errData['code'] = 405;
				$errData['message'] = 'Method not supported';
				$response['error'] = $errData;
				$response['code'] = $errData['code'];
				if ($debugState) {
					$response['debug']['httpMethod'] = $_SERVER['REQUEST_METHOD'];
					$response['debug']['resourceElems'] = $resourceElems;
					$response['debug']['qpElems'] = $qpElems;
					$postData = mb_convert_encoding (file_get_contents("php://input"), "UTF-8", "auto");
					$response['debug']['postData'] = $postData;
					$response['debug']['data'] = json_decode($postData);

					$response['debug']['module'] = __FILE__;
				}				
			}
		} 
		else 
		{
				// method not supported
				$errData['code'] = 400;
				$errData['message'] = 'Only HTTPS calls are supported';
				$response['error'] = $errData;
				$response['code'] = $errData['code'];
				if ($debugState) {
					$response['debug']['module'] = __FILE__;
					$response['debug']['globals'] = $GLOBALS;
				}
		}
		mysqli_close($link);	
	}
	return $response;
}
?>