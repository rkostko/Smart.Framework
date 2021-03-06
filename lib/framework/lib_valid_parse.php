<?php
// [LIB - SmartFramework / Smart Validators and Parsers]
// (c) 2006-2018 unix-world.org - all rights reserved
// v.3.7.7 r.2018.10.19 / smart.framework.v.3.7

//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.3.7')) {
	@http_response_code(500);
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//======================================================
// Smart-Framework - Validators and Parsers
// DEPENDS:
//	* Smart::
//  * SmartUnicode::
//======================================================


//=================================================================================
//================================================================================= CLASS START
//=================================================================================


/**
 * Class: SmartParser - Provides misc parsing methods.
 *
 * <code>
 * // Usage example:
 * SmartParser::some_method_of_this_class(...);
 * </code>
 *
 * @usage       static object: Class::method() - This class provides only STATIC methods
 *
 * @access      PUBLIC
 * @depends     classes: Smart, SmartUnicode
 * @version     v.170927
 * @package     Base
 *
 */
final class SmartParser {

	// ::


//================================================================
/**
 * Detect URL links in a text string
 *
 * @param 	STRING 	$string 			:: The text string to be processed
 *
 * @return 	ARRAY						:: A non-associative array with the URL links detected in the string
 */
public static function get_arr_urls($string) {
	$string = (string) $string;
	$expr = SmartValidator::regex_stringvalidation_expression('url', 'partial');
	$regex = $expr.'iu'; // insensitive, with /u modifier for unicode strings
	$arr = array();
	preg_match_all($regex, $string, $arr);
	return (array) $arr[0];
} //END FUNCTION
//================================================================


//================================================================
/**
 * Replace URL in a text string with HTML links <a href="(DetectedURL)" target="{target}">[DetectedURL]</a>
 *
 * @param 	STRING 	$string 			:: The text string to be processed
 * @param 	STRING	$ytarget			:: URL target ; default is '_blank' ; can be: '_self' or a specific window name: 'myWindow' ...
 * @param 	STRING	$ypict				:: The image path to display as link ; default is blank: ''
 * @param	INTEGER $y_lentrim			:: The length of the URL to be displayed into [DetectedURL] (used only if no image has been provided)
 *
 * @return 	STRING						:: The HTML processed text with URLs replaced with real tags
 */
public static function text_urls($string, $ytarget='_blank', $ypict='', $y_lentrim='100') {
	$string = (string) $string;
	$expr = SmartValidator::regex_stringvalidation_expression('url', 'partial');
	$regex = $expr.'iu'; //insensitive, with /u modifier for unicode strings
	if((string)$ypict == '') {
		$string = preg_replace_callback($regex, function($matches) use ($ytarget, $y_lentrim) { return '<a title="@URL@" id="url_recognition" href="'.Smart::escape_html($matches[0]).'" target="'.$ytarget.'">'.Smart::escape_html(Smart::text_cut_by_limit($matches[0], $y_lentrim)).'</a>'; }, $string);
	} else {
		$string = preg_replace_callback($regex, function($matches) use ($ytarget, $ypict, $y_lentrim) { return '<a title="@URL@" id="url_recognition" href="'.Smart::escape_html($matches[0]).'" target="'.$ytarget.'"><img border="0" src="'.$ypict.'" width="32" height="32" align="absmiddle" alt="'.Smart::escape_html($matches[0]).'" title="'.Smart::escape_html($matches[0]).'"></a>&nbsp;'.Smart::escape_html(Smart::text_cut_by_limit($matches[0], $y_lentrim)).'<br>'; }, $string);
	} //end if else
	return (string) $string;
} //END FUNCTION
//================================================================


//================================================================
/**
 * Detect EMAIL addresses in a text string
 *
 * @param 	STRING 	$string 			:: The text string to be processed
 *
 * @return 	ARRAY						:: A non-associative array with the EMAIL addresses detected in the string
 */
public static function get_arr_emails($string) {
	$string = (string) $string;
	$expr = SmartValidator::regex_stringvalidation_expression('email', 'partial');
	$regex = $expr.'iu'; //insensitive, with /u modifier for unicode strings
	$arr = array();
	preg_match_all($regex, $string, $arr);
	return (array) $arr[0];
} //END FUNCTION
//================================================================


//================================================================
/**
 * Replace EMAIL addresses in a text string with HTML links <a href="(emailAddr)" target="{target}">[emailAddr]</a>
 *
 * @param 	STRING 	$string 			:: The text string to be processed
 * @param 	STRING 	$yaction			:: Action to append the email link to ; Default is: 'mailto:' but can be for example: 'script.php?action=email&addr='
 * @param 	STRING	$ytarget			:: URL target ; default is '_blank' ; can be: '_self' or a specific window name: 'myWindow' ...
 *
 * @return 	STRING						:: The HTML processed text with EMAIL addresses replaced with real tags as links
 */
public static function text_emails($string, $yaction='mailto:', $ytarget='') {
	$string = (string) $string;
	$expr = SmartValidator::regex_stringvalidation_expression('email', 'partial');
	$regex = $expr.'iu'; //insensitive, with /u modifier for unicode strings
	$string = preg_replace_callback($regex, function($matches) use ($yaction, $ytarget) { return '<a title="@eMail@" id="url_recognition" href="'.Smart::escape_html($yaction.rawurlencode(trim($matches[0]))).'" target="'.$ytarget.'">'.Smart::escape_html(Smart::text_cut_by_limit($matches[0], 100)).'</a>'; }, $string);
	return (string) $string;
} //END FUNCTION
//================================================================


//================================================================
/**
 * Detect FAX numbers in a text string
 *
 * @param 	STRING 	$string 			:: The text string to be processed
 *
 * @return 	ARRAY						:: A non-associative array with the FAX numbers detected in the string
 */
public static function get_arr_faxnums($string) {
	$string = (string) $string;
	$expr = SmartValidator::regex_stringvalidation_expression('fax', 'partial');
	$regex = $expr.'iu'; //insensitive, with /u modifier for unicode strings
	$arr = array();
	preg_match_all($regex, $string, $arr);
	return (array) $arr[0];
} //END FUNCTION
//================================================================


//================================================================
/**
 * Replace FAX numbers in a text string with HTML links <a href="(faxNum)" target="{target}">[faxNum]</a>
 *
 * @param 	STRING 	$string 			:: The text string to be processed
 * @param 	STRING 	$yaction			:: Action to append the fax-num link to ; Default is: 'efax:' but can be for example: 'script.php?action=fax&number='
 * @param 	STRING	$ytarget			:: URL target ; default is '_blank' ; can be: '_self' or a specific window name: 'myWindow' ...
 *
 * @return 	STRING						:: The HTML processed text with FAX numbers replaced with real tags as links
 */
public static function text_faxnums($string, $yaction='efax:', $ytarget='_blank') {
	$string = (string) $string;
	$expr = SmartValidator::regex_stringvalidation_expression('fax', 'partial');
	$regex = $expr.'iu'; //insensitive, with /u modifier for unicode strings
	$string = preg_replace_callback($regex, function($matches) use ($yaction, $ytarget) { return '<a title="@eFax@" id="url_recognition" href="'.Smart::escape_html($yaction.rawurlencode(trim($matches[2]))).'" target="'.$ytarget.'">'.Smart::escape_html(Smart::text_cut_by_limit($matches[2], 75)).'</a>'; }, $string);
	return (string) $string;
} //END FUNCTION
//================================================================


//================================================================
/**
 * Parse Simple Notes :: '-----< yyyy-mm-dd hh:ii:ss >----- some note\nsome other line'
 *
 * @param STRING $ynotes			:: The Text or HTML to be processed
 * @param YES/NO $y_hide_times 		:: Show / Hide the time stamps
 * @param #SIZE $y_tblsize			:: HTML Table Size
 * @param #COLOR $ytxtcolor			:: HTML Table Color for Text
 * @param #COLOR $ycolor			:: HTML Table Row Color
 * @param #COLOR $ycolor_alt		:: HTML Table Row Alternate Color
 * @param #COLOR $ybrdcolor			:: HTML Table Border Color
 * @param #STYLE $y_style			:: HTML Extra Style
 *
 * @access 		private
 * @internal
 *
 * @return 	STRING					:: The HTML processed code
 */
public static function simple_notes($ynotes, $y_hide_times, $y_tblsize='100%', $ytxtcolor='#000000', $ycolor='#FFFFFF', $ycolor_alt='#FFFFFF', $ybrdcolor='#CCCCCC', $y_style=' style="overflow: auto; height:200px;"') {
	//--
	if(strpos((string)$ynotes, '-----<') === false) {
		return $tbl_start.'<tr><td bgcolor="'.$ycolor.'" valign="top"><font size="1">'.Smart::nl_2_br(Smart::escape_html($ynotes)).'</font></td></tr>'.$tbl_end ; // not compatible notes, so we not parse them
	} //end if
	//--
	$out = '';
	//--
	$tbl_start = '<table width="'.$y_tblsize.'" cellspacing="0" cellpadding="2" border="1" bordercolor="'.$ybrdcolor.'" style="border-style: solid; border-collapse: collapse;">'."\n";
	$tbl_end = '</table>';
	//--
	$tmp_shnotes_arr = (array) explode('-----<', (string)$ynotes);
	//--
	$i_alt=0;
	//--
	if(Smart::array_size($tmp_shnotes_arr) > 0) {
		//--
		$out .= '<!-- OVERFLOW START (S.NOTES) -->'.'<div title="#S.NOTES#"'.$y_style.'>'."\n";
		$out .= $tbl_start;
		//--
		for($i=0; $i<Smart::array_size($tmp_shnotes_arr); $i++) {
			//--
			$tmp_shnotes_arr[$i] = (string) trim((string)$tmp_shnotes_arr[$i]);
			//--
			if(Smart::striptags(str_replace('-----<', '', (string)$tmp_shnotes_arr[$i])) != '') {
				//--
				$tmp_expld = (array) explode('>-----', (string)$tmp_shnotes_arr[$i]);
				//--
				$tmp_meta_expl = (array) explode('|', (string)$tmp_expld[0]);
				$tmp_meta_date = trim((string)$tmp_meta_expl[0]);
				if(strlen(trim((string)$tmp_meta_expl[1])) > 0) {
					$tmp_metainfo = ' :: '.trim($tmp_meta_expl[1]);
				} else {
					$tmp_metainfo = '';
				} //end if else
				//--
				if(strlen(trim((string)$tmp_expld[1])) > 0) {
					//--
					$i_alt += 1;
					//-- alternate
					if($i_alt % 2) {
						$alt_color = $ycolor;
					} else {
						$alt_color = $ycolor_alt;
					} //end if else
					//--
					$out .= '<tr>'."\n";
					$out .= '<td bgcolor="'.$alt_color.'" valign="top">'."\n";
					//--
					if((string)$y_hide_times != 'yes') {
						$out .= '<div align="right" title="'.Smart::escape_html('#'.$i_alt.'.'.$tmp_metainfo).'"><font size="1" color="'.$ytxtcolor.'"><b>'.Smart::escape_html($tmp_meta_date).'</b></font></div><font size="1" color="'.$ytxtcolor.'">'.Smart::nl_2_br(Smart::escape_html(trim($tmp_expld[1]))).'</font>';
					} else {
						$out .= '<div title="'.Smart::escape_html('#'.$i_alt.'. '.$tmp_meta_date.$tmp_metainfo).'"><font size="1" color="'.$ytxtcolor.'">'.Smart::nl_2_br(Smart::escape_html(trim($tmp_expld[1]))).'</font></div>';
					} //end if else
					//--
					$out .= '</td>'."\n";
					$out .= '</tr>'."\n";
					//--
				} //end if
				//--
			} //end if
			//--
		} //end for
		//--
		$out .= $tbl_end;
		$out .= '</div>'.'<!-- OVERFLOW END (S.NOTES) -->'."\n";
		//--
	} //end if
	//--
	if($i_alt <= 0) {
		$out = '';
	} //end if
	//--
	return $out ;
	//--
} //END FUNCTION
//================================================================


} //END CLASS


//=================================================================================
//================================================================================= CLASS END
//=================================================================================


//=================================================================================
//================================================================================= CLASS START
//=================================================================================


/**
 * Class: SmartValidator - provides misc validating methods.
 *
 * <code>
 * // Usage example:
 * SmartValidator::some_method_of_this_class(...);
 * </code>
 *
 * @usage       static object: Class::method() - This class provides only STATIC methods
 *
 * @access      PUBLIC
 * @depends     classes: Smart, SmartUnicode
 * @version     v.170927
 * @package     Base
 *
 */
final class SmartValidator {

	// ::


//=================================================================
/**
 * Regex Expressions for Text Parsing of: Numbers, IP addresses, Valid eMail Address with TLD domain, Phone (US), Unicode Text (UTF-8)
 *
 * @param 	ENUM 	$y_mode 			:: The Regex mode to be returned ; valid modes:
 * 											number-integer			:: number integer: as -10 or 10
 * 											number-decimal			:: number decimal: as -0.05 or 0.05
 * 											number-list-integer 	:: number, list integer: as 1;2;30 (numbers separed by semicolon=;)
 * 											number-list-decimal 	:: number, list decimal: as 1.0;2;30.44 (numbers separed by semicolon=;)
 *											ipv4 					:: IP (v4): 0.0.0.0 .. 255.255.255.255
 *											ipv6 					:: IP (v4): ::1 .. 2a00:1450:400c:c01::68 ...
 * 											email					:: eMail@address.tld ; MUST contain a TLD ; TLD can be 2 letters long as well as 3 or more
 * 											phone-us 				:: US phone numbers
 * 											utf8-text 				:: Unicode (UTF-8) Text
 *
 * @param 	ENUM 	$y_match 			:: Match Type: full / partial
 *
 * @return 	STRING						:: The Regex expression or empty if invalid mode is provided
 */
public static function regex_stringvalidation_expression($y_mode, $y_match='full') {
	//--
	switch((string)strtolower((string)$y_match)) {
		case 'full':
			$rxs = '^';
			$rxe = '$';
			break;
		case 'partial':
			$rxs = '';
			$rxe = '';
			break;
		default:
			Smart::raise_error(
				'INVALID match type in function '.__CLASS__.'::'.__FUNCTION__.'(): '.$y_match,
				'Validations Internal ERROR' // msg to display
			);
			die(''); 	// just in case
			return '+'; // just in case
	} //end switch
	//--
	switch(strtolower((string)$y_mode)) { // WARNING: Never use class modifiers like [:print:] with /u modifier as it fails with some versions of PHP / Regex / PCRE
		//--
		//== #EXTERNAL USE
		//--
		case 'number-integer': 										// strict validation
			$regex = '/'.$rxs.'(\-)?[0-9]+?'.$rxe.'/'; 				// before was: '/([0-9\-])+/' but was not good enough as a strict rule
			break;
		case 'number-decimal': 										// strict validation ; must match also integer values ; {{{SYNC-DETECT-PURE-NUMERIC-INT-OR-DECIMAL-VALUES}}}
			$regex = '/'.$rxs.'(\-)?[0-9]+(\.[0-9]+)?'.$rxe.'/'; 	// before was: '/([0-9\-\.])+$/' but was not good enough as a strict rule
			break;
		//--
		case 'number-list-integer': 								// flexible validation (since this is a list, it may contain any numbers and ;)
			$regex = '/'.$rxs.'([0-9\-\;])+'.$rxe.'/'; 				// example: 1;2;30
			break;
		case 'number-list-decimal': 								// flexible validation (since this is a list, it may contain any numbers and ;) ; must match also integer list values
			$regex = '/'.$rxs.'([0-9\-\.\;])+'.$rxe.'/'; 			// example: 1.0;2;30.44
			break;
		//--
		case 'ipv4':
			$regex = '/'.$rxs.'([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}'.$rxe.'/';
			break;
		case 'ipv6':
			$regex = '/'.$rxs.'s*((([0-9A-Fa-f]{1,4}\:){7}([0-9A-Fa-f]{1,4}|\:))|(([0-9A-Fa-f]{1,4}\:){6}(\:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]d|1dd|[1-9]?d)(.(25[0-5]|2[0-4]d|1dd|[1-9]?d)){3})|\:))|(([0-9A-Fa-f]{1,4}\:){5}(((\:[0-9A-Fa-f]{1,4}){1,2})|\:((25[0-5]|2[0-4]d|1dd|[1-9]?d)(.(25[0-5]|2[0-4]d|1dd|[1-9]?d)){3})|\:))|(([0-9A-Fa-f]{1,4}\:){4}(((\:[0-9A-Fa-f]{1,4}){1,3})|((\:[0-9A-Fa-f]{1,4})?\:((25[0-5]|2[0-4]d|1dd|[1-9]?d)(.(25[0-5]|2[0-4]d|1dd|[1-9]?d)){3}))|\:))|(([0-9A-Fa-f]{1,4}\:){3}(((\:[0-9A-Fa-f]{1,4}){1,4})|((\:[0-9A-Fa-f]{1,4}){0,2}\:((25[0-5]|2[0-4]d|1dd|[1-9]?d)(.(25[0-5]|2[0-4]d|1dd|[1-9]?d)){3}))|\:))|(([0-9A-Fa-f]{1,4}\:){2}(((\:[0-9A-Fa-f]{1,4}){1,5})|((\:[0-9A-Fa-f]{1,4}){0,3}\:((25[0-5]|2[0-4]d|1dd|[1-9]?d)(.(25[0-5]|2[0-4]d|1dd|[1-9]?d)){3}))|\:))|(([0-9A-Fa-f]{1,4}\:){1}(((\:[0-9A-Fa-f]{1,4}){1,6})|((\:[0-9A-Fa-f]{1,4}){0,4}\:((25[0-5]|2[0-4]d|1dd|[1-9]?d)(.(25[0-5]|2[0-4]d|1dd|[1-9]?d)){3}))|\:))|(\:(((\:[0-9A-Fa-f]{1,4}){1,7})|((\:[0-9A-Fa-f]{1,4}){0,5}\:((25[0-5]|2[0-4]d|1dd|[1-9]?d)(.(25[0-5]|2[0-4]d|1dd|[1-9]?d)){3}))|\:)))(%.+)?s*'.$rxe.'/';
			break;
		case 'macaddr':
			$regex = '/'.$rxs.'([0-9a-fA-F][0-9a-fA-F]\:){5}([0-9a-fA-F][0-9a-fA-F])|([0-9a-fA-F][0-9a-fA-F]\-){5}([0-9a-fA-F][0-9a-fA-F])'.$rxe.'/';
			break;
		//--
		case 'url':
			$regex = '/'.$rxs.'(http|https)(:\/\/)([^\s<>\(\)\|]*)'.$rxe.'/'; // url recognition in a text / html code :: fixed in html <>
			break;
		case 'domain':
			$regex = '/'.$rxs.'([a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?\.)+[a-z]{2,24}'.$rxe.'/'; // internet (subdomain.)domain.name
			break;
		case 'email':
			$regex = '/'.$rxs.'([_a-z0-9\-\.]){1,63}@'.'[a-z0-9\-\.]{3,63}'.$rxe.'/'; // internet email@(subdomain.)domain.name :: [_a-z0-9\-\.]*+@+[_a-z0-9\-\.]*
			break;
		//--
		case 'fax':
			$regex = '/'.$rxs.'(~)([0-9\-\+\.\(\)][^~]*)(~)'.$rxe.'/'; // fax number recognition in a text / html code (must stay between ~)
			break;
		//--
		//== #ERROR: INVALID
		//--
		default:
			Smart::raise_error(
				'INVALID mode in function '.__CLASS__.'::'.__FUNCTION__.'(): '.$y_mode,
				'Validations Internal ERROR' // msg to display
			);
			die(''); 	// just in case
			return '+'; // just in case
		//--
		//== #END
		//--
	} //end switch
	//--
	return (string) $regex;
	//--
} //END FUNCTION
//=================================================================


//=================================================================
/**
 * Regex Segment to build Regex Expressions (Internal Use Only)
 *
 * @access 		private
 * @internal
 *
 * @param 	ENUM 	$y_mode 			:: The Regex mode to be returned (see in function)
 *
 * @return 	STRING						:: The Regex expression or empty if invalid mode is provided
 */
public static function regex_stringvalidation_segment($y_mode) {
	//--
	switch(strtolower((string)$y_mode)) { // WARNING: Never use class modifiers like [:print:] with /u modifier as it fails with some versions of PHP / Regex / PCRE
		//--
		//== #INTERNAL USE ONLY
		//-- {{{SYNC-HTML-TAGS-REGEX}}} ; expression delimiter must be # (not / or others ...)
		case 'tag-name':
			$regex = 'a-z0-9\-\:'; // regex expr: the allowed characters in tag names (just for open tags ... the end tags will add / and space
			break;
		case 'tag-start':
			$regex = '\<\s*?'; // regex expr: tag start
			break;
		case 'tag-end-start':
			$regex = '\<\s*?/\s*?'; // regex expr: end tag start
			break;
		case 'tag-simple-end':
			$regex = '\s*?\>'; // regex expr: tag end without attributes
			break;
		case 'tag-complex-end':
			$regex = '\s+[^>]*?\>'; // regex expr: tag end with attributes or / (it needs at least one space after tag name)
			break;
		//--
		//== #ERROR: INVALID
		//--
		default:
			$regex = '+';
			Smart::raise_error(
				'INVALID mode in function '.__CLASS__.'::'.__FUNCTION__.'(): '.$y_mode,
				'Segment Validations Internal ERROR' // msg to display
			);
			die(''); // just in case
			return '';
		//--
		//== #END
		//--
	} //end switch
	//--
	return (string) $regex;
	//--
} //END FUNCTION
//=================================================================


//=================================================================
/**
 * Validate a string using SmartValidator::regex_stringvalidation_expression(), Full Match
 *
 * @param 	STRING		$y_string			:: The String to be validated
 * @param 	ENUM 		$y_mode 			:: The Regex mode to use for validation ; see reference for SmartValidator::regex_stringvalidation_expression()
 *
 * @return 	BOOLEAN							:: TRUE if validated by regex ; FALSE if not validated
 */
public static function validate_string($y_string, $y_mode) {
	//--
	$regex = self::regex_stringvalidation_expression((string)$y_mode, 'full');
	//--
	if(preg_match((string)$regex, (string)$y_string)) {
		return true;
	} else {
		return false;
	} //end if else
	//--
} //END FUNCTION
//=================================================================


//================================================================
/**
 * Validate if a string or number is Integer or Decimal (positive / negative)
 * This will not check if the number is finite or overflowing !!
 *
 * @param 	STRING		$val				:: The string or number to be validated
 * @return 	BOOL							:: TRUE if Integer or Decimal (positive / negative) ; FALSE if not
 */
public static function validate_numeric_integer_or_decimal_values($val) { // {{{SYNC-DETECT-PURE-NUMERIC-INT-OR-DECIMAL-VALUES}}}
	//--
	$val = (string) $val; // do not use TRIM as it may strip out null or weird characters that may inject security issues if not trimmed outside (MUST VALIDATE THE REAL STRING !!!)
	//--
	$regex_decimal = (string) self::regex_stringvalidation_expression('number-decimal', 'full');
	//--
	if(((string)$val != '') AND (is_numeric($val)) AND (preg_match((string)$regex_decimal, (string)$val))) { // detect numbers: 0..9 - .
		return true; // VALID
	} else {
		return false; // NOT VALID
	} //end if else
	//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Validate if a string or number is VALID numeric (finite, not overflowing, match precision, not Expressions like 1.3e3 ; may contain only -0123456789.)
 *
 * @param 	STRING		$val				:: The string or number to be validated
 * @return 	BOOL							:: TRUE if match condition ; FALSE if not
 */
public static function validate_numeric_pure_valid_values($val) {
	//--
	if((self::validate_numeric_integer_or_decimal_values($val)) AND (!is_nan($val)) AND (!is_infinite($val)) AND ((string)$val == (string)(float)$val)) {
		return true;
	} else {
		return false;
	} //end if
	//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Validate if a string or number is VALID integer (finite, not overflowing, match precision and max/min integer, not Expressions like 1.3e3 ; may contain only -0123456789)
 *
 * @param 	STRING		$val				:: The string or number to be validated
 * @return 	BOOL							:: TRUE if match condition ; FALSE if not
 */
public static function validate_integer_pure_valid_values($val) {
	//--
	if((self::validate_numeric_pure_valid_values($val)) AND ($val >= PHP_INT_MIN) AND ($val <= PHP_INT_MAX)) {
		return true;
	} else {
		return false;
	} //end if
	//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Detect HTML or XML code contain tags (if does not contain tags is not html or xml code ...)
 *
 * @param STRING 	$y_html_or_xml_code		:: The String to be tested
 *
 * @return BOOLEAN 							:: TRUE (if XML or HTML tags are detected) or FALSE if not
 */
public static function validate_html_or_xml_code($y_html_or_xml_code) {
	//-- enforce string
	$y_html_or_xml_code = (string) trim((string)$y_html_or_xml_code);
	//-- regex expr
	$expr_tag_name 			= self::regex_stringvalidation_segment('tag-name');
	$expr_tag_start 		= self::regex_stringvalidation_segment('tag-start');
	$expr_tag_end_start 	= self::regex_stringvalidation_segment('tag-end-start');
	$expr_tag_simple_end 	= self::regex_stringvalidation_segment('tag-simple-end');
	$expr_tag_complex_end 	= self::regex_stringvalidation_segment('tag-complex-end');
	//-- {{{SYNC-HTML-TAGS-REGEX}}}
	$regex_part_tag_name 	= '['.$expr_tag_name.']+'; // regex syntax: tag name def
	//-- build regex syntax
	$regex_match_tag = '#'.$expr_tag_start.$regex_part_tag_name.$expr_tag_simple_end.'|'.$expr_tag_start.$regex_part_tag_name.$expr_tag_complex_end.'#si';
	//-- evaluate
	//if(((string)$y_html_or_xml_code != '') AND (strpos((string)$y_html_or_xml_code, '<') !== false) AND (strpos((string)$y_html_or_xml_code, '>') !== false) AND ((string)$y_html_or_xml_code != (string)strip_tags((string)$y_html_or_xml_code))) {
	if(((string)$y_html_or_xml_code != '') AND (strpos((string)$y_html_or_xml_code, '<') !== false) AND (strpos((string)$y_html_or_xml_code, '>') !== false) AND (preg_match((string)$regex_match_tag, (string)$y_html_or_xml_code))) {
		$out = true;
	} else {
		$out = false;
	} //end if else
	//-- return
	return (bool) $out;
	//--
} //END FUNCTION
//================================================================


//================================================================ Validate an IP Address
/**
 * Validate and Filter an IP Address
 *
 * @param 	STRING		$ip					:: The IP Address to be validated
 *
 * @return 	STRING							:: The IP address if valid (as string) or an empty string if Invalid
 */
public static function validate_filter_ip_address($ip) {
	//--
	$ip = @filter_var((string)$ip, FILTER_VALIDATE_IP);
	//--
	if($ip === false) {
		$ip = '';
	} //end if
	//--
	return (string) $ip;
	//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Validate The Mime Disposition
 *
 * @param 	STRING		$y_disp				:: The Mime Disposition ; can be: inline / attachment / attachment; filename="somefile.pdf"
 * @return 	STRING							:: The validated Mime Disposition
 */
public static function validate_mime_disposition($y_disp) {
	//--
	$y_disp = (string) trim((string)$y_disp);
	//--
	if((string)$y_disp == '') {
		return '';
	} //end if
	//--
	if(preg_match('/^[[:print:]]+$/', $y_disp)) { // mime types are only ISO-8859-1
		$disp = $y_disp;
	} else {
		$disp = '';
	} //end if
	//--
	return (string) $disp;
	//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Validate The Mime Type
 *
 * @param 	STRING		$y_type				:: The Mime Type ; Ex: image/png
 * @return 	STRING							:: The validated Mime Type
 */
public static function validate_mime_type($y_type) {
	//--
	$y_type = (string) strtolower(trim((string)$y_type));
	//--
	if((string)$y_type == '') {
		return '';
	} //end if
	//--
	if(preg_match('/^[[:graph:]]+$/', $y_type)) { // mime types are only ISO-8859-1
		$type = $y_type;
	} else {
		$type = '';
	} //end if
	//--
	return (string) $type;
	//--
} //END FUNCTION
//================================================================


} //END CLASS

//=================================================================================
//================================================================================= CLASS END
//=================================================================================


//end of php code
?>