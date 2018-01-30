<?php
// Controller: Cloud/Files
// Route: admin.php/page/cloud.files/~/
// Author: unix-world.org
// v.180130

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_APP_MODULE_AREA', 'ADMIN'); // admin area only
define('SMART_APP_MODULE_AUTH', true); // requires auth always
define('SMART_APP_MODULE_DIRECT_OUTPUT', true); // do direct output

/**
 * Admin Controller (direct output)
 */
class SmartAppAdminController extends \SmartModExtLib\Webdav\ControllerAdmDavFs {

	public function Run() {

		//-- dissalow run this sample if not test mode enabled
		if(!defined('SMART_FRAMEWORK_TEST_MODE') OR (SMART_FRAMEWORK_TEST_MODE !== true)) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('ERROR: Test mode is disabled ...');
			return;
		} //end if
		//--
		if(!defined('SMART_FRAMEWORK_TESTUNIT_ALLOW_DAVFS_TESTS') OR (SMART_FRAMEWORK_TESTUNIT_ALLOW_DAVFS_TESTS !== true)) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('ERROR: WebDAV Test mode is disabled ...');
			return;
		} //end if
		//--

		//--
		if(!defined('SMART_SOFTWARE_URL_ALLOW_PATHINFO') OR ((int)SMART_SOFTWARE_URL_ALLOW_PATHINFO < 1)) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('ERROR: WebDAV requires PathInfo to be enabled into init.php for Admin Area ...');
			return;
		} //end if
		//--
		$this->DavFsRunServer(
			'wpub/webapps-content/test-webdav',
			true // you may disable this on large webdav file systems to avoid huge calculations
		);
		//--

	} //END FUNCTION

} //END CLASS

//end of php code
?>