<?php
// [LIB - SmartFramework / ExtraLibs / MongoDB Database Client]
// (c) 2006-2018 unix-world.org - all rights reserved
// v.3.7.7 r.2018.10.19 / smart.framework.v.3.7

//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.3.7')) {
	@http_response_code(500);
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//======================================================
// Smart-Framework - MongoDB Client
// DEPENDS:
//	* Smart::
// DEPENDS-EXT: PHP MongoDB / PECL (v.1.0.1 or later)
//======================================================
// Tested and Stable on MongoDB Server versions:
// 3.2 / 3.3 / 3.4 / 3.5 / 3.6
//======================================================


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


/**
 * Class Smart MongoDB Client (for PHP-MongoDB v.1.0.1 or later)
 *
 * @usage  		dynamic object: (new Class())->method() - This class provides only DYNAMIC methods
 *
 * @access 		PUBLIC
 * @depends 	extensions: PHP MongoDB (v.1.0.1 or later) ; classes: Smart
 * @version 	v.181019
 * @package 	Database:MongoDB
 *
 * @method MIXED		count($strCollection, $arrQuery)										# count documents in a collection
 * @method MIXED		find($strCollection, $arrQuery, $arrProjFields, $arrOptions)			# find single or multiple ddocument(s) in a collection with optional filter criteria / limit
 * @method MIXED		findone($strCollection, $arrQuery, $arrProjFields, $arrOptions)			# find single document in a collection with optional filter criteria / limit
 * @method MIXED		bulkinsert($strCollection, $arrMultiDocs)								# add multiple documents to a collection
 * @method MIXED		insert($strCollection, $arrDoc)											# add single document to a collection
 * @method MIXED		update($strCollection, $arrFilter, $strUpdOp, $arrUpd)					# modify single or many document(s) in a collection that are matching the filter criteria
 * @method MIXED		delete($strCollection, $arrFilter)										# delete single or many document(s) from a collection that are matching the filter criteria
 * @method MIXED		command($arrCmd)														# run a command over database like: distinct, groupBy, mapReduce, createCollection, dropCollection
 * @method MIXED		igcommand($arrCmd)														# run a command over database (but in case of error will ignore stop execution and will return the errors instead of result) like: createCollection which may throw errors if collection already exists, dropCollection (similar if does not exists)
 *
 */
final class SmartMongoDb {

	// ->


/** @var string */
private $server;
private $srvver;

/** @var string */
private $db;

/** @var timeout */
private $timeout;

/** @var resource */
private $mongodbclient;

/** @var $mongodb */
private $mongodb;

/** @var $collection */
private $collection;

/** @var slow_time */
private $slow_time = 0.0035;

/** @var fatal_err */
private $fatal_err = true;

/** @var connex_key */
private $connex_key = '';

/** @var connected */
private $connected = false;


//======================================================
/**
 * Class constructor
 *
 * @param 	STRING 	$col 				:: MongoDB Collection
 * @param 	ARRAY 	$y_configs_arr 		:: The Array of Configuration parameters - the ARRAY STRUCTURE should be identical with the default config.php: $configs['mongodb'].
 *
 */
public function __construct($y_configs_arr=array(), $y_fatal_err=true) {

	//--
	if(version_compare(phpversion('mongodb'), '1.0.1') < 0) {
		$this->error('[INIT]', 'PHP MongoDB Extension', 'CHECK PHP MongoDB Version', 'This version of MongoDB Client Library needs MongoDB PHP Extension v.1.0.1 or later');
		return;
	} //end if
	//--

	//--
	$this->fatal_err = (bool) $y_fatal_err;
	//--

	//--
	$y_configs_arr = (array) $y_configs_arr;
	//--
	if(Smart::array_size($y_configs_arr) <= 0) { // if not from constructor, try to use the default
		$y_configs_arr = Smart::get_from_config('mongodb');
	} //end if
	//--
	if(Smart::array_size($y_configs_arr) > 0) {
		$type 		= (string) $y_configs_arr['type'];
		$db 		= (string) $y_configs_arr['dbname'];
		$host 		= (string) $y_configs_arr['server-host'];
		$port 		= (string) $y_configs_arr['server-port'];
		$timeout 	= (string) $y_configs_arr['timeout'];
		$username 	= (string) $y_configs_arr['username'];
		$password 	= (string) $y_configs_arr['password'];
		$timeslow 	= (float)  $y_configs_arr['slowtime'];
	//	$transact 	= (string) $y_configs_arr['transact']; // reserved for future usage
	} else {
		$this->error('[CHECK-CONFIGS]', 'MongoDB Configuration Init', 'CHECK Connection Config', 'Empty Configuration');
		return;
	} //end if
	//--
	if((string)$type != 'mongo-cluster') {
		$type = 'mongo-standalone';
	} //end if else
	//--
	if((string)$password != '') {
		$password = (string) base64_decode((string)$password);
	} //end if
	//--
	if(((string)$host == '') OR ((string)$port == '') OR ((string)$db == '') OR ((string)$timeout == '')) {
		$this->error('[CHECK-CONFIGS]', 'MongoDB Configuration Init', 'CHECK Connection Params: '.$host.':'.$port.'@'.$db, 'Some Required Parameters are Empty');
		return;
	} //end if
	//--
	$this->srvver = '';
	$this->server = (string) $host.':'.$port;
	$this->db = (string) $db;
	//--
	$this->timeout = Smart::format_number_int($timeout, '+');
	if($this->timeout < 1) {
		$this->timeout = 1;
	} //end if
	if($this->timeout > 60) {
		$this->timeout = 60;
	} //end if
	//--
	if(SmartFrameworkRuntime::ifDebug()) {
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
			'type' => 'metainfo',
			'data' => 'MongoDB App Connector Version: '.SMART_FRAMEWORK_VERSION
		]);
		//--
		if((float)$timeslow > 0) {
			$this->slow_time = (float) $timeslow;
		} else {
			$this->slow_time = 0.0035; // default slow time for mongodb
		} //end if
		if($this->slow_time < 0.0000001) {
			$this->slow_time = 0.0000001;
		} elseif($this->slow_time > 0.9999999) {
			$this->slow_time = 0.9999999;
		} //end if
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|slow-time', number_format($this->slow_time, 7, '.', ''), '=');
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
			'type' => 'metainfo',
			'data' => 'Fast Query Reference Time < '.number_format($this->slow_time, 7, '.', '').' seconds'
		]);
		//--
	} //end if
	//--

	//--
	$this->connex_key = (string) $this->server.'@'.$this->db.'#'.$username;
	//--

	//--
	if(!class_exists('\\MongoDB\\Driver\\Manager')) {
		$this->error((string)$this->connex_key, 'MongoDB Driver', 'PHP MongoDB Driver Manager is not available', '');
		return;
	} //end if
	//--
	if(!class_exists('\\MongoDB\\Driver\\Command')) {
		$this->error((string)$this->connex_key, 'MongoDB Driver', 'PHP MongoDB Driver Command is not available', '');
		return;
	} //end if
	//--
	if(!class_exists('\\MongoDB\\Driver\\Query')) {
		$this->error((string)$this->connex_key, 'MongoDB Driver', 'PHP MongoDB Driver Query is not available', '');
		return;
	} //end if
	//--
	if(!class_exists('\\MongoDB\\Driver\\BulkWrite')) {
		$this->error((string)$this->connex_key, 'MongoDB Driver', 'PHP MongoDB Driver BulkWrite is not available', '');
		return;
	} //end if
	//--
	if(!class_exists('\\MongoDB\\Driver\\WriteResult')) {
		$this->error((string)$this->connex_key, 'MongoDB Driver', 'PHP MongoDB Driver WriteResult is not available', '');
		return;
	} //end if
	//--

	//--
	$this->connect($type, $username, $password);
	//--

} //END FUNCTION
//======================================================


//======================================================
// a replacement for the default MongoDB object ID, will generate a 32 characters very unique UUID (base36)
public function assign_uuid() {

	//--
	return (string) Smart::uuid_10_seq().'-'.Smart::uuid_10_num().'-'.Smart::uuid_10_str();
	//--

} //END FUNCTION
//======================================================


//======================================================
public function get_server_version() {

	//--
	if((string)trim((string)$this->srvver) == '') {
		//--
		$arr_build_info = $this->command(['buildinfo' => true]);
		//--
		if(is_array($arr_build_info)) {
			if(is_array($arr_build_info[0])) {
				$this->srvver = (string) trim((string)$arr_build_info[0]['version']);
			} //end if
		} //end if
		//--
		$arr_build_info = null;
		//--
	} //end if
	//--
	if((string)trim((string)$this->srvver) == '') {
		$this->srvver = '0.0'; // avoid requery
	} //end if
	//--

	//--
	return (string) $this->srvver;
	//--

} //END FUNCTION
//======================================================


//======================================================
// test a command output for 0/OK=1
public function command_is_ok($result) {

	//--
	$is_ok = false;
	//--
	if(is_array($result)) {
		if(is_array($result[0])) {
			if((int)$result[0]['ok'] == 1) {
				$is_ok = true;
			} //end if
		} //end if
	} //end if
	//--

	//--
	return (bool) $is_ok;
	//--

} //END FUNCTION
//======================================================


//======================================================
/**
 * this is the Magic Call Method
 *
 * @access 		private
 * @internal
 *
 */
public function __call($method, array $args) {

	//--
	$method = (string) $method;
	$args = (array) $args;
	//--
	if(SmartFrameworkRuntime::ifDebug()) {
		$time_start = microtime(true);
	} //end if
	//--

	//--
	if(!is_object($this->mongodbclient)) {
		$this->error((string)$this->connex_key, 'MongoDB Initialize', 'MongoDB->INIT-MANAGER() :: '.$this->connex_key.'/'.$this->collection, 'ERROR: MongoDB Manager Object is null ...');
		return null;
	} //end if
	//--

	//--
	$obj = null;
	$qry = array();
	$opts = array();
	$drows = 0;
	$dcmd = 'nosql';
	//--
	switch((string)$method) {
		//-- collection methods
		case 'count': // ARGS [ strCollection, arrQuery ]
			//--
			$dcmd = 'count';
			//--
			$this->collection = trim((string)$args[0]); // strCollection
			if((string)trim((string)$this->collection) == '') {
				$this->error((string)$this->connex_key, 'MongoDB Count', 'MongoDB->'.$method.'()', 'ERROR: Empty Collection name ...', $args);
				return 0;
			} //end if
			//--
			$qry = (array) $args[1]; // arrQuery
			//--
			$command = new \MongoDB\Driver\Command([
				'count' => (string) $this->collection,
				'query' => (array) $qry
			]);
			if(!is_object($command)) {
				$this->error((string)$this->connex_key, 'MongoDB Count', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Command Object is null ...', $args);
				return 0;
			} //end if
			//--
			try {
				$cursor = $this->mongodbclient->executeCommand($this->db, $command);
			} catch(Exception $err) {
				$this->error((string)$this->connex_key, 'MongoDB Count Execute', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: '.$err->getMessage(), $args);
				return 0;
			} //end try
			if(!is_object($cursor)) {
				$this->error((string)$this->connex_key, 'MongoDB Count Cursor', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Cursor Object is null ...', $args);
				return 0;
			} //end if
			$cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);
			//print_r($cursor->toArray()); die();
			$obj = 0;
			if(is_object($cursor)) {
				$tmp_obj = (array) $cursor->toArray();
				if(is_array($tmp_obj[0])) {
					$tmp_obj = (array) $tmp_obj[0];
					if(array_key_exists('n', (array)$tmp_obj)) {
						if((int)$tmp_obj['ok'] == 1) {
							$obj = (int) $tmp_obj['n'];
							$drows = (int) $obj;
						} //end if
					} //end if
				} //end if
				$tmp_obj = array(); // free mem
			} //end if object
			//--
			unset($cursor);
			unset($command);
			//--
			break;
		//--
		case 'find': 	// ARGS [ strCollection, arrQuery, arrProjFields, arrOptions ]
		case 'findone': // ARGS [ strCollection, arrQuery, arrProjFields, arrOptions ]
			//--
			$dcmd = 'read';
			//--
			$this->collection = trim((string)$args[0]); // strCollection
			if((string)trim((string)$this->collection) == '') {
				$this->error((string)$this->connex_key, 'MongoDB Read', 'MongoDB->'.$method.'()', 'ERROR: Empty Collection name ...', $args);
				return array();
			} //end if
			//--
			$qry = (array) $args[1]; // arrQuery
			//--
			if(is_array($args[3])) {
				$opts = (array) $args[3]; // arrOptions
			} //end if
			//-- fix: find one must have limit 1
			if((string)$method == 'findone') {
				$opts['limit'] = 1;
			} //end if
			//-- fix: select just particular fields
			$opts['projection'] = array(); // arrProjFields
			if(Smart::array_size($args[2]) > 0) {
				foreach((array)$args[2] as $key => $val) {
					$val = (string) trim((string)$val);
					if((string)$val != '') {
						$opts['projection'][(string)$val] = 1;
					} //end if
				} //end foreach
			} //end if
			//print_r($opts); die();
			//--
			$query = new \MongoDB\Driver\Query( // max 2 parameters
				(array) $qry, // query (empty: select all)
				(array) $opts // options
			);
			//print_r($query); die();
			if(!is_object($query)) {
				$this->error((string)$this->connex_key, 'MongoDB Read', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Query Object is null ...', $args);
				return array();
			} //end if
			//--
			try {
				$cursor = $this->mongodbclient->executeQuery($this->db.'.'.$this->collection, $query);
			} catch(Exception $err) {
				$this->error((string)$this->connex_key, 'MongoDB Read Execute', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: '.$err->getMessage(), $args);
				return array();
			} //end try
			if(!is_object($cursor)) {
				$this->error((string)$this->connex_key, 'MongoDB Read Cursor', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Cursor Object is null ...', $args);
				return array();
			} //end if
			$cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);
			//print_r($cursor->toArray()); die();
			$obj = array();
			if(is_object($cursor)) {
				$obj = \Smart::json_decode(
					(string) \Smart::json_encode(
						(array)$cursor->toArray(),
						false, // no pretty print
						true, // unescaped unicode
						false // html safe
					),
					true // return array
				); // mixed, normalize via json:encode/decode
				if(!is_array($obj)) {
					$obj = array();
				} //end if
				$drows = (int) Smart::array_size($obj);
				if((string)$method == 'findone') {
					if(is_array($obj[0])) {
						$obj = (array) $obj[0];
						$drows = 1;
					} else {
						$obj = array();
						$drows = 0;
					} //end if
				} //end if
			} //end if object
			//--
			unset($cursor);
			unset($query);
			//--
			//print_r($obj); die();
			//--
			break;
		//--
		case 'bulkinsert': 	// ARGS [ strCollection, arrMultiDocs ] ; can do multiple inserts
		case 'insert': 		// ARGS [ strCollection, arrDoc ] ; can do just single insert
		case 'update': 		// ARGS [ strCollection, arrFilter, strUpdOp, arrUpd ] ; can do just single update
			//--
			$dcmd = 'write';
			//--
			$this->collection = trim((string)$args[0]); // strCollection
			if((string)trim((string)$this->collection) == '') {
				$this->error((string)$this->connex_key, 'MongoDB Write', 'MongoDB->'.$method.'()', 'ERROR: Empty Collection name ...', $args);
				return array();
			} //end if
			//--
			$write = new \MongoDB\Driver\BulkWrite();
			if(!is_object($write)) {
				$this->error((string)$this->connex_key, 'MongoDB Write', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Write Object is null ...', $args);
				return array();
			} //end if
			//--
			$num_docs = 0;
			//--
			if((string)$method == 'bulkinsert') {
				if(Smart::array_type_test($args[1]) === 1) { // 1: non-associative array of multi docs
					$qry = 'bulkinsert['.Smart::array_size($args[1]).']';
					$opts = [];
					for($i=0; $i<Smart::array_size($args[1]); $i++) {
						if(Smart::array_size($args[1][$i]) > 0) {
							$write->insert(
								(array) $args[1][$i] // doc
							);
							$num_docs++;
						} else {
							$this->error((string)$this->connex_key, 'MongoDB Write', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Multi-Document #'.$i.' is empty or not array ...', $args);
							return array();
							break;
						} //end if
					} //end for
				} else {
					$this->error((string)$this->connex_key, 'MongoDB Write', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Invalid Multi-Document structure ...', $args);
					return array();
				} //end if else
			} elseif((string)$method == 'insert') {
				$qry = 'insert';
				$opts = [];
				if(Smart::array_size($args[1]) > 0) {
					$write->insert(
						(array) $args[1] // doc
					);
					$num_docs++;
				} else {
					$this->error((string)$this->connex_key, 'MongoDB Write', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Document is empty or not array ...', $args);
					return array();
					break;
				} //end if
			} elseif((string)$method == 'update') {
				if(!is_array($args[1])) {
					$this->error((string)$this->connex_key, 'MongoDB Write', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Invalid Filter provided ...', $args);
					return array();
				} //end if
				$qry = (string) 'update:'.$args[2];
				$opts = [ // update options
					'multi' 	=> true, // update all the matching documents
					'upsert' 	=> false // if filter does not match an existing document, do not insert a single document
				];
				if(Smart::array_size($args[3]) > 0) {
					$write->update(
						(array) $args[1], 									// filter
						(array) [ (string)$args[2] => (array)$args[3] ], 	// must be in format: [ '$set|$inc|$mul|...' => (array)$doc ]
						(array) $opts										// options
					);
					$num_docs++;
				} else {
					$this->error((string)$this->connex_key, 'MongoDB Write', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Document is empty or not array or invalid format ...', $args);
					return array();
					break;
				} //end if
			} //end if else
			//--
			if($num_docs <= 0) {
				$this->error((string)$this->connex_key, 'MongoDB Write', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: No valid document(s) found ...', $args);
				return array();
			} //end if
			//--
			try {
				$result = $this->mongodbclient->executeBulkWrite($this->db.'.'.$this->collection, $write);
			} catch(Exception $err) {
				$this->error((string)$this->connex_key, 'MongoDB Write Execute', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: '.$err->getMessage(), $args);
				return array();
			} //end try
			if(!is_object($result)) {
				$this->error((string)$this->connex_key, 'MongoDB Write Result', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Result Object is null ...', $args);
				return array();
			} //end if
			$obj = array();
			if($result instanceof \MongoDB\Driver\WriteResult) {
				$msg = (string) implode("\n", (array)$result->getWriteErrors());
				$msg = (string) trim((string)$msg);
				if((string)$msg == '') {
					$msg = 'oknosqlwriteoperation';
				} //end if
				$obj[0] = (string) $msg;
				$obj[1] = 0;
				if(((string)$method == 'insert') OR ((string)$method == 'bulkinsert')) {
					$obj[1] = (int) $result->getInsertedCount();
				} elseif((string)$method == 'update') {
					$obj[1] = (int) $result->getModifiedCount();
				} //end if else
				$obj[2] = (string) $qry;
				$msg = '';
				$drows = (int) $obj[1];
			} else {
				$this->error((string)$this->connex_key, 'MongoDB Write Result Type', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Result Object is not instance of WriteResult ...', $args);
				return array();
			} //end if
			//--
			//print_r($result); die();
			//--
			unset($result);
			unset($write);
			//--
			//print_r($obj); die();
			//--
			break;
		//--
		case 'delete': // ARGS [ strCollection, arrFilter ]
			//--
			$dcmd = 'write';
			//--
			$this->collection = trim((string)$args[0]); // strCollection
			if((string)trim((string)$this->collection) == '') {
				$this->error((string)$this->connex_key, 'MongoDB Delete', 'MongoDB->'.$method.'()', 'ERROR: Empty Collection name ...', $args);
				return array();
			} //end if
			//--
			$write = new \MongoDB\Driver\BulkWrite();
			if(!is_object($write)) {
				$this->error((string)$this->connex_key, 'MongoDB Delete', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Write Object is null ...', $args);
				return array();
			} //end if
			//--
			if(!is_array($args[1])) {
				$this->error((string)$this->connex_key, 'MongoDB Delete', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Invalid Filter provided ...', $args);
				return array();
			} //end if
			$qry = 'delete';
			$opts = [ // delete options
				'limit' => false // delete all matching documents
			];
			$write->delete(
				(array) $args[1], 									// filter
				(array) $opts										// options
			);
			try {
				$result = $this->mongodbclient->executeBulkWrite($this->db.'.'.$this->collection, $write);
			} catch(Exception $err) {
				$this->error((string)$this->connex_key, 'MongoDB Delete Execute', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: '.$err->getMessage(), $args);
				return array();
			} //end try
			if(!is_object($result)) {
				$this->error((string)$this->connex_key, 'MongoDB Delete Result', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Result Object is null ...', $args);
				return array();
			} //end if
			$obj = array();
			if($result instanceof \MongoDB\Driver\WriteResult) {
				$msg = (string) implode("\n", (array)$result->getWriteErrors());
				$msg = (string) trim((string)$msg);
				if((string)$msg == '') {
					$msg = 'oknosqlwriteoperation';
				} //end if
				$obj[0] = (string) $msg;
				$obj[1] = (int) $result->getDeletedCount();
				$obj[2] = (string) $qry;
				$msg = '';
				$drows = (int) $obj[1];
			} else {
				$this->error((string)$this->connex_key, 'MongoDB Delete Result Type', 'MongoDB->'.$method.'() :: '.$this->collection, 'ERROR: Result Object is not instance of WriteResult ...', $args);
				return array();
			} //end if
			//--
			//print_r($result); die();
			//--
			unset($result);
			unset($write);
			//--
			//print_r($obj); die();
			//--
			break;
		//--
		case 'command': 	// ARGS [ arrCmd ]
		case 'igcommand': 	// ARGS [ arrCmd ]
			//--
			$qry = (array) $args[0]; // arrQuery
			//--
			$command = new \MongoDB\Driver\Command((array)$qry);
			if(!is_object($command)) {
				$this->error((string)$this->connex_key, 'MongoDB Command', 'MongoDB->'.$method.'()', 'ERROR: Command Object is null ...', $args);
				return array();
			} //end if
			//--
			$igerr = false;
			try {
				$cursor = $this->mongodbclient->executeCommand($this->db, $command);
			} catch(Exception $err) {
				if((string)$method == 'igcommand') {
					$igerr = (string) $err->getMessage(); // must be type string
				} else {
					$this->error((string)$this->connex_key, 'MongoDB Command Execute', 'MongoDB->'.$method.'()', 'ERROR: '.$err->getMessage(), $args);
					return array();
				} //end if else
			} //end try
			$obj = array();
			if(((string)$method == 'command') OR ($igerr === false)) {
				if(!is_object($cursor)) {
					$this->error((string)$this->connex_key, 'MongoDB Command Cursor', 'MongoDB->'.$method.'()', 'ERROR: Cursor Object is null ...', $args);
					return array();
				} //end if
				$cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);
				//print_r($cursor->toArray()); die();
				if(is_object($cursor)) {
					$obj = \Smart::json_decode(
						(string) \Smart::json_encode(
							(array)$cursor->toArray(),
							false, // no pretty print
							true, // unescaped unicode
							false // html safe
						),
						true // return array
					); // mixed, normalize via json:encode/decode
					if(!is_array($obj)) {
						$obj = array();
					} //end if
					$drows = (int) Smart::array_size($obj);
				} //end if object
			} else {
				$obj = array(
					'ERRORS' => [
						'err-msg' 	=> (string) $igerr,
						'type' 		=> 'catcheable PHP Exception / MongoDB Manager: executeCommand',
						'class' 	=> (string) __CLASS__,
						'function' 	=> (string) __FUNCTION__,
						'method' 	=> (string) $method
					]
				);
			} //end if else
			//--
			unset($cursor);
			unset($command);
			//print_r($obj); die();
			//--
			break;
		//--
		default:
			//--
			$this->error((string)$this->connex_key, 'MongoDB Method', 'MongoDB->'.$method.'()', 'ERROR: The selected method ['.$method.'] is NOT implemented ...', $args);
			return null;
			//--
	} //end switch
	//--

	//--
	if($this->connected === true) { // avoid register pre-connect commands like version)
		if(SmartFrameworkRuntime::ifDebug()) {
			//--
			SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|total-queries', 1, '+');
			//--
			$time_end = (float) (microtime(true) - (float)$time_start);
			//--
			SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|total-time', $time_end, '+');
			//--
			SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
				'type' => (string) $dcmd,
				'data' => strtoupper($method),
				'command' => array('Collection' => (string)$this->collection, 'Query' => (array)$qry, 'Options' => (array)$opts),
				'time' => Smart::format_number_dec($time_end, 9, '.', ''),
				'rows' => (int) $drows,
				'connection' => (string)$this->connex_key,
			]);
			//--
		} //end if
	} //end if
	//--

	//--
	return $obj; // mixed
	//--

} //END FUNCTION
//======================================================


//======================================================
/**
 * this is the internal connector (will connect just when needed)
 *
 * @access 		private
 * @internal
 *
 */
private function connect($type, $username, $password) {

	//--
	if((string)$type == 'mongo-cluster') { // cluster (sharding)
		$concern_rd = 'majority'; // requires the servers to be started with --enableMajorityReadConcern
		$concern_wr = 'majority'; // make sense if with a sharding cluster
	} else { // mongo-standalone
		$concern_rd = 'local';
		$concern_wr = 1;
	} //end if else
	//--

	//--
	$options = array(
		'connect' 			=> false,
		'connectTimeoutMS' 	=> (int) ($this->timeout * 1000),
		'socketTimeoutMS' 	=> (int) (SMART_FRAMEWORK_NETSOCKET_TIMEOUT * 1000),
		'readConcernLevel' 	=> $concern_rd, // rd concern
		'w' 				=> $concern_wr, // wr concern
		'wTimeoutMS' 		=> (int) (SMART_FRAMEWORK_NETSOCKET_TIMEOUT * 1000) // if this is 0 (no timeout) the write operation will block indefinitely
	);
	//--
	if((string)$username != '') {
		$options['username'] = (string) $username;
		if((string)$password != '') {
			$options['password'] = (string) $password;
			$options['authMechanism'] = 'MONGODB-CR';
		} //end if
	} //end if
	//--

	//--
	if(is_object(SmartFrameworkRegistry::$Connections['mongodb'][(string)$this->connex_key])) {
		//--
		$this->mongodbclient = &SmartFrameworkRegistry::$Connections['mongodb'][(string)$this->connex_key];
		$this->connected = true;
		//--
		if(SmartFrameworkRuntime::ifDebug()) {
			SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
				'type' => 'open-close',
				'data' => 'Re-Using MongoDB Manager Instance :: ServerType ['.$type.']: '.$this->connex_key
			]);
		} //end if
		//--
	} else {
		//--
		try {
			$this->mongodbclient = new \MongoDB\Driver\Manager(
				(string) 'mongodb://'.$this->server.'/'.$this->db,
				(array) $options
			);
		} catch(Exception $err) {
			$this->mongodbclient = null;
			$this->error((string)$this->connex_key, 'MongoDB Manager', 'Failed to Initialize Object: '.$this->db.' on '.$this->server, 'ERROR: '.$err->getMessage());
			return false;
		} //end try catch
		//--
		if(SmartFrameworkRuntime::ifDebug()) {
			SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
				'type' => 'open-close',
				'data' => 'Creating MongoDB Manager Instance :: ServerType ['.$type.']: '.$this->connex_key
			]);
		} //end if
		//--
		$this->get_server_version(); // this will register the $this->srvver if req.
		//--
		$min_ver_srv = '2.4.0';
		if(((string)$this->srvver == '') OR (version_compare((string)$min_ver_srv, (string)$this->srvver) > 0)) {
			$this->mongodbclient = null;
			$this->error((string)$this->connex_key, 'MongoDB Manager', 'Invalid MongoDB Server Version on '.$this->server, 'ERROR: Minimum MongoDB supported Server version is: '.$min_ver_srv.' but this Server version is: '.$this->srvver);
			return false;
		} //end if
		//--
		if(SmartFrameworkRuntime::ifDebug()) {
			//--
			SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
				'type' => 'metainfo',
				'data' => 'MongoDB Server Version: '.$this->srvver
			]);
			//--
		} //end if
		//--
		SmartFrameworkRegistry::$Connections['mongodb'][(string)$this->connex_key] = &$this->mongodbclient; // export connection
		$this->connected = true;
		//--
	} //end if else
	//--

	//--
	if(SmartFrameworkRuntime::ifDebug()) {
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
			'type' => 'set',
			'data' => 'Using Database: '.$this->db,
			'connection' => (string) $this->server,
			'skip-count' => 'yes'
		]);
		//--
	} //end if
	//--

	//--
	return true;
	//--

} //END FUNCTION
//======================================================


//======================================================
/**
 * this is for disconnect from MongoDB
 *
 * @access 		private
 * @internal
 *
 */
public function disconnect() {
	//--
	SmartFrameworkRegistry::$Connections['mongodb'][(string)$this->connex_key] = null; // close connection
	//--
	if(SmartFrameworkRuntime::ifDebug()) {
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
			'type' => 'open-close',
			'data' => 'Destroying MongoDB Manager Instance: '.$this->connex_key
		]);
	} //end if
	//--
} //END FUNCTION
//======================================================


//======================================================
/**
 * Displays the MongoDB Errors and HALT EXECUTION (This have to be a FATAL ERROR as it occur when a FATAL MongoDB ERROR happens or when a Data Query fails)
 * PRIVATE
 *
 * @param STRING $y_area :: The Area
 * @param STRING $y_error_message :: The Error Message to Display
 * @param STRING $y_query :: The query
 * @param STRING $y_warning :: The Warning Title
 *
 * @return :: HALT EXECUTION WITH ERROR MESSAGE
 *
 */
private function error($y_conhash, $y_area, $y_info, $y_error_message, $y_query='', $y_warning='') {
//--
if($this->fatal_err === false) {
	throw new Exception('#MONGO-DB@'.$y_conhash.'# :: Q# // MongoDB Client :: EXCEPTION :: '.$y_area."\n".$y_info.': '.$y_error_message);
	return;
} //end if
//--
$def_warn = 'Execution Halted !';
$y_warning = (string) trim((string)$y_warning);
if(Smart::array_size($y_query) > 0) {
	$y_query = (string) print_r($y_query,1);
} //end if
$the_params = '- '.'MongoDB Manager v.'.phpversion('mongodb').' -';
if(SmartFrameworkRuntime::ifDebug()) {
	$width = 750;
	$the_area = (string) $y_area;
	if((string)$y_warning == '') {
		$y_warning = (string) $def_warn;
	} //end if
	$the_error_message = 'Operation FAILED: '.$def_warn."\n".$y_error_message;
	$the_query_info = (string) trim((string)$y_query);
	$y_query = ' '.trim((string)$the_params."\n".$y_info."\n".$y_query);
} else {
	$width = 550;
	$the_area = '';
	$the_error_message = 'Operation FAILED: '.$def_warn;
	$y_query = ' '.trim((string)$the_params."\n".$y_info."\n".$y_query);
	$the_query_info = ''; // do not display query if not in debug mode ... this a security issue if displayed to public ;)
	$the_params = '';
} //end if else
//--
$out = SmartComponents::app_error_message(
	'MongoDB Manager',
	'MongoDB',
	'NoSQL/DB',
	'Server',
	'lib/core/img/db/mongodb-logo.svg',
	$width, // width
	$the_area, // area
	$the_error_message, // err msg
	$the_params, // title or params
	$the_query_info // command
);
//--
Smart::raise_error(
	'#MONGO-DB@'.$y_conhash.' :: Q# // MongoDB Client :: ERROR :: '.$y_area."\n".'*** Error-Message: '.$y_error_message."\n".'*** Statement:'.$y_query,
	$out // msg to display
);
die(''); // just in case
//--
} //END FUNCTION
//======================================================


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================

// end of php code
?>