<?php
// Module Lib: \SmartModExtLib\Webdav\ControllerAdmCardDavFs

namespace SmartModExtLib\Webdav;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


abstract class ControllerAdmCardDavFs extends \SmartAbstractAppController {

	// v.180209

	private $dav_author = 'unknown';
	private $dav_uri = '';
	private $dav_url = '';
	private $dav_method = '';
	private $dav_request_path = '';
	private $dav_request_back_path = '';
	private $dav_vfs_path = '';
	private $dav_vfs_root = 'none';
	private $dav_is_root_path = true;


	public function DavFsRunServer($dav_fs_root_path, $show_usage_quota=false, $nfo_title='DAV@webAbook', $nfo_signature='Smart.Framework::CardDAV', $nfo_prefix_crrpath='DAV:', $nfo_lnk_welcome='', $nfo_txt_welcome='CardDAV :: Home', $nfo_svg_logo='modules/mod-webdav/libs/img/abook.svg') {

		//-- set nocache headers
		header('Cache-Control: no-cache'); // HTTP 1.1
		header('Pragma: no-cache'); // HTTP 1.0
		//--

		//--
		if(defined('SMART_WEBDAV_SHOW_USAGE_QUOTA')) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: The constant SMART_WEBDAV_SHOW_USAGE_QUOTA must NOT be defined outside DavRunServer !');
			return;
		} //end if
		//--
		define('SMART_WEBDAV_SHOW_USAGE_QUOTA', (bool)$show_usage_quota);
		//--

		//--
		if(!defined('SMART_APP_MODULE_AREA') OR (strtoupper((string)SMART_APP_MODULE_AREA) !== 'ADMIN')) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: Requires an Admin Module Area controller to run !');
			return;
		} //end if
		//--

		//--
		if(!defined('SMART_APP_MODULE_DIRECT_OUTPUT') OR (SMART_APP_MODULE_DIRECT_OUTPUT !== true)) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: Requires Direct Output set to True in the controller !');
			return;
		} //end if
		//--

		//-- check auth
		if(!defined('SMART_APP_MODULE_AUTH') OR (SMART_APP_MODULE_AUTH !== true)) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: Requires Module Auth set to True in the controller !');
			return;
		} //end if
		//--
		if(\SmartAuth::check_login() !== true) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: Authentication required but not detected !');
			return;
		} //end if
		//--
		$this->dav_author = (string) \SmartAuth::get_login_id();
		//--

		//--
		$dav_fs_root_path = (string) trim((string)$dav_fs_root_path);
		if((string)$dav_fs_root_path == '') {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: DAV FS Root Path is Empty !');
			return;
		} //end if
		//--
		$dav_fs_root_path = (string) \SmartFileSysUtils::add_dir_last_slash((string)\SmartModExtLib\Webdav\DavServer::safePathName((string)$dav_fs_root_path));
		if(\SmartFileSysUtils::check_if_safe_path((string)$dav_fs_root_path) != '1') {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: DAV FS Root Path is Invalid: '.$dav_fs_root_path);
			return;
		} //end if
		if(\SmartFileSystem::path_exists((string)$dav_fs_root_path) !== true) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: DAV FS Root Path does Not Exists: '.$dav_fs_root_path);
			return;
		} //end if
		//--

		//-- calculate base uri
		$this->dav_request_path = (string) ltrim((string)$this->RequestPathGet(), '/');
		$this->dav_request_path = (string) \SmartUnicode::deaccent_str($this->dav_request_path);
		$this->dav_request_path = (string) \SmartModExtLib\Webdav\DavServer::safePathName($this->dav_request_path);
		if((string)$this->dav_request_path == '') {
			$this->dav_is_root_path = true;
			$this->dav_request_back_path = '';
		} else {
			$this->dav_is_root_path = false;
			$this->dav_request_back_path = (string) trim((string)\Smart::dir_name((string)$this->dav_request_path));
			if((string)$this->dav_request_back_path == '.') {
				$this->dav_request_back_path = '';
			} //end if
			if((string)$this->dav_request_back_path != '') {
				if(\SmartFileSysUtils::check_if_safe_path($this->dav_request_back_path) != '1') {
					$this->dav_request_back_path = '';
				} //end if
			} //end if
		} //end if
		//--
		$this->dav_uri = (string) \SmartUtils::get_server_current_full_script().\SmartUtils::get_server_current_request_path();
		$this->dav_url = (string) \SmartUtils::get_server_current_url().\SmartUtils::get_server_current_script().\SmartUtils::get_server_current_request_path();
		$this->dav_method = (string) strtoupper((string)$_SERVER['REQUEST_METHOD']);
		$this->dav_vfs_root = (string) $dav_fs_root_path;
		$this->dav_vfs_path = (string) \SmartModExtLib\Webdav\DavServer::safePathName(rtrim((string)$this->dav_vfs_root.$this->dav_request_path, '/'));
		//--

		//--
		if(defined('SMART_WEBDAV_CARDDAV_ACC_PATH')) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: The constant SMART_WEBDAV_CARDDAV_ACC_PATH must NOT be defined outside DavRunServer !');
			return;
		} //end if
		define('SMART_WEBDAV_CARDDAV_ACC_PATH', $this->dav_vfs_root.'principals/'); // proxys path
		//--
		if(defined('SMART_WEBDAV_CARDDAV_ABOOK_PATH')) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: The constant SMART_WEBDAV_CARDDAV_ABOOK_PATH must NOT be defined outside DavRunServer !');
			return;
		} //end if
		define('SMART_WEBDAV_CARDDAV_ABOOK_PATH', $this->dav_vfs_root.'addressbooks/'.\Smart::safe_username($this->dav_author).'/');
		//--
		if(defined('SMART_WEBDAV_CARDDAV_ABOOK_HOME')) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: The constant SMART_WEBDAV_CARDDAV_ABOOK_HOME must NOT be defined outside DavRunServer !');
			return;
		} //end if
		define('SMART_WEBDAV_CARDDAV_ABOOK_HOME', (string)\SmartUtils::get_server_current_full_script().'/page/'.$this->ControllerGetParam('url-page').'/~/addressbooks/'.\Smart::safe_username($this->dav_author).'/');
		//--
		if(defined('SMART_WEBDAV_CARDDAV_ABOOK_PPS')) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: The constant SMART_WEBDAV_CARDDAV_ABOOK_PPS must NOT be defined outside DavRunServer !');
			return;
		} //end if
		define('SMART_WEBDAV_CARDDAV_ABOOK_PPS', (string)\SmartUtils::get_server_current_full_script().'/page/'.$this->ControllerGetParam('url-page').'/~/principals/');
		//--
		if(defined('SMART_WEBDAV_CARDDAV_ABOOK_ACC')) {
			http_response_code(500);
			echo \SmartComponents::http_message_500_internalerror('FATAL ERROR @ CardDAV: The constant SMART_WEBDAV_CARDDAV_ABOOK_ACC must NOT be defined outside DavRunServer !');
			return;
		} //end if
		define('SMART_WEBDAV_CARDDAV_ABOOK_ACC', (string)\SmartUtils::get_server_current_full_script().'/page/'.$this->ControllerGetParam('url-page').'/~/principals/'.\Smart::safe_username($this->dav_author).'/');
		//--

		//--
		// \Smart::log_notice($this->dav_method.': '.$this->dav_request_path.' @ '.$this->dav_vfs_path);
		//--
		switch((string)$this->dav_method) {

			case 'OPTIONS':
				\SmartModExtLib\Webdav\DavFsCardDav::methodOptions();
				break;

			case 'HEAD':
				\SmartModExtLib\Webdav\DavFsCardDav::methodHead((string)$this->dav_vfs_path);
				break;

			case 'PROPFIND':
				\SmartModExtLib\Webdav\DavFsCardDav::methodPropfind(
					(string) $this->dav_uri,
					(string) $this->dav_request_path,
					(string) $this->dav_vfs_path,
					(bool)   $this->dav_is_root_path,
					(string) $this->dav_vfs_root
				);
				break;

			case 'REPORT':
				\SmartModExtLib\Webdav\DavFsCardDav::methodReport(
					(string) $this->dav_uri,
					(string) $this->dav_request_path,
					(string) $this->dav_vfs_path,
					(bool)   $this->dav_is_root_path,
					(string) $this->dav_vfs_root
				);
				break;

			case 'PUT':
				\SmartModExtLib\Webdav\DavFsCardDav::methodPut((string)$this->dav_vfs_path);
				break;

			case 'DELETE':
				\SmartModExtLib\Webdav\DavFsCardDav::methodDelete((string)$this->dav_vfs_path);
				break;

			case 'GET':
				\SmartModExtLib\Webdav\DavFsCardDav::methodGet(
					(string) $this->dav_method,
					(string) $this->dav_author,
					(string) $this->dav_url,
					(string) $this->dav_request_path,
					(string) $this->dav_vfs_path,
					(bool)   $this->dav_is_root_path,
					(string) $this->dav_vfs_root,
					(string) $this->dav_request_back_path,
					(string) $nfo_title,
					(string) $nfo_signature,
					(string) $nfo_prefix_crrpath,
					(string) $nfo_lnk_welcome,
					(string) $nfo_txt_welcome,
					(string) $nfo_svg_logo
				);
				break;

			default:
				http_response_code(501); // not implemented
				// \Smart::log_notice('Method NOT Implemented: '.(string)$this->dav_method);

		} //end switch

	} //END FUNCTION


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


//end of php code
?>