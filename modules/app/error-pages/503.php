<?php
// [CUSTOM 503 Status Code Page]
// v.3.1.1 r.2017.04.10 / smart.framework.v.3.1

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

/**
 * Function: Custom 503 Answer (can be customized on your needs ...)
 *
 * @access 		private
 * @internal
 *
 */
function custom_http_message_503_serviceunavailable($y_message, $y_extra_message='') {
	//--
	return SmartComponents::http_error_message('*Custom* 503 Service Unavailable', $y_message, $y_extra_message);
	//--
} //END FUNCTION

// end of php code
?>