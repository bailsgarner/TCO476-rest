<?php 
require_once 'airport.php';

function getUrlElems ($requestElemArray) {
	// breaks URL elements found in request to individual strings
	$returnValue = explode ("/", $requestElemArray);
	// the first element is probably blank because of the leading
	//  slash in the request so remove it, if it is.
	if (empty($returnValue[0])) {
		unset ($returnValue[0]);
	}
	// return via array_values to set the first index to 0
	return array_values($returnValue);
}

function getQpElems ($qpElemArray) {
	$returnValue = [];
	$qpElems = explode ("&", $qpElemArray);
	if (!empty($qpElems)){
		// parse query parameters
		foreach ($qpElems as $qpVal) {
			$qpValElems = explode("=", $qpVal);
			if (!empty($qpValElems)) {
				if (count($qpValElems)==2) {
					// this is a key=value string so 
					//   assign value to arrary indexed by key
					$returnValue[$qpValElems[0]] = $qpValElems[1];
				} else if (count($qpValElems)==1) {
					// this is a key-only string so 
					//   assign "true" to arrary indexed by key
					$returnValue[$qpValElems[0]] = "true";
				} // else unreadable QP
			}
		}
	}
	// check for default query parameters
	if (!isset($returnValue["debug"])) {
		//  pick only one of the following lines
		// $returnValue["debug"] = "true";
		$returnValue["debug"] = "false";
	}
	return $returnValue;
}

// main method starts here

$apiResponse = [];
$debugState = true; // by default, unless modified later by a query parameter

$request = $_SERVER['REQUEST_URI']; 

$apiResponse['file'] = __FILE__;
$apiResponse['request'] = $request;
$apiResponse['code'] = 200;

$requestElems = explode ("?",$request);
$apiResponse['requestElems'] = $requestElems;
if (isset($requestElems[0])) {
	$urlElems = getUrlElems($requestElems[0]);
	$apiResponse['urlElems'] = $urlElems;
	if (!empty($urlElems)) {
		if (isset($urlElems[0])) {
			if ($urlElems[0] == "api") {
				// this is a correctly formatted URL
				if (isset($urlElems[1])) {
					// process the query parameters
					$qpElems = getQpElems ($requestElems[1]);
					// set the debug flag 
					// 	the [debug] index should always be present
					$debugState = (boolean)$qpElems["debug"];
					// collect any remaining URL elements
					$resourceElems = $urlElems;
					unset ($resourceElems[0]);
					unset ($resourceElems[1]);
					$resourceElems = array_values($resourceElems);
					// save these values before processing request
					$apiResponse['resourceElems'] = $resourceElems;
					$apiResponse['qpElems'] = $qpElems;
					// select the method for the resource
					switch ($urlElems[1]) {
						case 'airport':
							$response = _doAirport($resourceElems, $qpElems, $debugState);
							break;

						default:
							$response['code'] = 400;
							$response['error']['message'] = 'Unsupported resource resource type.';		
							break;
					}
				}		
			} else {
				// the first url element is not "api" 
				$response['code'] = 400;
				$response['error']['message'] = 'Unsupported resource specification.';		
			}
		} else {
			// the URL was not parsed correctly
			$response['code'] = 400;
			$response['error']['message'] = 'Unsupported resource specification.';		
		}
	} else {
		$response['code'] = 400;
		$response['error']['message'] = 'Unsupported resource specification.';		
	}
} 
else {
	$response['code'] = 400;
	$response['error']['message'] = 'Unsupported resource specification.';
}

// save debug info, if desired
if ($debugState) {
	$response['api'] = $apiResponse;
}

require 'format_response.php';
print $fnResponse;
?>