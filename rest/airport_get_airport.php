<?php

function _airport_get_airport ($link, $resourceElems, $qpElems, $debugState) {
require_once 'dbConfig.php';
require_once 'db_utils.php';
	// initialize the response buffer
	// $resourceElems[0] is the AIRPORT ID to look up and return
	$response = '';
	// initialize the debug values
	if ($debugState) {
		$response['debug']['module'] = __FILE__;
		$response['debug']['resourceElems'] = $resourceElems;
		$response['debug']['link'] = $link;
	}
	if (!is_null($link)) {
		if (empty($response['error'])) {
			// validate the request buffer fields
			if (!empty($resourceElems[0])) {
				$localErr = '';
				$airportId = $resourceElems[0];
				
				// if there was an error, return it, otherwise add the record
				if (!empty($localErr)) {
					if (empty($response['error'])) {
						$errData['message'] = get_error_message ($link, 400);
						$errData['requestError'] = $localErr;
						$response['error'] = $errData;
					}
				} 
				else 
				{
					// read conifguration for this study and condition
					$queryString = 'SELECT * FROM '.DB_TABLE_AIRPORTS.
						' WHERE ident = "'.$airportId.'"';
					$result = @mysqli_query ($link, $queryString);
					if ($result) {
						if (mysqli_num_rows($result)  > 0) {
							// take only the first record
							$thisRecord = mysqli_fetch_assoc($result);
							$response['data'] = array_merge($thisRecord);
							foreach ($response['data'] as $k => $v) {
								// set "null" strings to null values
								if ($v == 'NULL') {
									$response['data'][$k] = NULL;
								}
							}
							$response['code'] = 200;
						}						
						else 
						{
							$localErr = '';
							$localErr['info'] = 'No airport records found for the specified ID';
							$localErr['message'] = get_error_message ($link, 404);
							$response['error'] = $localErr;
							$response['code'] = 404;
						}
					} 
					else 
					{
						$localErr = '';
						$localErr['info'] = 'No airport records found for the specified ID';
						$localErr['message'] = get_error_message ($link, 404);
						$response['error'] = $localErr;
						$response['code'] = 404;
					}
					if ($debugState) {
						// write detailed sql info
						$localErr = '';
						$localErr['sqlQuery'] = $queryString;
						$localErr['sqlError'] =  mysqli_sqlstate($link);
						$localErr['message'] = mysqli_error($link);				
						$response['debug']['sqlSelect1']= $localErr;
					}
				}
			} else {
				$errData['message'] = get_error_message ($link, 400);
				$errData['info'] = 'No data in request.';
				$response['error'] = $errData;
				$response['code'] = 400;
			}
		}
	} else {
	// not implemented
		$errData['code'] = 500;
		$errData['message'] = 'DB Error on the server.';
		$response['error'] = $errData;	
		$response['code'] = 500;
	}			
	return $response;
}
?>