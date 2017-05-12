<?php
// [@[#[!SF.DEV-ONLY!]#]@]
// Controller: Samples/DirectOutput
// Route: ?/page/samples.direct-output (?page=direct-output)
// Author: unix-world.org
// v.3.5.1 r.2017.05.12 / smart.framework.v.3.5

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_APP_MODULE_AREA', 'SHARED'); // INDEX, ADMIN, SHARED
define('SMART_APP_MODULE_DIRECT_OUTPUT', true);

// NOTICE: For this type of controllers you must echo everything and build manually the output from the scratch ...
// When the SMART_APP_MODULE_DIRECT_OUTPUT is set to true:
//		* the Middleware will end bypass the output directly to the controller
//		* all the page settings will be ignored, no headers, no templates no other features will be available
//		* the use for this type of controllers is when you need by example use passthru() from PHP or other functions that need gradually output !
//		* calling InstantFlush() after each portion of output will do a gradually output (see the running example !!)

/**
 * Index Controller
 *
 * @ignore
 *
 */
class SmartAppIndexController extends SmartAbstractAppController {

	public function Run() {

		//-- dissalow run this sample if not test mode enabled
		if(SMART_FRAMEWORK_TEST_MODE !== true) {
			if(!headers_sent()) {
				http_response_code(500);
			} //end if
			die(SmartComponents::http_message_500_internalerror('ERROR: Test mode is disabled ...'));
			return;
		} //end if
		//--

		//--
		header('Cache-Control: no-cache'); // HTTP 1.1
		header('Pragma: no-cache'); // HTTP 1.0
		header('Expires: '.gmdate('D, d M Y', @strtotime('-1 year')).' 09:05:00 GMT'); // HTTP 1.0
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		//--
		$this->InstantFlush();
		//--

		//--
		for($i=0; $i<5; $i++) {
			echo '#'.$i.'<br>';
			$this->InstantFlush();
			sleep(1);
		} //end for
		//--

		//--
		echo '[DONE]';
		//--

	} //END FUNCTION

} //END CLASS

/**
 * Admin Controller (optional)
 *
 * @ignore
 *
 */
class SmartAppAdminController extends SmartAppIndexController {

	// this will clone the SmartAppIndexController to run exactly the same action in admin.php
	// or this can implement a completely different controller if it is accessed via admin.php

} //END CLASS

//end of php code
?>