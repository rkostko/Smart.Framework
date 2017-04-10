<?php
// Controller: Samples/BenchMarkWithSession
// Route: ?/page/samples.benchmark-with-session (?page=samples.benchmark-with-session)
// Author: unix-world.org
// v.3.1.1 r.2017.04.10 / smart.framework.v.3.1

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_APP_MODULE_AREA', 'ADMIN'); 						// INDEX, ADMIN, SHARED
define('SMART_APP_MODULE_AUTH', true); 							// if set to TRUE requires auth always
define('SMART_APP_MODULE_REALM_AUTH', 'SMART-FRAMEWORK.TEST'); 	// if set will check the login realm

/**
 * Admin Controller
 *
 * @ignore
 *
 */
class SmartAppAdminController extends SmartAbstractAppController {

	public function Run() {

		//-- Session will be started also by set
		$sess_key = 'Samples_Benchmark_WithSession (just-for-admin)';
		$sess_test = (string) SmartSession::get($sess_key);
		if((string)$sess_test == '') {
			SmartSession::set($sess_key, date('Y-m-d H:i:s'));
			$sess_test = (string) SmartSession::get($sess_key);
		} //end if
		//--

		//--
		$this->PageViewSetCfg('template-path', '@'); // set template path to this module
		$this->PageViewSetCfg('template-file', 'template-benchmark.htm'); // the default template
		//--

		//--
		$this->PageViewSetVar(
			'title',
			'Benchmark with Session Test URL'
		);
		//--
		$this->PageViewSetVar(
			'main',
			SmartMarkersTemplating::render_file_template(
				$this->ControllerGetParam('module-path').'views/benchmark.htm',
				[
					'BENCHMARK-TITLE' => '[ Benchmark Test URL with PHP Session ]<br>use this URL to run a benchmark of this PHP framework with the PHP Session started ...<br>(Session Value = \''.Smart::escape_html($sess_test).'\')'
				]
			)
		);
		//--

	} //END FUNCTION

} //END CLASS

//end of php code
?>