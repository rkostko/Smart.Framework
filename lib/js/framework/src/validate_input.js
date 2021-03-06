
// [LIB - SmartFramework / JS / Validate Input (Fields)]
// (c) 2006-2018 unix-world.org - all rights reserved
// v.3.7.7 r.2018.10.19 / smart.framework.v.3.7

// DEPENDS: SmartJS_CoreUtils

//==================================================================
//==================================================================

//================== [NO:evcode]

//=======================================
// CLASS :: Validate Input (Fields)
//=======================================

// added support for Integer Numbers
// added support for Number of Decimals to Place (0..4)

var SmartJS_FieldControl = new function() { // START CLASS :: v.170831

// :: static


// Validate Input Field as Integer Number
this.validate_Field_Integer = function(yObjInputField, yAllowNegatives) {
	//--
	var tmp_Value = '';
	tmp_Value = SmartJS_CoreUtils.format_number_int(yObjInputField.value, yAllowNegatives);
	tmp_Value = String(tmp_Value);
	//--
	yObjInputField.value = tmp_Value;
	//--
} //END FUNCTION


// Validate Input Field as Decimal(1..4) Number
this.validate_Field_Decimal = function(yObjInputField, yDecimalsDigits, yAllowNegatives, yAddThousandsSeparator) {
	//-- inits
	var tmp_Value = '';
	if(yObjInputField.value == '') {
		tmp_Value = '0';
	} else {
		tmp_Value = String(yObjInputField.value);
	} //end if
	//-- remove all spaces
	tmp_Value = String(SmartJS_CoreUtils.stringReplaceAll(' ', '', tmp_Value));
	//-- detect and trick the decimal and thousands separators
	var regex_dot = /\./g;
	var have_dot = regex_dot.test(tmp_Value);
	if(have_dot === true) {
		tmp_Value = SmartJS_CoreUtils.stringReplaceAll(',', '', tmp_Value); // remove thousands separator (comma) because there is already a dot there as decimal separator there (dot)
	} else {
		tmp_Value = SmartJS_CoreUtils.stringReplaceAll(',', '.', tmp_Value); // replace the wrong decimal separator (comma) with the real decimal separator (dot)
	} //end if
	//-- real format the value as decimal
	tmp_Value = SmartJS_CoreUtils.format_number_dec(tmp_Value, yDecimalsDigits, yAllowNegatives);
	//--
	if(yAddThousandsSeparator === true) {
		yObjInputField.value = String(SmartJS_CoreUtils.add_number_ThousandsSeparator(String(tmp_Value)));
	} else {
		yObjInputField.value = String(tmp_Value);
	} //end if else
	//--
} //END FUNCTION

} //END CLASS

//==================================================================
//==================================================================

// #END
