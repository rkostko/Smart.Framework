<?php
// [LIB - SmartFramework / Samples / Test FileSystem]
// (c) 2006-2018 unix-world.org - all rights reserved
// v.3.7.7 r.2018.10.19 / smart.framework.v.3.7

// Class: \SmartModExtLib\Samples\TestUnitFileSystem
// Type: Module Library
// Info: this class integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup

namespace SmartModExtLib\Samples;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	@http_response_code(500);
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


/**
 * Test FileSystem
 *
 * @access 		private
 * @internal
 *
 * @version 	v.170920
 *
 */
final class TestUnitFileSystem {

	// ::

	//============================================================
	public static function testFs() {

		//--
		if(SMART_FRAMEWORK_TESTUNIT_ALLOW_FS_TESTS !== true) {
			//--
			return (string) \SmartComponents::operation_notice('Test Unit File System Tests are DISABLED ...');
			//--
		} //end if
		//--

		//--
		$time = microtime(true);
		//--

		//--
		$err = '';
		$tests = array();
		//--

		//--
		if((string)DIRECTORY_SEPARATOR != '\\') { // broken links do not work on Windows !
			$tests[] = '##### FileSystem OPERATIONS / TESTS - ALL: #####';
		} else {
			$tests[] = '##### FileSystem OPERATIONS / TESTS *** PARTIAL SUPPORT ONLY (BY PLATFORM) ***: #####';
		} //end if else
		//--

		//--
		$test_string = '#START#'."\n".'グッド'."\n".'SmartFramework/Test/FileSystem'."\n".time()."\n".SMART_FRAMEWORK_HTACCESS_NOINDEXING.SMART_FRAMEWORK_HTACCESS_FORBIDDEN.SMART_FRAMEWORK_HTACCESS_NOEXECUTION."\n".'#END#';
		$test_str_cksum = \SmartHashCrypto::sha512($test_string);
		$long_prefixed = \SmartFileSysUtils::prefixed_sha1_path(sha1(time()));
		$short_prefixed = \SmartFileSysUtils::prefixed_uuid10_dir(\Smart::uuid_10_seq());
		//--
		$the_base_folder = 'tmp/tests/';
		$the_sufx_folder = 'Folder1';
		$the_base_file = 'NORMAL-Write_123_@#.txt';
		//--
		$the_folder = $the_base_folder.$the_sufx_folder.'/';
		$the_copy_folder = $the_base_folder.'folder2';
		$the_move_folder = $the_base_folder.'FOLDER3';
		$the_extra_folder = $the_folder.'extra/';
		$the_file = $the_folder.$the_base_file;
		//--
		$get_folder = \SmartFileSysUtils::add_dir_last_slash(\SmartFileSysUtils::get_dir_from_path($the_folder));
		$get_file = \SmartFileSysUtils::get_file_name_from_path($the_file);
		$get_xfile = \SmartFileSysUtils::get_noext_file_name_from_path($the_file);
		$get_ext = \SmartFileSysUtils::get_file_extension_from_path($the_file);
		//--
		$the_sufx_copy = '.copy.txt';
		$the_copy_file = $the_file.$the_sufx_copy;
		$the_move_file = $the_extra_folder.$the_base_file.'.copy.moved.txt';
		$the_broken_link = $the_extra_folder.'a-broken-link';
		$the_broken_dir_link = $the_extra_folder.'a-broken-dir-link';
		$the_good_link = $the_extra_folder.'a-good-link';
		$the_good_dir_link = $the_extra_folder.'a-good-dir-link';
		//--

		//--
		$tests[] = 'INITIAL-FOLDER: '.$get_folder;
		$tests[] = 'NEW-FOLDER: '.$the_folder;
		$tests[] = 'NEW-FILE: '.$the_file;
		//--

		//--
		if((string)$err == '') {
			$the_test = 'CHECK TEST SAFE PATH NAME: DIR / FILE ...';
			$tests[] = $the_test;
			if(
				((string)\Smart::safe_pathname((string)$get_folder) !== (string)$get_folder) OR
				((string)\Smart::safe_pathname((string)$the_copy_file) !== (string)$the_copy_file) OR
				((string)\Smart::safe_pathname((string)\SmartFileSysUtils::get_dir_from_path($the_copy_file)) !== (string)rtrim($the_folder,'/')) OR
				((string)\Smart::safe_filename((string)\SmartFileSysUtils::get_file_name_from_path($the_copy_file)) !== (string)$the_base_file.$the_sufx_copy) OR
				((string)\Smart::safe_pathname('.') !== '') OR
				((string)\Smart::safe_filename('.') !== '') OR
				((string)\Smart::safe_validname('.') !== '') OR
				((string)\Smart::safe_username('.') !== '') OR
				((string)\Smart::safe_varname('.') !== '') OR
				((string)\Smart::safe_pathname('..') !== '') OR
				((string)\Smart::safe_filename('..') !== '') OR
				((string)\Smart::safe_validname('..') !== '') OR
				((string)\Smart::safe_username('..') !== '') OR
				((string)\Smart::safe_varname('..') !== '') OR
				((string)\Smart::safe_pathname('/') !== '') OR
				((string)\Smart::safe_filename('/') !== '') OR
				((string)\Smart::safe_validname('/') !== '') OR
				((string)\Smart::safe_username('/') !== '') OR
				((string)\Smart::safe_varname('/') !== '') OR
				((string)\Smart::safe_pathname('/.') !== '') OR
				((string)\Smart::safe_filename('/.') !== '') OR
				((string)\Smart::safe_validname('/.') !== '') OR
				((string)\Smart::safe_username('/.') !== '') OR
				((string)\Smart::safe_varname('/.') !== '') OR
				((string)\Smart::safe_pathname('/..') !== '') OR
				((string)\Smart::safe_filename('/..') !== '') OR
				((string)\Smart::safe_validname('/..') !== '') OR
				((string)\Smart::safe_username('/..') !== '') OR
				((string)\Smart::safe_varname('/..') !== '') OR
				((string)\Smart::safe_pathname('_a-zA-Z0-9-.@#/') !== '_a-zA-Z0-9-.@#/') OR
				((string)\Smart::safe_filename('_a-zA-Z0-9-.@#/') !== '_a-zA-Z0-9-.@#-') OR // slash is replaced by -
				((string)\Smart::safe_validname('_a-zA-Z0-9-.@#/') !== '_a-za-z0-9-.@-') OR // slash is replaced by - (from above)
				((string)\Smart::safe_username('_a-zA-Z0-9-.@#/') !== 'azaz09.') OR
				((string)\Smart::safe_varname('_a-zA-Z0-9-.@#/') !== '_azaz09')
			) {
				$err = 'ERROR: SAFE PATH NAME TEST ... FAILED !!!';
			} //end if
		} //end if
		//--
		if((string)$err == '') {
			$the_test = 'CHECK TEST VARIOUS ABSOLUTE AND BACKWARD PATHS ...';
			$tests[] = $the_test;
			if(
				(!\SmartFileSysUtils::check_if_safe_path('/this/is/absolute', 'no')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/absolute')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/../backward/path')) OR
				(\SmartFileSysUtils::check_if_safe_path('../backward/path')) OR
				(\SmartFileSysUtils::check_if_safe_path('#this/is/protected', 'yes', 'no')) OR
				(!\SmartFileSysUtils::check_if_safe_path('#this/is/protected', 'yes', 'yes'))
			) {
				$err = 'ERROR: CHECK TEST ABSOLUTE / BACKWARD / PROTECTED PATHS ... FAILED !!!';
			} //end if
		} //end if
		//--
		if((string)$err == '') {
			$the_test = 'CHECK TEST ABSOLUTE INVALID PATHS ...';
			$tests[] = $the_test;
			if(
				(\SmartFileSysUtils::check_if_safe_path('some/path:/this/is/absolute', 'no')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/absolute:some/path', 'no')) OR
				(\SmartFileSysUtils::check_if_safe_path('c:/this/is/absolute', 'no')) OR
				(\SmartFileSysUtils::check_if_safe_path(':/this/is/absolute', 'no')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/abso|lute', 'no')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/abso lute', 'no')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/abso:lute', 'no')) OR
				(\SmartFileSysUtils::check_if_safe_path('#this/is/protected', 'no'))
			) {
				$err = 'ERROR: CHECK TEST ABSOLUTE : INVALID / PROTECTED PATHS ... FAILED !!!';
			} //end if
		} //end if
		//--
		if((string)$err == '') {
			$the_test = 'CHECK TEST INVALID / DANGEROUS PATHS ...';
			$tests[] = $the_test;
			if(
				(\SmartFileSysUtils::check_if_safe_path('some/path:/this/is/absolute')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/absolute:some/path')) OR
				(\SmartFileSysUtils::check_if_safe_path('c:/this/is/absolute')) OR
				(\SmartFileSysUtils::check_if_safe_path(':/this/is/absolute')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/abso|lute')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/abso lute')) OR
				(\SmartFileSysUtils::check_if_safe_path('/this/is/abso:lute')) OR
				(\SmartFileSysUtils::check_if_safe_path('#this/is/protected')) OR
				(\SmartFileSysUtils::check_if_safe_file_or_dir_name('')) OR
				(\SmartFileSysUtils::check_if_safe_path('')) OR
				(\SmartFileSysUtils::check_if_safe_file_or_dir_name(' ')) OR
				(\SmartFileSysUtils::check_if_safe_path(' ')) OR
				(\SmartFileSysUtils::check_if_safe_file_or_dir_name('some fname with spaces')) OR
				(\SmartFileSysUtils::check_if_safe_path('some/path with spaces')) OR
				(\SmartFileSysUtils::check_if_safe_file_or_dir_name('.')) OR
				(\SmartFileSysUtils::check_if_safe_path('.')) OR
				(\SmartFileSysUtils::check_if_safe_path('/.')) OR
				(\SmartFileSysUtils::check_if_safe_path('/. ')) OR
				(\SmartFileSysUtils::check_if_safe_path(' /.')) OR
				(\SmartFileSysUtils::check_if_safe_path('relative/.')) OR
				(\SmartFileSysUtils::check_if_safe_path('relative/. ')) OR
				(\SmartFileSysUtils::check_if_safe_file_or_dir_name('..')) OR
				(\SmartFileSysUtils::check_if_safe_path('..')) OR
				(\SmartFileSysUtils::check_if_safe_path('/..')) OR
				(\SmartFileSysUtils::check_if_safe_path('/.. ')) OR
				(\SmartFileSysUtils::check_if_safe_path(' /..')) OR
				(\SmartFileSysUtils::check_if_safe_path('relative/..')) OR
				(\SmartFileSysUtils::check_if_safe_path('relative/.. ')) OR
				(\SmartFileSysUtils::check_if_safe_path('.../test')) OR
				(\SmartFileSysUtils::check_if_safe_path('a\\path\\with\\backslashes'))
			) {
				$err = 'ERROR: CHECK TEST INVALID / PROTECTED PATHS ... FAILED !!!';
			} //end if
		} //end if
		//--
		if((string)$err == '') {
			$the_test = 'CHECK EXTRACT FOLDER FROM PATH ...';
			$tests[] = $the_test;
			if((string)$get_folder != (string)\SmartFileSysUtils::add_dir_last_slash(\Smart::dir_name($the_folder))) {
				$err = 'ERROR: Path Extraction FAILED: Dir='.$get_folder.' ; DirName='.\SmartFileSysUtils::add_dir_last_slash(\Smart::dir_name($the_folder));
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'CHECK EXTRACT FILE AND EXTENSION FROM PATH (1) ...';
			$tests[] = $the_test;
			if((string)$get_folder.\SmartFileSysUtils::add_dir_last_slash($the_sufx_folder).$get_file != $the_file) {
				$err = 'ERROR :: Path Extraction FAILED: Re-Composed-File='.$get_folder.\SmartFileSysUtils::add_dir_last_slash($the_sufx_folder).$get_file.' ; File='.$the_file;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'CHECK EXTRACT FILE AND EXTENSION FROM PATH (2) ...';
			$tests[] = $the_test;
			if((string)$get_file != (string)$get_xfile.'.'.$get_ext) {
				$err = 'ERROR :: Path Extraction FAILED: File='.$get_file.' ; XFile='.$get_xfile.' ; Ext='.$get_ext;
			} //end if
		} //end if
		//--
		\SmartFileSysUtils::raise_error_if_unsafe_path($the_folder);
		if((string)$err == '') {
			$the_test = 'CHECK PATH NAME DIR: check_if_safe_path() : '.$the_folder;
			$tests[] = $the_test;
			$result = \SmartFileSysUtils::check_if_safe_path($the_folder);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		\SmartFileSysUtils::raise_error_if_unsafe_path($the_file);
		if((string)$err == '') {
			$the_test = 'CHECK PATH NAME FILE: check_if_safe_path() : '.$the_file;
			$tests[] = $the_test;
			$result = \SmartFileSysUtils::check_if_safe_path($the_file);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--

		//--
		if((string)$err == '') {
			$parent_folder = \SmartFileSysUtils::add_dir_last_slash('');
			$the_test = 'Check Add Dir Last (trailing) Slash: Empty Folder Name';
			$tests[] = $the_test;
			if((string)$parent_folder != './') {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$parent_folder = \SmartFileSysUtils::add_dir_last_slash('.');
			$the_test = 'Check Add Dir Last (trailing) Slash: Dot Folder Name: '.$parent_folder;
			$tests[] = $the_test;
			if((string)$parent_folder != './') {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$parent_folder = \SmartFileSysUtils::add_dir_last_slash('./');
			$the_test = 'Check Add Dir Last (trailing) Slash: DotSlash Folder Name: '.$parent_folder;
			$tests[] = $the_test;
			if((string)$parent_folder != './') {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$parent_folder = \SmartFileSysUtils::add_dir_last_slash(\Smart::dir_name($the_base_folder));
			$the_test = 'Check Parent Dir Name with Add Dir Last (trailing) Slash: '.$parent_folder.' # from: '.$the_base_folder;
			$tests[] = $the_test;
			if((string)$parent_folder != 'tmp/') {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--

		//--
		if((string)$err == '') {
			if(\SmartFileSystem::is_type_dir($get_folder)) {
				$the_test = 'DIR DELETE - INIT CLEANUP: dir_delete() + recursive: '.$get_folder;
				$tests[] = $the_test;
				$result = \SmartFileSystem::dir_delete($the_base_folder, true);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} else {
				$tests[] = 'DIR DELETE - INIT CLEANUP: Test Not Run (folder does not exists): '.$get_folder;
			} //end if else
		} //end if
		//--
		if((string)$err == '') {
			$the_test = 'DIR CREATE RECURSIVE: dir_create() : '.$the_folder.$long_prefixed.$short_prefixed;
			$tests[] = $the_test;
			$result = \SmartFileSystem::dir_create($the_folder.$long_prefixed.$short_prefixed, true); // recursive
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'DIR CREATE NON-RECURSIVE: dir_create() : extra/ in : '.\Smart::dir_name($the_extra_folder);
			$tests[] = $the_test;
			$result = \SmartFileSystem::dir_create($the_extra_folder);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--
		if((string)DIRECTORY_SEPARATOR != '\\') { // broken links do not work on Windows !
			if((string)$err == '') {
				$the_test = 'CREATE BROKEN FILE LINK FOR DELETION (1): link_create() : as : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::link_create('tmp/cache', $the_broken_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'DELETE BROKEN FILE LINK (1): delete() : as : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::delete($the_broken_link);
				if(($result !== 1) || \SmartFileSystem::is_type_link($the_broken_link)) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'CREATE BROKEN FILE LINK FOR DELETION (2): link_create() : as : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::link_create('tmp/index.html', $the_broken_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'DELETE BROKEN FILE LINK (2): dir_delete() : as : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::dir_delete($the_broken_link);
				if(($result !== 1) || \SmartFileSystem::is_type_link($the_broken_link)) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'CREATE BROKEN FILE LINK: link_create() : as : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::link_create('tmp/index.html', $the_broken_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'CREATE BROKEN DIR LINK: link_create() : as : '.$the_broken_dir_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::link_create('tmp/', $the_broken_dir_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'CREATE A FILE LINK: link_create() tmp/index.html : as : '.$the_good_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::link_create(\Smart::real_path('tmp/index.html'), $the_good_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'COPY A FILE LINK: copy() '.$the_good_link.' : as : '.$the_good_link.'.copied';
				$tests[] = $the_test;
				$result = \SmartFileSystem::copy($the_good_link, $the_good_link.'.copied');
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'COPY A FILE LINK (2): copy() '.$the_good_link.' : as : '.$the_good_link.'.copied2';
				$tests[] = $the_test;
				$result = \SmartFileSystem::copy($the_good_link, $the_good_link.'.copied2');
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'DELETE A FILE LINK: delete() : '.$the_good_link.'.copied2';
				$tests[] = $the_test;
				$result = \SmartFileSystem::delete($the_good_link.'.copied2');
				if(($result !== 1) OR (\SmartFileSystem::path_exists($the_good_link.'.copied2'))) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'CREATE A DIR LINK: link_create() '.$the_good_dir_link.' : as : '.$the_good_dir_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::link_create(\Smart::real_path('tmp/'), $the_good_dir_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'RENAME A DIR LINK: dir_rename() '.$the_good_dir_link.' : as : '.$the_good_dir_link.'.renamed';
				$tests[] = $the_test;
				$result = \SmartFileSystem::dir_rename($the_good_dir_link, $the_good_dir_link.'.renamed');
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'CREATE A DIR LINK (2): link_create() tmp/ : as : '.$the_good_dir_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::link_create(\Smart::real_path('tmp/'), $the_good_dir_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'RENAME A DIR LINK (2): dir_rename() '.$the_good_dir_link.' : as : '.$the_good_dir_link.'.renamed2';
				$tests[] = $the_test;
				$result = \SmartFileSystem::dir_rename($the_good_dir_link, $the_good_dir_link.'.renamed2');
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'DELETE A DIR LINK: dir_delete() : '.$the_good_dir_link.'.renamed2';
				$tests[] = $the_test;
				$result = \SmartFileSystem::dir_delete($the_good_dir_link.'.renamed2');
				if(($result !== 1) OR (\SmartFileSystem::path_exists($the_good_dir_link.'.renamed2'))) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
		} //end if
		//--

		//--
		if((string)$err == '') {
			$the_test = 'FILE WRITE with empty content: write() : '.$the_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::write($the_file, '');
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE WRITE: write() / before append : '.$the_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::write($the_file, $test_string);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE GET MTIME: get_file_mtime() / before append : '.$the_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::get_file_mtime($the_file);
			if(!is_int($result) OR ((int)$result <= 0) OR ((int)$result < (int)time())) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE GET SIZE: get_file_size() / before append : '.$the_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::get_file_size($the_file);
			if(!is_int($result) OR ((int)$result <= 0) OR ((int)$result != (int)strlen((string)$test_string))) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE WRITE: write() +append : '.$the_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::write($the_file, $test_string, 'a');
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE READ / Append: read() Full Size + Test Path/RealPath Exist + isFile/!isLink/!isDir + Test Readable/Writable: '.$the_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::read($the_file);
			if(((string)\SmartHashCrypto::sha512($result) != (string)\SmartHashCrypto::sha512($test_string.$test_string)) OR (!\SmartFileSystem::path_exists($the_file)) OR (!\SmartFileSystem::path_real_exists($the_file)) OR (!\SmartFileSystem::is_type_file($the_file)) OR (\SmartFileSystem::is_type_link($the_file)) OR (\SmartFileSystem::is_type_dir($the_file)) OR (!\SmartFileSystem::have_access_read($the_file)) OR (!\SmartFileSystem::have_access_write($the_file))) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE WRITE: re-write() : '.$the_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::write($the_file, $test_string);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--

		//--
		if((string)DIRECTORY_SEPARATOR != '\\') { // broken links do not work on Windows !
			if((string)$err == '') {
				$the_test = 'FILE WRITE TO A BROKEN LINK: write() : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::write($the_broken_link, $test_string);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'DELETE THE BROKEN LINK AFTER write() and RE-CREATE IT : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::delete($the_broken_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'RE-CREATE BROKEN FILE LINK [AFTER WRITE]: link_create() : as : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::link_create('tmp/index.html', $the_broken_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'FILE WRITE: write_if_not_exists() with Content Compare to a broken link : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::write_if_not_exists($the_broken_link, $test_string, 'yes');
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'DELETE THE BROKEN LINK AFTER write_if_not_exists() and RE-CREATE IT : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::delete($the_broken_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			if((string)$err == '') {
				$the_test = 'RE-CREATE BROKEN FILE LINK [AFTER WRITE-IF-NOT-EXISTS]: link_create() : as : '.$the_broken_link;
				$tests[] = $the_test;
				$result = \SmartFileSystem::link_create('tmp/index.html', $the_broken_link);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
		} //end if
		//--

		//--
		if((string)$err == '') {
			$the_test = 'FILE WRITE: write_if_not_exists() without Content Compare : '.$the_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::write_if_not_exists($the_file, $test_string, 'no');
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--
		if((string)$err == '') {
			$the_test = 'FILE READ: read() Full Size: '.$the_file;
			$tests[] = $the_test;
			$result = (string) \SmartFileSystem::read($the_file);
			if((string)\SmartHashCrypto::sha512($result) != (string)$test_str_cksum) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE READ: read() Partial Size, First 11 bytes: '.$the_file;
			$tests[] = $the_test;
			$result = (string) \SmartFileSystem::read($the_file, 11);
			if((strlen($result) !== 11) OR ((string)sha1($result) != (string)\SmartHashCrypto::sha1(substr($test_string, 0, 11)))) { // here we read bytes so substr() not \SmartUnicode::sub_str() should be used
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--
		if((string)$err == '') {
			$the_test = 'FILE COPY: copy() : '.$the_file.' to: '.$the_copy_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::copy($the_file, $the_copy_file);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE COPY with OVERWRITE: copy() : '.$the_file.' to: '.$the_copy_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::copy($the_file, $the_copy_file, true); // overwrite destination file(s)
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE RE-COPY (test should re-write the destination): copy() : '.$the_file.' to: '.$the_move_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::copy($the_file, $the_move_file);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} else {
				$the_test = 'FILE DELETE: delete() : '.$the_move_file;
				$tests[] = $the_test;
				$result = 0;
				$result = \SmartFileSystem::delete($the_move_file);
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'FILE RENAME/MOVE: rename() : '.$the_copy_file.' to: '.$the_move_file;
			$tests[] = $the_test;
			$result = \SmartFileSystem::rename($the_copy_file, $the_move_file);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--

		//--
		if(\SmartFileSystem::is_type_dir('_scripts/')) {
			//--
			if((string)$err == '') {
				$the_test = 'GET STORAGE DIR [DEVELOPMENT]: get_storage() : '.'_scripts/';
				$tests[] = $the_test;
				$result = (new \SmartGetFileSystem(true))->get_storage('_scripts/', true, true);
				if(\Smart::array_size($result) <= 0) {
					$err = 'ERROR :: '.$the_test.' #RESULT=NOT-ARRAY';
				} else {
					if(\Smart::array_size($result['list-dirs']) <= 0) {
						$err = 'ERROR :: '.$the_test.' #RESULT[list-dirs]=NOT-ARRAY';
					} //end if
					if(\Smart::array_size($result['list-dirs']) != (int)$result['dirs']) {
						$err = 'ERROR :: '.$the_test.' #RESULT[list-dirs]!=RESULT[dirs]';
					} //end if
					if(\Smart::array_size($result['list-files']) <= 0) {
						$err = 'ERROR :: '.$the_test.' #RESULT[list-files]=NOT-ARRAY';
					} //end if
					if(\Smart::array_size($result['list-files']) != (int)$result['files']) {
						$err = 'ERROR :: '.$the_test.' #RESULT[list-files]!=RESULT[files]';
					} //end if
				} //end if
			} //end if
			//--
			if((string)$err == '') {
				$the_test = 'RECURSIVE COPY (CLONE) DIR [DEVELOPMENT]: dir_copy() : '.'_scripts/'.' to: '.$the_folder.'_scripts';
				$tests[] = $the_test;
				$result = \SmartFileSystem::dir_copy('_scripts/', $the_folder.'_scripts');
				if($result !== 1) {
					$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
				} //end if
			} //end if
			//--
			if((string)$err == '') {
				$the_test = 'DIR COMPARE THE [DEVELOPMENT] SOURCE WITH [DEVELOPMENT] DESTINATION AFTER DIR COPY AND DIR MOVE:'.' '.'compare_folders() : '.'_scripts/'.' with: '.$the_folder.'_scripts/';
				$tests[] = $the_test;
				$arr_diff = array();
				$arr_diff = \SmartFileSystem::compare_folders('_scripts', $the_folder.'_scripts', true, true);
				if(\Smart::array_size($arr_diff) > 0) {
					$err = 'ERROR :: '.$the_test.' #DIFFERENCES='.print_r($arr_diff,1);
				} //end if
			} //end if
			//--
		} else {
			$tests[] = 'GET STORAGE / RECURSIVE COPY / DIR COMPARE :: DIR [DEVELOPMENT]: Tests Not Run (Development environment not detected) ...';
		} //end if else
		//--

		//--
		if((string)$err == '') {
			$the_test = 'RECURSIVE COPY (CLONE) DIR: dir_copy() : '.$the_folder.' to: '.$the_copy_folder;
			$tests[] = $the_test;
			$result = \SmartFileSystem::dir_copy($the_folder, $the_copy_folder);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--
		if((string)$err == '') {
			$the_test = 'MOVE/RENAME DIR: dir_rename() : '.$the_copy_folder.' to: '.$the_move_folder;
			$tests[] = $the_test;
			$result = \SmartFileSystem::dir_rename($the_copy_folder, $the_move_folder);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--
		if((string)$err == '') {
			$the_test = 'DIR COMPARE THE SOURCE WITH DESTINATION AFTER DIR COPY AND DIR MOVE: '.$the_folder.' with: '.$the_move_folder;
			$tests[] = $the_test;
			$arr_diff = array();
			$arr_diff = \SmartFileSystem::compare_folders($the_folder, $the_move_folder, true, true);
			if(\Smart::array_size($arr_diff) > 0) {
				$err = 'ERROR :: '.$the_test.' #DIFFERENCES='.print_r($arr_diff,1);
			} //end if
		} //end if
		//--

		//--
		if((string)$err == '') {
			$the_test = 'DIR DELETE - SIMPLE: dir_delete() non-recursive: '.$the_extra_folder;
			$tests[] = $the_test;
			$result = \SmartFileSystem::dir_delete($the_extra_folder, false);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		if((string)$err == '') {
			$the_test = 'DIR DELETE - LAST CLEANUP: dir_delete() + recursive: '.$get_folder;
			$tests[] = $the_test;
			$result = \SmartFileSystem::dir_delete($the_base_folder, true);
			if($result !== 1) {
				$err = 'ERROR :: '.$the_test.' #RESULT='.$result;
			} //end if
		} //end if
		//--

		//--
		$time = 'TOTAL TIME was: '.(microtime(true) - $time);
		//--
		$end_tests = '##### END TESTS ... '.$time.' sec. #####';
		//--

		//--
		$img_check = 'modules/mod-samples/libs/templates/testunit/img/test-filesys.svg';
		if((string)$err == '') {
			$img_sign = 'lib/framework/img/sign-info.svg';
			$text_main = '<span style="color:#83B953;">Good ... Perfect &nbsp;&nbsp;&nbsp; :: &nbsp;&nbsp;&nbsp; グッド ... パーフェクト</span>';
			$text_info = '<h2><span style="color:#83B953;">All</span> the SmartFramework FS Operations <span style="color:#83B953;">Tests PASSED on PHP</span><hr></h2><div style="font-size:14px; white-space:nowrap;">'.\Smart::nl_2_br(\Smart::escape_html(implode("\n".'* ', $tests)."\n".$end_tests)).'</div>';
		} else {
			$img_sign = 'lib/framework/img/sign-error.svg';
			$text_main = '<span style="color:#FF5500;">An ERROR occured ... &nbsp;&nbsp;&nbsp; :: &nbsp;&nbsp;&nbsp; エラーが発生しました ...</span>';
			$text_info = '<h2><span style="color:#FF5500;">A test FAILED</span> when testing FS Operations.<span style="color:#FF5500;"><hr>FAILED Test Details</span>:</h2><br><h5 class="inline">'.\Smart::escape_html($tests[\Smart::array_size($tests)-1]).'</h5><br><span style="font-size:14px;"><pre>'.\Smart::escape_html($err).'</pre></span>';
		} //end if else
		//--
		$test_info = 'FileSystem Operations Test Suite for SmartFramework: PHP';
		//--
		$test_heading = 'SmartFramework LibFileSystem Tests: DONE ...';
		//--

		//--
		return (string) \SmartMarkersTemplating::render_file_template(
			'modules/mod-samples/libs/templates/testunit/partials/test-dialog.inc.htm',
			[
				//--
				'TEST-HEADING' 		=> (string) $test_heading,
				//--
				'DIALOG-WIDTH' 		=> '780',
				'DIALOG-HEIGHT' 	=> '475',
				'IMG-SIGN' 			=> (string) $img_sign,
				'IMG-CHECK' 		=> (string) $img_check,
				'TXT-MAIN-HTML' 	=> (string) $text_main,
				'TXT-INFO-HTML' 	=> (string) $text_info,
				'TEST-INFO' 		=> (string) $test_info
				//--
			]
		);
		//--

	} //END FUNCTION
	//============================================================


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


// end of php code
?>